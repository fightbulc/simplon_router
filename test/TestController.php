<?php

class TestController
{
    public function fooAction($param = null)
    {
        return 'Hello ' . $param;
    }
}