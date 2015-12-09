<?php

namespace Simplon\Router;

/**
 * Router
 * @package Simplon\Router
 * @author  Tino Ehrich (tino@bigpun.me)
 */
class Router
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @var \Closure[]
     */
    private $filters;

    /**
     * @var string
     */
    private $requestedRoute;

    /**
     * @var string
     */
    private $request;

    /**
     * @param array $routes
     * @param string|null $requestedRoute
     */
    public function __construct(array $routes, $requestedRoute = null)
    {
        // set all available routes
        $this->routes = $routes;

        // set route
        $this->requestedRoute = $requestedRoute ?: $_SERVER['PATH_INFO'];

        // clean route
        $this->requestedRoute = rtrim($this->requestedRoute, '/');

        // set request method
        $this->request = strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @param \Closure $filter
     *
     * @return Router
     */
    public function addFilter(\Closure $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @return string
     * @throws RouterException
     */
    public function observe()
    {
        // loop through all defined routes
        foreach ($this->routes as $route)
        {
            // apply filter
            $requestedRoute = $this->applyFilters($this->requestedRoute);

            $route['pattern'] = preg_replace('/(:\w+)/i', '*(\w+)*', $route['pattern']);

            // handle controller matching
            if (preg_match_all('|' . str_replace('|', '\|', $route['pattern']) . '/*|i', $requestedRoute, $match, PREG_SET_ORDER))
            {
                // if home pattern the requested route should be empty too
                if (empty($route['pattern']) === true && empty($requestedRoute) === false)
                {
                    continue;
                }

                // handle request method restrictions
                if (isset($route['request']) && strpos(strtoupper($route['request']), $this->request) === false)
                {
                    continue;
                }

                // prepare params
                $params = [];

                if (isset($match[0][1]))
                {
                    // remove matched string
                    unset($match[0][0]);

                    // set params
                    $params = $match[0];
                }

                // dispatch
                return $this->handleRoute($route, $params);
            }
        }

        throw new RouterException('Failed to match any route');
    }

    /**
     * @param array $route
     * @param array $params
     *
     * @return string
     * @throws RouterException
     */
    private function handleRoute(array $route, array $params = [])
    {
        // handling via class
        if (isset($route['controller']))
        {
            list($controller, $method) = explode('::', $route['controller']);

            return call_user_func_array([(new $controller), $method], $params);
        }

        // handling via closure
        elseif (isset($route['callback']))
        {
            return call_user_func_array($route['callback'], $params);
        }

        throw new RouterException('A route requires either $router["controller"] or $router["callback"]');
    }

    /**
     * @param $route
     *
     * @return string
     */
    private function applyFilters($route)
    {
        if (empty($this->filters) === false)
        {
            foreach ($this->filters as $filter)
            {
                $route = $filter($route);
            }
        }

        return $route;
    }
}