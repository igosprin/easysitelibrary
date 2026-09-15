<?php
namespace Easysite\Library;

use Throwable;

class ErrorHandler
{
    private static bool $cli = false;
    private static bool $handling = false;

    private const FATAL_LEVELS = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];

    public static function register(bool $cli = false): void
    {
        self::$cli = $cli;

        ini_set('display_errors', '0');
        ini_set('log_errors', '0'); // log_errors writes to error_log independently of set_error_handler's return value — we log ourselves via Log
        error_reporting(E_ALL);

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }
        // a failed Log write (e.g. permissions) itself raises a warning — don't let that
        // loop back into Log::error() and recurse.
        if (self::$handling) {
            return true;
        }
        self::$handling = true;
        Log::error("$message in $file:$line");
        self::$handling = false;
        return true;
    }

    public static function handleException(Throwable $e): void
    {
        Log::exception($e);
        self::respond($e->getMessage());
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], self::FATAL_LEVELS, true)) {
            Log::error("{$error['message']} in {$error['file']}:{$error['line']}");
            self::respond($error['message']);
        }
    }

    private static function respond(string $message): void
    {
        $debug = getenv('APP_DEBUG') === 'true';

        if (self::$cli) {
            fwrite(STDERR, ($debug ? $message : 'Internal error, see storage/logs') . PHP_EOL);
            exit(1);
        }

        if (!headers_sent()) {
            http_response_code(500);
        }
        echo $debug ? '<pre>' . htmlspecialchars($message) . '</pre>' : 'Internal Server Error';
        exit(1);
    }
}
