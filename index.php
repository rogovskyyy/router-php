<?php

    include 'src/Rogovskyyy/Router/Router.php';
    include 'src/Rogovskyyy/Router/test_class.php';
    use Rogovskyyy\Router\Router;

    Router::get('/', function() 
    { 
        print "Hello, World! - straight from Router!"; 
    });

    Router::get('/home', function()
    {
        print "test";
    });

    Router::get('/test', [Test::class, 'item']);

    Router::ignite();

?>
