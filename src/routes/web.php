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

use App\Http\Controllers\captcha_solvers\RuCaptcha;
use App\Http\Controllers\channels\RedisChannels;
use App\Http\Controllers\ChecksCreator;
use App\Http\Controllers\handlers\ChecksHandler;
use App\Http\Controllers\Redis as RedisAlias;
use App\rcaChecksList;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Redis api
|--------------------------------------------------------------------------
|
*/

$router->get('/redis/list', function (Request $request) {

    $key = $request->get('key');

    return (new RedisAlias)->connect->lRange($key, 0, -1);
});

$router->put('/redis/list', function (Request $request) {

    $key = $request->get('key');
    $content = $request->getContent();

    return (new RedisAlias)->connect->lPush($key, $content);
});

$router->delete('/redis/list', function (Request $request) {

    $key = $request->get('key');

    return (new RedisAlias)->connect->del($key);
});


$router->get('/redis/keys', function () {

    return (new RedisAlias)->connect->keys('*');
});

$router->get('/redis/info', function () {

    return (new RedisAlias)->connect->info();
});


/*
|--------------------------------------------------------------------------
| RuCaptcha api
|--------------------------------------------------------------------------
|
*/
$router->get('/captcha', function () {

    $url = 'https://dkbm-web.autoins.ru/dkbm-web-1.0/policy.htm';
    $key = '6Lf2uycUAAAAALo3u8D10FqNuSpUvUXlfP7BzHOk';

    $c = (new RuCaptcha)->solveRecaptchaV2($url, $key);

    return [
        'result' => $c->result,
        'status' => $c->status,
        'errors' => $c->errors,
        'rucapTaskId' => $c->rucaptchaTaskId,
    ];


});


/*
|--------------------------------------------------------------------------
| RCA api
|--------------------------------------------------------------------------
|
*/


$router->get('/', function (Request $request) {

    return app()->version();

});


$router->get('/checks/handle', function (Request $request) {

    return (new ChecksHandler(RedisChannels::CHECKS_LIST))->handle();

});


$router->post('/redis/get', function (Request $request) {

    return $request->fullUrl();

});


/*
|--------------------------------------------------------------------------
| Main API
|--------------------------------------------------------------------------
|
*/
$router->post('/checks', function (Request $request) {

    $content = $request->getContent();

    return (new ChecksCreator($content))->createChecks();

});

$router->delete('/checks', function (Request $request) {

    $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

    return (new rcaChecksList())->viewed($content);

});

$router->get('/feed', function () {
    return (new rcaChecksList())->getFeed();
});
