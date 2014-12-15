<?php

namespace Simplon\Router;

use Simplon\Error\ErrorResponse;

/**
 * Router
 * @package Simplon\Router
 * @author Tino Ehrich (tino@bigpun.me)
 */
class Router
{
    /**
     * @var string
     */
    private static $route;

    /**
     * @var string
     */
    private static $request;

    /**
     * @param array $routes
     * @param null $requestedRoute
     * @param null $dispatcher
     *
     * @return string
     * @throws RouterException
     */
    public static function observe(array $routes, $requestedRoute = null, $dispatcher = null)
    {
        self::setup($requestedRoute);

        // loop through all defined routes
        foreach ($routes as $route)
        {
            // handle route before passing it on to the controller
            if ($dispatcher instanceof \Closure)
            {
                self::$route = $dispatcher(self::$route);
            }

            // handle controller matching
            if (preg_match_all('#' . $route['pattern'] . '/*#i', self::$route, $match, PREG_SET_ORDER))
            {
                // handle request method restrictions
                if (isset($route['request']) && strpos(strtoupper($route['request']), self::$request) === false)
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
                return self::handleRoute($route, $params);
            }
        }

        throw new RouterException('Failed to match any route');
    }

    /**
     * @param null $route
     *
     * @return bool
     */
    private static function setup($route = null)
    {
        if ($route === null)
        {
            $route = $_SERVER['PATH_INFO'];
        }

        // set route
        self::$route = rtrim($route, '/');

        // set request method
        self::$request = strtoupper($_SERVER['REQUEST_METHOD']);

        return true;
    }

    /**
     * @param array $route
     * @param array $params
     *
     * @return string|ErrorResponse
     * @throws RouterException
     */
    private static function handleRoute(array $route, array $params = [])
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

        throw new RouterException('A route requires either "controller" or a "callback"');
    }
}