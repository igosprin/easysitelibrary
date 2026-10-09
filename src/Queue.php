<?php
namespace Easysite\Library;

use Easysite\Library\Interface\SqlRepositoryInterface;

/**
 * Generic DB-backed job queue: enqueue a job now, run it later (typically from a
 * cron command calling work() once a minute) instead of inline in a request —
 * for anything too slow or too rare to do synchronously, or that needs a manual
 * trigger with real logic behind it (e.g. an admin action).
 *
 * Knows nothing about what a job does — only the shape of the `jobs` table it
 * reads/writes, same relationship as Auth to `users`/`user_remember_tokens`: the
 * consuming app owns the table (its own schema.sql/migration).
 *
 *   CREATE TABLE jobs (
 *       id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *       type        VARCHAR(64) NOT NULL,
 *       payload     JSON NOT NULL,
 *       status      ENUM('pending','running','done','failed') NOT NULL DEFAULT 'pending',
 *       attempts    TINYINT UNSIGNED NOT NULL DEFAULT 0,
 *       error       TEXT NULL,
 *       run_after   DATETIME NOT NULL,
 *       created_at  DATETIME NOT NULL,
 *       updated_at  DATETIME NOT NULL,
 *       KEY idx_status_run_after (status, run_after)
 *   ) ENGINE=InnoDB;
 *
 * Handlers are Job subclasses, registered by the app in config/jobs.php
 * ('type' => class). work() claims due jobs one at a time via an atomic
 * UPDATE ... WHERE status = 'pending' (safe against two overlapping cron runs
 * picking up the same job) and dispatches each to its handler.
 *
 * status($id) reads a job's current row back (status/error/attempts/payload) — for
 * a caller (an admin page, a status-check endpoint, ...) to poll on a job it enqueued.
 */
class Queue
{
    public function __construct(private SqlRepositoryInterface $db) {}

    /** Puts a job in the queue. $runAfter — 'Y-m-d H:i:s', default now. */
    public function enqueue(string $type, array $payload, ?string $runAfter = null): int
    {
        $now = date('Y-m-d H:i:s');

        return (int) $this->db->insert('jobs', [
            'type'       => $type,
            'payload'    => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'status'     => 'pending',
            'attempts'   => 0,
            'error'      => null,
            'run_after'  => $runAfter ?? $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Claims and runs up to $limit due jobs. $registry — 'type' => Job subclass
     * (the app's config/jobs.php), $dbRepository is handed to each job as
     * Job::$dbRepository.
     *
     * @return array<int,array{id:int,type:string,status:'done'|'failed',error:?string}>
     */
    public function work(array $registry, Db $dbRepository, int $limit = 10): array
    {
        $results = [];

        foreach ($this->dueIds($limit) as $id) {
            if (!$this->claim($id)) {
                continue; // another worker already took it (overlapping cron run)
            }

            $job = $this->find($id);
            $type = $job['type'];

            try {
                if (!isset($registry[$type])) {
                    throw new \RuntimeException("Unknown job type: {$type}");
                }

                $handler = new $registry[$type]();
                if (!$handler instanceof Job) {
                    throw new \RuntimeException($registry[$type] . ' must extend ' . Job::class);
                }

                $handler->dbRepository = $dbRepository;
                $handler->handle(json_decode($job['payload'], true) ?? []);

                $this->markDone($id);
                $results[] = ['id' => $id, 'type' => $type, 'status' => 'done', 'error' => null];
            } catch (\Throwable $e) {
                $this->markFailed($id, $e->getMessage());
                $results[] = ['id' => $id, 'type' => $type, 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /** Full row for one job (`payload` decoded back to array), or null — no such job. */
    public function status(int $id): ?array
    {
        $job = $this->find($id);
        if (!$job) {
            return null;
        }

        $job['payload'] = json_decode($job['payload'], true) ?? [];

        return $job;
    }

    /** id of pending jobs whose run_after has come, oldest first. */
    private function dueIds(int $limit): array
    {
        return array_column($this->db->fetchAll(
            'SELECT id FROM jobs WHERE status = :s AND run_after <= :now ORDER BY id LIMIT ' . (int) $limit,
            ['s' => 'pending', 'now' => date('Y-m-d H:i:s')]
        ), 'id');
    }

    /** Atomically claims a job for this worker; false — someone else already took it. */
    private function claim(int $id): bool
    {
        $statement = $this->db->query(
            "UPDATE jobs SET status = 'running', attempts = attempts + 1, updated_at = :now WHERE id = :id AND status = 'pending'",
            ['id' => $id, 'now' => date('Y-m-d H:i:s')]
        );

        return $statement->rowCount() === 1;
    }

    private function find(int $id): ?array
    {
        return $this->db->getById('jobs', $id) ?: null;
    }

    private function markDone(int $id): void
    {
        $this->db->update('jobs', ['id' => $id], ['status' => 'done', 'error' => null, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    private function markFailed(int $id, string $error): void
    {
        $this->db->update('jobs', ['id' => $id], ['status' => 'failed', 'error' => $error, 'updated_at' => date('Y-m-d H:i:s')]);
    }
}
