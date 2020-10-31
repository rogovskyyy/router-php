<?php 

namespace Rogovskyyy\Router;

require_once 'vendor/autoload.php';

use Exception;

class Router 
{
    private static $routemap = [];
    private static $handlermap = [];
    private static $user = [];

    public static function get($args, $callback) : void { self::add_route($args, 'GET', $callback); }
    public static function post($args, $callback) : void { self::add_route($args, 'POST', $callback); }
    public static function put($args, $callback) : void { self::add_route($args, 'PUT', $callback); }
    public static function delete($args, $callback) : void { self::add_route($args, 'DELETE', $callback); }

    public static function ignite() : void 
    { 
        self::$user = [
            'uri' => $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD']
        ];

        if(array_key_exists(404, self::$handlermap))
        {
            if(is_null(self::$handlermap[404]) || empty(self::$handlermap[404]))
                self::$handlermap[404] = 'error.php';
        }
        else 
        {
            self::$handlermap = [404 => 'error.php'];
        }
        

        self::verify_route(self::split($_SERVER['REQUEST_URI']));
    }

    private static function error($code) 
    {
        require_once self::$handlermap[$code];
        http_response_code($code);
    }

    public static function handler($http_code, $context) : void
    {   
        self::$handlermap[$http_code] = $context;
    }

    private static function add_route($args, $method, $callback) : void 
    {
        if($method != 'GET') 
        {
            foreach(explode('/', $args) as $value) 
            {
                if(preg_match_all('#{{1}:(.*?)\}{1}#', $value) != 0) 
                    throw new Exception("[Router] Url binding is not supported to be used with POST, PUT & DELETE - use GET instead");
            } 
        }

        self::$routemap[self::split($args)] = [
            'method' => $method,
            'callback' => $callback
        ];
    }

    private static function split($uri) : string 
    { 
        $args = array_values(array_filter(explode('/', $uri)));
        $url = '';
        for($i = 0; $i <= count($args) - 1; $i++) 
            $url .= $args[$i].'/';

        return $url;
    }

    private static function verify_route($routemap) 
    {
        $uri = self::split(self::$user['uri']);

        foreach(self::$routemap as $key => $value) 
        {
            $temp = preg_replace('#{{1}:(.*?)\}{1}#', '(\w+)', $key);
            $matches  = preg_match("#^$temp$#", $uri);
            if($matches == 1) 
            {
                if(self::$user['method'] != $value['method']) 
                    return self::error(404);

                $inside = preg_match_all('#{{1}:(.*?)\}{1}#', $key);
                
                $temporary_key = explode('/', $key);
                $temporary_uri = explode('/', $uri);

                $temporary_binds = [];

                for($i = 0; $i <= count($temporary_key) - 1; $i++) 
                {
                    if(preg_match_all('#{{1}:(.*?)\}{1}#', $temporary_key[$i]))
                        array_push($temporary_binds, $temporary_uri[$i]);
                }

                return call_user_func_array($value['callback'], $temporary_binds);
            }
        }

        self::error(404);
    }
}

?>