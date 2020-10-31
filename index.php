<?php

require_once 'vendor/autoload.php';

use Rogovskyyy\Router\Router;
use Rogovskyyy\Router\Test\TestClass;

Router::get('/', fn() => printf('Hello from Router!'));

Router::get('/post', fn() => print "dldokfog");
Router::get('/post/{:id}', [TestClass::class, 'view']);

Router::get('/home/', fn() => print "gupi kutas");
Router::get('/home/{:id}', fn($id) => print "wow ".$id);
Router::get('/home/{:id}/edit', fn($id, $name) => print "wow".$id);
Router::get('/home/{:id}/edit/{:name}', fn($id, $name) => print "wow".$id);

Router::get('/test', [Test::class, 'item']);
Router::get('/test/{:id}', [Test::class, 'item2']);

//Router::handler(404, 'error.php');

Router::ignite();

?>
