<?php 

namespace Rogovskyyy\Router;

use Exception;

class Router 
{
    /**
     * Variable containing all available routes
     */

    private static $routemap = [];

    /**
    * Explode array of input and merge into one correct path
    *
    * @param uri Input array (path)
    *
    * @return url url
    *
    */

    private static function split($uri) {

        $args = explode('/', $uri);        
        $args = array_values(array_filter($args));
        $url = '';
        for($i = 0; $i <= count($args) - 1; $i++)
        {
            $url .= $args[$i].'/';
        }
        return $url;
    }

    /**
     * Add route to routemap as one of items
     * 
     * @param args Arguments (entire path)
     * @param method HTTP Method (GET, POST, PUT, DELETE)
     * @param callback Callback function e.g. anonymous function, static method in class
     *  
     */

    private static function add_route($args, $method, $callback) {
        
        $args = self::split($args);
        self::$routemap[$args]['method'] = $method;
        self::$routemap[$args]['callback'] = $callback;
    }

    /**
     * Checks if route you're trying to reach is available
     * 
     * @param uri contains path
     *
     */

    private static function exist_route($uri) {

        foreach(self::$routemap as $key => $value)
        {
            if($key == $uri)
                return call_user_func($value['callback']);
        }
        throw new Exception('Router error - no existing path');
    }

    /**
     * Runs entire router and handle connections
     */

    public static function ignite() {

        self::exist_route(self::split($_SERVER['REQUEST_URI']));
    }


    /**
     * GET method
     * 
     * @param args containing full path 
     * @param callback containing callable function like anonymous function or static method in class
     */
    public static function get($args, $callback) {

        self::add_route($args, 'GET', $callback);
    }
}

?>