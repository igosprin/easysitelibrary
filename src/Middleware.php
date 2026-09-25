<?php
namespace Easysite\Library;

/**
 * Base class for request middleware. Registry in config/middleware.php
 * ('aliases' => name => class, 'global' => names run on every request).
 * A route lists its own in config/routs.php:
 *
 *   'middleware' => ['auth' => ['role' => 'user']]   // name => $params for handle()
 *
 * Middleware runs after the controller is created and before its action:
 * global ones first (with $params = null), then the route's. The same middleware
 * runs at most once per request, with the route's $params if it has any.
 */
abstract class Middleware
{
    /** Db wrapper (all connections) — the middleware picks the connection alias itself. */
    public Db $dbRepository;

    /**
     * @param Controller $controller controller that is about to run the action
     * @param mixed $params value of this middleware's key in the matched route; null if the route has no such key
     * @return bool false — request is finished (redirect, denial), the action is NOT called
     */
    abstract public function handle(Controller $controller, mixed $params): bool;

    /** Sends the redirect and finishes the request: return the result from handle(). */
    protected function redirect(string $url): bool
    {
        header('Location: ' . $url);

        return false;
    }
}
