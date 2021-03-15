<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


$api = app('Dingo\Api\Routing\Router');
$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array','cross']//auths 权限判断
], function ($api) {


    $api->group(['prefix' => 'gift'], function ($api) {
        $api->get('GetOrderNoInfo', 'OrderGiftController@GetOrderNoInfo')->name('test.GetOrderNoInfo');
        $api->get('GetOrderReviewInfo', 'OrderGiftController@GetOrderReviewInfo')->name('test.GetOrderReviewInfo');
        $api->get('SaveOrderNo', 'OrderGiftController@SaveOrderNo')->name('test.SaveOrderNo');
        $api->get('SaveOrderReview', 'OrderGiftController@SaveOrderReview')->name('test.SaveOrderReview');
        $api->get('GetOrderNoPageList', 'OrderGiftController@GetOrderNoPageList')->name('test.GetOrderNoPageList');
        $api->get('GetOrderReviewPageList', 'OrderGiftController@GetOrderReviewPageList')->name('test.GetOrderReviewPageList');
        $api->post('ImportOrderNo', 'OrderGiftController@ImportOrderNo')->name('test.ImportOrderNo');

    });
});