<?php 

namespace Rogovskyyy\Router;

require_once 'vendor/autoload.php';

use Exception;

class Router 
{
    private static $routemap = [];
    private static $uri = '';
    private static $method = '';

    public static function get($args, $callback) : void { self::add_route($args, 'GET', $callback); }
    public static function post($args, $callback) : void { self::add_route($args, 'POST', $callback); }
    public static function put($args, $callback) : void { self::add_route($args, 'PUT', $callback); }
    public static function delete($args, $callback) : void { self::add_route($args, 'DELETE', $callback); }

    public static function ignite() : void { 
        self::$uri = $_SERVER['REQUEST_URI'];
        self::$method = $_SERVER['REQUEST_METHOD'];
        self::verify_route(self::split($_SERVER['REQUEST_URI']));
     }

    private static function redirect($http_code, $context) : void
    {   
        require_once $context;
        http_response_code($http_code);
    }

    private static function add_route($args, $method, $callback) : void {
        if($method != 'GET') {
            foreach(explode('/', $args) as $value) {
                if(preg_match_all('#{{1}:(.*?)\}{1}#', $value) != 0) {
                    throw new Exception("[Router] Url binding is not supported to be used with POST, PUT & DELETE - use GET instead");
                }
            } 
        }

        $args = self::split($args);
        self::$routemap[$args]['method'] = $method;
        self::$routemap[$args]['callback'] = $callback;
    }

    private static function split($uri) : string { 
        $args = array_values(array_filter(explode('/', $uri)));
        $url = '';
        for($i = 0; $i <= count($args) - 1; $i++) {
            $url .= $args[$i].'/';
        }
        return $url;
    }

    private static function verify_route($routemap) {
        $uri = self::split(self::$uri);

        foreach(self::$routemap as $key => $value) {
            $temp = preg_replace('#{{1}:(.*?)\}{1}#', '(\w+)', $key);
            $matches  = preg_match("#^$temp$#", $uri);
            if($matches == 1) {
                if(self::$method != $value['method']) {
                    //throw new Exception("[Router] This method is not supported for this route");
                    return self::redirect(404);
                }

                $inside = preg_match_all('#{{1}:(.*?)\}{1}#', $key);
                
                $temporary_key = explode('/', $key);
                $temporary_uri = explode('/', $uri);

                $temporary_binds = [];

                for($i = 0; $i <= count($temporary_key) - 1; $i++) {
                    if(preg_match_all('#{{1}:(.*?)\}{1}#', $temporary_key[$i])) {
                        array_push($temporary_binds, $temporary_uri[$i]);
                    }
                }
                return call_user_func_array($value['callback'], $temporary_binds);
            }
        }

        self::redirect(404);
    }



}

?>