<?php

use Illuminate\Support\Facades\Route;

// Member 模块未启用时不挂载会员中间件、不注册会员页面路由（避免依赖不存在的会员表）
$middleware = ['web.bootstrap'];
if (modstart_module_enabled('Member')) {
    $middleware[] = \Module\Member\Middleware\WebAuthMiddleware::class;
}

Route::group(
    [
        'namespace' => '\App\Web\Controller',
        'middleware' => $middleware,
    ], function () {

    Route::match(['get', 'post'], '', 'IndexController@index');
    // Route::match(['get', 'post'], 'test', 'IndexController@test');
//    Route::match(['get', 'post'], 'member', 'MemberController@index');

    if (modstart_module_enabled('Member')) {
        Route::match(['get', 'post'], 'member/{id}', 'MemberController@show');
        Route::match(['get', 'post'], 'member_profile', 'MemberProfileController@index');
    }

});
