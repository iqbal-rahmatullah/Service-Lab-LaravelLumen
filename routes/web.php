<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'labs'], function () use ($router) {
    $router->get('', 'LabController@index');
    $router->post('', 'LabController@store');
    $router->put('/{id}', 'LabController@update');
    $router->delete('/{id}', 'LabController@destroy');
    $router->get('/{id}', 'LabController@show');
});

$router->group(['prefix' => 'resultslab'], function () use ($router) {
    $router->get('', 'ResultLabController@index');
    $router->post('', 'ResultLabController@store');
    $router->post('/{id}', 'ResultLabController@update');
    $router->delete('/{id}', 'ResultLabController@destroy');
    $router->get('/{id}', 'ResultLabController@show');
});
