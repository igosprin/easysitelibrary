<?php
namespace Easysite\Library;

/**
 * Base class for a queued job handler. Registry in the consuming app's config/jobs.php
 * ('type' => class); Queue::work() looks a due job's type up there, instantiates the
 * class and calls handle() with the job's decoded payload.
 */
abstract class Job
{
    /** Db wrapper (all connections) — the job picks the connection alias itself, same as Middleware/Command. */
    public Db $dbRepository;

    /** @throws \Throwable — Queue::work() catches it, stores the message on the job row, and moves on. */
    abstract public function handle(array $payload): void;
}
