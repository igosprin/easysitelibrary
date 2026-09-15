<?php
namespace Easysite\Library;

class ConsoleRoute
{
    private array $argv;

    function __construct(array $argv = [])
    {
        $this->argv = $argv;
    }

    /**
     * search command and action from argv
     *
     * php console.php teams:sync --competition=PL --season=2025
     */
    public function searchEvent(): array
    {
        $action = [
            'controller' => 'index',
            'action' => 'index',
            'params' => [],
        ];

        $command = $this->argv[1] ?? '';
        if (!empty($command)) {
            $parts = explode(':', $command, 2);
            $action['controller'] = $parts[0];
            $action['action'] = $parts[1] ?? 'index';
        }

        foreach (array_slice($this->argv, 2) as $arg) {
            if (!str_starts_with($arg, '--')) {
                continue;
            }
            $pair = substr($arg, 2);
            if (str_contains($pair, '=')) {
                [$key, $value] = explode('=', $pair, 2);
            } else {
                $key = $pair;
                $value = true;
            }
            $action['params'][$key] = $value;
        }

        return $action;
    }
}
