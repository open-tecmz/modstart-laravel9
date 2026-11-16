<?php

// ===============================================================================
// Welcome 模块自动截图脚本（独立运行）
//
// 用法：
//   php module/Welcome/Test/ScreenShot.php
//
// 产出（module/Welcome/Temp/ScreenShotImage/）：
//   - 前台：pc.index.png / pcFull.index.png / m.index.png / mFull.index.png（PC 1280x800、手机 390x844）
//   - 后台：pc.dashboard.png（仅截取 iframe 内容区，需 .env 设置 MS_AUTO_TEST=1 跳过登录验证码）
//
// 前置条件：
//   - Node + playwright-core + 系统 Chrome 可用
//   - 站点已安装（storage/install.lock 存在）
//
// 说明：本项目为 Laravel 13 骨架（没有 bootstrap/autoload.php），因此这里直接引导
// vendor/autoload.php + bootstrap/app.php，不走 TestScript::boot()
// ===============================================================================

require __DIR__ . '/../../../vendor/autoload.php';

use ModStart\Test\TestScreenShot;

$app = require __DIR__ . '/../../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$module = 'Welcome';

// 前台首页（prune=false：保留同目录下的后台首页截图）
TestScreenShot::start();
TestScreenShot::captureFrontPages($module, [
    'index' => '/',
], [
    'prune' => false,
]);
TestScreenShot::stop();

// 后台首页（需 MS_AUTO_TEST=1 跳过登录验证码；capturePages 内部会重新启动浏览器并登录）
if (config('env.MS_AUTO_TEST')) {
    TestScreenShot::start();
    TestScreenShot::capturePages($module, [
        'dashboard' => '/admin',
    ]);
    TestScreenShot::stop();
}
