<?php

  namespace Simplon\Router;

  class Router
  {
    /** @var Router */
    private static $_instance;

    /** @var array */
    private $_routes = array();

    /** @var array */
    private $_wildcardTypes = array(
      'num'   => '([0-9]+)',
      'alpha' => '([0-9A-Za-z]+)',
      'hex'   => '([0-9A-Fa-f]+)',
      'all'   => '*(.*?)',
    );

    // ##########################################

    /**
     * For chaining below PHP 5.4
     *
     * @return Router
     */
    public static function init()
    {
      if(! isset(Router::$_instance))
      {
        Router::$_instance = new Router();
      }

      return Router::$_instance;
    }

    // ##########################################

    /**
     * @return Request
     */
    protected function getRequestInstance()
    {
      return Request::getInstance();
    }

    // ##########################################

    /**
     * @return array
     */
    protected function _getRoutes()
    {
      return $this->_routes;
    }

    // ##########################################

    /**
     * @return array
     */
    protected function _getWildcardTypes()
    {
      return $this->_wildcardTypes;
    }

    // ##########################################

    /**
     * @param $route
     * @param $callback
     * @return Router
     */
    protected function setRoute($route, $callback)
    {
      $_routes = $this->_getRoutes();
      $_routes[$route] = $callback;
      $this->_routes = $_routes;

      return $this;
    }

    // ##########################################

    /**
     * @return Router
     */
    public function addRoute()
    {
      // handle dynamic arguments
      $argsCount = func_num_args();
      $args = func_get_args();

      // if request method is passed
      if($argsCount == 3)
      {
        $method = strtolower($args[0]);
        $route = $args[1];
        $callback = $args[2];
      }

      // withouth request method
      else
      {
        $method = 'get';
        $route = $args[0];
        $callback = $args[1];
      }

      // compile route
      $route = $this->compileRoute($method, $route);

      // set route
      $this->setRoute($route, $callback);

      return $this;
    }

    // ##########################################

    /**
     * @param $method
     * @param $route
     * @return string
     */
    protected function compileRoute($method, $route)
    {
      $wildcardTypes = $this->_getWildcardTypes();

      foreach($wildcardTypes as $type => $regexp)
      {
        $route = str_replace(':' . $type, $regexp, $route);
      }

      // add final slash
      $route .= '/*';

      // escape fwd slash
      $route = str_replace('/', '\/', $route);

      return $method . "::^$route$";
    }

    // ##########################################

    /**
     * @return bool
     */
    protected function parseRoutes()
    {
      $_routes = $this->_getRoutes();

      $_wildcardTypes = $this->_getWildcardTypes();

      $requestMethod = $this
        ->getRequestInstance()
        ->getMethod();

      $requestRoute = $this
        ->getRequestInstance()
        ->getPathInfo();

      foreach($_routes as $route => $callback)
      {
        list($method, $regex) = explode('::', $route);

        if($method == $requestMethod && preg_match('/' . $regex . '/ui', $requestRoute, $matched))
        {
          // leave out the match
          array_shift($matched);

          // if :all wildcard, split result by "/"
          if(strpos($regex, $_wildcardTypes['all']) !== FALSE)
          {
            $matched = explode('/', $matched[0]);
          }

          // callback home with found params
          call_user_func_array($callback, $matched);

          // and we stop
          return TRUE;
        }
      }

      return FALSE;
    }

    // ##########################################

    /**
     * @return bool
     */
    public function run()
    {
      return $this->parseRoutes();
    }
  }