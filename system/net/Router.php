<?php
namespace system\net;

/**
 * The Router class is responsible for routing an HTTP request to
 * an assigned callback function. The Router tries to match the
 * requested URL against a series of URL patterns.
 */
class Router
{
    /**
     * Mapped routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Pointer to current route.
     *
     * @var int
     */
    protected $index = 0;

    /**
     * Case sensitive matching.
     *
     * @var boolean
     */
    public $caseSensitive = false;

    /**
     * Gets mapped routes.
     *
     * @return array Array of routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Clears all routes in the router.
     */
    public function clear()
    {
        $this->routes = [];
    }

    /**
     * Maps a URL pattern to a callback function.
     *
     * @param string $pattern URL pattern to match
     * @param callback $callback Callback function
     * @param boolean $passRoute Pass the matching route object to the callback
     */
    public function map($pattern, $callback, $passRoute = false)
    {
        $url = $pattern;
        $methods = ['*'];

        if (strpos($pattern, ' ') !== false) {
            list($method, $url) = explode(' ', trim($pattern), 2);

            $methods = explode('|', $method);
        }

        $this->routes[] = new Route($url, $callback, $methods, $passRoute);
    }

    /**
     * Routes the current request.
     *
     * @param Request $request Request object
     * @return Route Matching route
     */
    public function route(Request $request)
    {
        while ($route = $this->current()) {
            if ($route !== false && $route->matchMethod($request->method) && $route->matchUrl($request->url, $this->case_sensitive)) {
                return $route;
            }
            $this->next();
        }

        return false;
    }

    /**
     * Gets the current route.
     *
     * @return Route
     */
    public function current()
    {
        return isset($this->routes[$this->index]) ? $this->routes[$this->index] : false;
    }

    /**
     * Gets the next route.
     *
     * @return Route
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Reset to the first route.
     */
    public function reset()
    {
        $this->index = 0;
    }
}
