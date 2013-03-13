<?php

  namespace Simplon\Router;

  use Simplon\Border\Request;

  class Router
  {
    /** @var Router */
    private static $_instance;

    /** @var array */
    private $_routes = [];

    /** @var array */
    private $_wildcardTypes = [
      'num'    => '([0-9]+)',
      'alpha'  => '([0-9A-Za-z_\-]+)',
      'hex'    => '([0-9A-Fa-f]+)',
      'base64' => '([0-9A-Za-z+/=.\-_]+)',
      'query'  => '\?(.*?)',
      'all'    => '*(.*?)',
    ];

    /** @var bool */
    private $_routingViaQueryString = FALSE;

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
     * @return string
     */
    protected function _getRequestedRoute()
    {
      // return path info
      if($this->_isRoutingViaQueryString() === FALSE)
      {
        return $this
          ->getRequestInstance()
          ->getPathInfo();
      }

      // return query string
      return $this
        ->getRequestInstance()
        ->getQueryString();
    }

    // ##########################################

    /**
     * @return bool
     */
    protected function _isRoutingViaQueryString()
    {
      return $this->_routingViaQueryString;
    }

    // ##########################################

    /**
     * @param bool $use
     * @return $this
     */
    public function enableRoutingViaQueryString($use)
    {
      $this->_routingViaQueryString = $use !== TRUE ? FALSE : TRUE;

      return $this;
    }

    // ##########################################

    /**
     * @return $this
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

      // compile route to regex
      $regex = $this->_compileRoute($route);

      // set route
      $this->_setRoute($method, $regex, $callback);

      return $this;
    }

    // ##########################################

    /**
     * @param $route
     * @return string
     */
    protected function _compileRoute($route)
    {
      $wildcardTypes = $this->_getWildcardTypes();

      foreach($wildcardTypes as $type => $regexp)
      {
        $route = str_replace(':' . $type, $regexp, $route);
      }

      // make sure we got a leading slash
      $route = '/' . ltrim($route, '/');

      // add final slash
      $route .= '/*';

      // escape fwd slash
      $route = str_replace('/', '\/', $route);

      return "^$route$";
    }

    // ##########################################

    /**
     * @param $method
     * @param $regex
     * @param $callback
     * @return $this
     */
    protected function _setRoute($method, $regex, $callback)
    {
      $_routes = $this->_getRoutes();

      $_routes[] = [
        'method'   => $method,
        'regex'    => $regex,
        'callback' => $callback,
      ];

      $this->_routes = $_routes;

      return $this;
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

      $requestRoute = $this->_getRequestedRoute();

      foreach($_routes as $route)
      {
        $method = $route['method'];
        $regex = $route['regex'];
        $callback = $route['callback'];

        if(strpos($method, $requestMethod) !== FALSE && preg_match('/' . $regex . '/ui', $requestRoute, $matched))
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