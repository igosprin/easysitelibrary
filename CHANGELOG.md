# Changelog

All notable changes to `easysite/library` are documented here.

## [Unreleased]

### Added
- `Easysite\Library\ErrorHandler` — global `set_error_handler`/`set_exception_handler`/shutdown handler. Registered once from the application bootstrap (`index.php`, `console.php`); logs every error/exception via `Log` instead of letting PHP display it.
- `Log::error()`, `Log::info()`, `Log::exception()` — `Log::log()` no longer echoes HTML into the response; all levels now append to `storage/logs/{Y-m-d}.log` via `FileManager`.
- `Easysite\Library\Application\Console` — CLI counterpart to `Application\Http`. Dispatches `php console.php {command}:{action} --key=value` to `application/commands/{name}Command.php`, same file-convention as HTTP controllers.
- `Easysite\Library\ConsoleRoute` — parses `$argv` into `['controller','action','params']`, the CLI counterpart to `Route`.
- `Easysite\Library\Command` — base class for CLI commands (`dbRepository`, `_model`, `output()`/`error()`), the CLI counterpart to `Controller`.
- `FileInterface::appendFile()` / `FileSystems::appendFile()` — appends instead of overwriting (`createFile()` always overwrote; needed for the new file-based `Log`).

### Changed
- `Application\Http::getLoadError()` now renders `errorController::error404()` when the consuming application defines one, instead of a bare `echo $error; die('error');`.
- Minimum PHP requirement raised from `^8.0` to `^8.4`, matching the Docker image this package is currently developed/run against.

### Fixed
- `Instances::searchClass()` had an implicit-nullable parameter (`string $driver = null`). PHP 8.4 raises this as a deprecation at class-load time rather than call time. Combined with the new `ErrorHandler`, this caused a reentrant autoload of `FileManager` (the handler tried to log the deprecation via `FileManager` while `FileManager`'s own class hierarchy was still being loaded) — surfaced as a fatal `Class "Easysite\Library\Instance\FileManager" not found` on every CLI command. Fixed to `?string $driver = null`.
- `View\PageElement`'s properties (`$scripts`, `$styles`, `$title`, ...) were typed with no default, so any getter (`getScripts()`, ...) fatals with "must not be accessed before initialization" if the matching setter was never called for that request — e.g. a controller that renders a layout looping over `getScripts()` without itself calling `setScripts()` first (hit this via `errorController::error404()`, which never sets scripts). Given all defaults.

### Removed
- `ErrorController.php` — was declared with no namespace and extended a bare `Controller`, which doesn't exist outside `Easysite\Library\Controller`; not PSR-4 autoloadable from this package and never referenced anywhere, so it always fataled if loaded. The real 404 handler belongs to the consuming application (it renders app-specific view templates), not this engine package — see the application's own `application/controllers/errorController.php`.

### Notes
- This file only tracks the **engine** (this package, `src/`). Application-level changes that came with this work (`application/commands/`, `application/controllers/errorController.php`, `application/view/error/404.html`, `application/services/ScoreManagerService.php`, the project's own `console.php`/`index.php` entry points) live in the consuming project, not here.
