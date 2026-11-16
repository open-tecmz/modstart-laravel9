<?php

// Member 模块未启用时不挂载会员鉴权中间件
$middleware = ['api.bootstrap'];
if (modstart_module_enabled('Member')) {
    $middleware[] = \Module\Member\Middleware\ApiAuthMiddleware::class;
}

Route::group(
    [
        'middleware' => $middleware,
        'namespace' => '\App\Api\Controller',
        'prefix' => 'api',
    ], function () {

    Route::match(['get', 'post'], 'config_app', 'ConfigController@app');
    Route::match(['get', 'post'], 'config_constant', 'ConfigController@constant');

});
