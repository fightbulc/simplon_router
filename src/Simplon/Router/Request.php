<?php

  namespace Simplon\Router;

  class Request
  {
    /** @var Request */
    private static $_instance;

    /** @var array */
    private $_data = array();

    // ##########################################

    /**
     * @return Request
     */
    public static function getInstance()
    {
      if(! isset(Request::$_instance))
      {
        Request::$_instance = new Request();
      }

      return Request::$_instance;
    }

    // ##########################################

    protected function __construct()
    {
      $this->readData();
    }

    // ##########################################

    protected function readData()
    {
      $this->_data = $_SERVER;

      if($this->getMethod() == 'post')
      {
        // get POST data
        if(! empty($_POST))
        {
          $this->setParams($_POST);
        }

        // check for JSON-RPC
        else
        {
          $this->readJsonRpc();
        }
      }
    }

    // ##########################################

    /**
     * @return bool
     */
    protected function readJsonRpc()
    {
      $json = file_get_contents('php://input');
      $data = json_decode($json, TRUE);

      if(isset($data['id']) && isset($data['method']) && isset($data['params']))
      {
        $this
          ->setByKey('isJsonRpc', TRUE)
          ->setByKey('jsonRpcId', $data['id'])
          ->setByKey('jsonRpcMethod', $data['method'])
          ->setByKey('jsonRpcParams', $data['params']);

        return TRUE;
      }

      $this->setByKey('isJsonRpc', FALSE);

      return FALSE;
    }

    // ##########################################

    /**
     * @param $key
     * @param $val
     * @return Request
     */
    protected function setByKey($key, $val)
    {
      $key = strtoupper($key);

      $this->_data[$key] = $val;

      return $this;
    }

    // ##########################################

    /**
     * @param $key
     * @return bool
     */
    protected function getByKey($key)
    {
      $key = strtoupper($key);

      if(! isset($this->_data[$key]))
      {
        return FALSE;
      }

      $value = $this->_data[$key];

      return ! is_array($value) ? strtolower($value) : $value;
    }

    // ##########################################

    public function getData()
    {
      return $this->_data;
    }

    // ##########################################

    public function getServerIp()
    {
      return $this->getByKey('server_addr');
    }

    // ##########################################

    public function getServerPort()
    {
      return $this->getByKey('server_port');
    }

    // ##########################################

    public function getProtocol()
    {
      return $this->getByKey('server_protocol');
    }

    // ##########################################

    public function getMethod()
    {
      return $this->getByKey('request_method');
    }

    // ##########################################

    public function getTime()
    {
      return $this->getByKey('request_time');
    }

    // ##########################################

    public function getScriptName()
    {
      return $this->getByKey('script_name');
    }

    // ##########################################

    public function getUri()
    {
      return $this->getByKey('request_uri');
    }

    // ##########################################

    public function getQueryString()
    {
      return $this->getByKey('query_string');
    }

    // ##########################################

    public function getHttpCacheControl()
    {
      return $this->getByKey('http_cache_control');
    }

    // ##########################################

    public function getHttpAccept()
    {
      return $this->getByKey('http_accept');
    }

    // ##########################################

    public function getHttpAcceptCharset()
    {
      return $this->getByKey('http_accept_charset');
    }

    // ##########################################

    public function getHttpAcceptEncoding()
    {
      return $this->getByKey('http_accept_encoding');
    }

    // ##########################################

    public function getHttpAcceptLanguage()
    {
      return $this->getByKey('http_accept_language');
    }

    // ##########################################

    public function getHttpConnection()
    {
      return $this->getByKey('http_connection');
    }

    // ##########################################

    public function getHttpHost()
    {
      return $this->getByKey('http_host');
    }

    // ##########################################

    public function getHttpReferer()
    {
      return $this->getByKey('http_referer');
    }

    // ##########################################

    public function getHttpUserAgent()
    {
      return $this->getByKey('http_user_agent');
    }

    // ##########################################

    public function getHttps()
    {
      return $this->getByKey('https');
    }

    // ##########################################

    public function getRemoteIp()
    {
      return $this->getByKey('remote_addr');
    }

    // ##########################################

    public function getRemotePort()
    {
      return $this->getByKey('remote_port');
    }

    // ##########################################

    public function getRemoteUser()
    {
      return $this->getByKey('remote_user');
    }

    // ##########################################

    public function getPathInfo()
    {
      return $this->getByKey('path_info');
    }

    // ##########################################

    public function isJsonRpc()
    {
      return $this->getByKey('isJsonRpc');
    }

    // ##########################################

    public function getJsonRpcId()
    {
      return $this->getByKey('jsonRpcId');
    }

    // ##########################################

    public function getJsonRpcMethod()
    {
      return $this->getByKey('jsonRpcMethod');
    }

    // ##########################################

    public function getJsonRpcParams()
    {
      return $this->getByKey('jsonRpcParams');
    }

    // ##########################################

    public function setParams($params)
    {
      $this->setByKey('params', $params);

      return $this;
    }

    // ##########################################

    public function getParams()
    {
      return $this->getByKey('params');
    }
  }
