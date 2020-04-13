<?php

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

/*$router->get('/', function () use ($router) {
    return $router->app->version();
});*/

$router = app('Dingo\Api\Routing\Router');
$router->version('v1', function ($api) {
    $namespace = 'App\Http\Controllers';
    $api->group(['namespace' => $namespace], function ($api) use ($namespace) {
        $api->get('login','LoginController@login');
        $api->get('outlogin','LoginController@outLogin');
        $api->get('refresh','LoginController@refresh');
        $api->get('getcaptcha','LoginController@getCaptcha');
        $api->get('edituser','LoginController@editUser');

        /*$apis = \App\Models\SystemApi::getApis();

        if (isset($apis) && count($apis) > 0) {
            foreach ($apis as $a) {
                if (!class_exists($namespace . '\\' . $a['controller'])) continue;
                if ($a['method'] == '2') {
                    $api->post($a['url'], ['as' => $a['name'], 'uses' => $a['controller'] . '@' . $a['action']]);
                } else if ($a['method'] == '1') {
                    $api->get($a['url'], ['as' => $a['name'], 'uses' => $a['controller'] . '@' . $a['action']]);
                }
            }
        }*/

    });


});
