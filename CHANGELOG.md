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
- `Easysite\Library\Auth` — login mechanics: session (`Session` facade) plus a separate remember-me cookie. The cookie holds `selector:validator`; only `sha256(validator)` is stored in `user_remember_tokens`, compared with `hash_equals()`, and the token is rotated on every restore. API: `login($userId, $remember)`, `logout()`, `check()`, `id()`, `user()`, `hasRole($role)` (hierarchy `user` < `superuser`). `login()` regenerates the session id (session fixation). Knows nothing about email/password — that stays in the application. Schema it relies on: `users(id, role)` and `user_remember_tokens`.
- `Easysite\Library\Middleware` — abstract base class for request middleware: `handle(Controller $controller, mixed $params): bool` (`false` finishes the request, the action is not called) and a `redirect()` helper. Registry in the application's `config/middleware.php` (`aliases` — name => class, `global` — names run on every request); a route lists its own via `'middleware' => [name => params]` in `config/routs.php`. Global middleware runs first (with `$params = null`), then the route's; the same middleware runs once, with the route's params. Unknown name throws `InvalidArgumentException`.
- Optional `'dir'` key in a route config — subdirectory of `application/controllers/` the controller is loaded from (`Http::getEventClass($className, $area)`); without it behaviour is unchanged.
- `Controller::$auth` (`?Auth`) and `PageElement::setAuthUser()` / `getAuthUser()` (+ `PageElementInterface::getAuthUser()`) — for middleware to hand the current user to controllers and the layout.
- `CacheInterface::clearAll()` / `CacheFile::clearAll()` (+ `@method` on the `Cache` facade) — recursively wipes everything under the cache root, keeping the root directory and re-creating its `.gitignore`.

### Changed
- `Route::searchEvent()` now merges the whole matched route config into the result (`array_merge`) at all six match points instead of copying only `controller`/`action`. Extra route keys (`middleware`, `dir`, ...) used to be silently dropped. Route matching itself is unchanged.
- `Application\Http::loadEvent()` runs the middleware pipeline after the controller is set up and before the action. The engine itself knows nothing about `Auth`, roles or connection aliases — that lives in the application's middleware.
- `SqlRepository`: with `debug => true` the `debugDumpParams()` output is buffered and printed once at shutdown instead of being echoed after every query. Echoing early started the response body, so any later `header()` / `setcookie()` (redirects, login cookies) silently failed with "headers already sent".
- All comments in the engine translated to English.
- `Application\Http::getLoadError()` now renders `errorController::error404()` when the consuming application defines one, instead of a bare `echo $error; die('error');`.
- Minimum PHP requirement raised from `^8.0` to `^8.4`, matching the Docker image this package is currently developed/run against.

### Fixed
- `Instances::searchClass()` had an implicit-nullable parameter (`string $driver = null`). PHP 8.4 raises this as a deprecation at class-load time rather than call time. Combined with the new `ErrorHandler`, this caused a reentrant autoload of `FileManager` (the handler tried to log the deprecation via `FileManager` while `FileManager`'s own class hierarchy was still being loaded) — surfaced as a fatal `Class "Easysite\Library\Instance\FileManager" not found` on every CLI command. Fixed to `?string $driver = null`.
- `View\PageElement`'s properties (`$scripts`, `$styles`, `$title`, ...) were typed with no default, so any getter (`getScripts()`, ...) fatals with "must not be accessed before initialization" if the matching setter was never called for that request — e.g. a controller that renders a layout looping over `getScripts()` without itself calling `setScripts()` first (hit this via `errorController::error404()`, which never sets scripts). Given all defaults.

- `CacheFile::clear($key)` was an empty stub (calls succeeded but deleted nothing, so callers had to fake invalidation with `set($key, null, 1)`). It now removes the cache and settings files, and does nothing for a missing key.
- `Helpers::сamelCase()` had a Cyrillic `с` in its name (definition and its single call site); renamed to `camelCase()`. Two docblocks (`FileInterface`, `DirectoryInterface`) had Cyrillic `С` letters in "Create"/"Check" — fixed.

### Removed
- `ErrorController.php` — was declared with no namespace and extended a bare `Controller`, which doesn't exist outside `Easysite\Library\Controller`; not PSR-4 autoloadable from this package and never referenced anywhere, so it always fataled if loaded. The real 404 handler belongs to the consuming application (it renders app-specific view templates), not this engine package — see the application's own `application/controllers/errorController.php`.

### Notes
- This file only tracks the **engine** (this package, `src/`). Application-level changes that came with this work (`application/commands/`, `application/controllers/errorController.php`, `application/view/error/404.html`, `application/services/ScoreManagerService.php`, the project's own `console.php`/`index.php` entry points) live in the consuming project, not here.
- Same for the auth/middleware work: `config/middleware.php`, `application/middleware/AuthMiddleware.php` (registers `Auth` on `$controller->auth` and the navbar user, enforces `['role' => ...]`), `UserModel`, the login/register/logout controllers and views, and the `users.role` / `user_remember_tokens` schema are in the consuming project.
