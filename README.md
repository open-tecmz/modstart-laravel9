<p align="center">
  <a href="https://modstart.com">
    <img src="https://ms-assets.modstart.com/data/image/2021/09/08/23652_1f1j_9825.png" alt="ModStart" width="360" />
  </a>
</p>
<p align="center">
  基于 Laravel 9 的模块化框架站点
</p>

<p align="center">
  <a href="LICENSE"><img alt="License Apache 2.0" src="https://img.shields.io/badge/License-Apache%202.0-blue"></a>
  <a href="https://modstart.com"><img alt="Framework ModStart" src="https://img.shields.io/badge/Framework-ModStart-blue"></a>
  <a href="https://www.php.net"><img alt="PHP 8.1" src="https://img.shields.io/badge/PHP-8.1-red"></a>
  <a href="https://laravel.com"><img alt="Laravel 9" src="https://img.shields.io/badge/Laravel-9.x-red"></a>
  <a href="https://vuejs.org"><img alt="Vue / Element UI" src="https://img.shields.io/badge/JS-Vue%2FElementUI-green"></a>
</p>

# modstart-laravel9：Laravel 9 模块化框架站点

`ModStart` 框架在本项目中已精简装配：默认只启用 **Welcome（欢迎首页）/ Nav（导航）/ Site（站点设置）** 三个业务模块，以及 `Vendor`、`AdminManager`、`ModuleStore` 三个基础支撑模块。

## 🔥 功能一览

- 欢迎首页：`Welcome` 模块提供介绍型首页，展示当前启用的模块清单，并提供后台 / 文档 / 模块市场入口
- 站点设置：`Site` 模块提供站点名称、Logo、联系方式、备案号等基础配置
- 导航管理：`Nav` 模块提供头部导航、底部导航、底部次导航三个位置
- 后台管理：基于 RBAC 的权限管理，多管理员多角色，手机、平板、PC 浏览器均可管理
- 全模块化开发：所有业务能力以模块形式存在，安装、升级、卸载互不影响
- 后台内置数据表格、数据表单与文件上传；云存储需安装 `DataQiniuKodo`、`DataAliyunOss` 等存储模块

## 💡 系统简介

本项目是一个基于 **Laravel 9** 的模块化框架站点，底座为 ModStart 基础库（Composer 包 `modstart/modstart-laravel9`，实际代码位于 `vendor/modstart/modstart`）。

系统默认只注册 6 个模块（3 个业务模块 + 3 个基础支撑模块，见 `config/module.php`）：

| 模块 | 说明 |
|---|---|
| `Vendor` | 通用功能包（基础库能力封装） |
| `AdminManager` | 管理员基础框架 |
| `ModuleStore` | 模块市场 |
| `Site` | 站点基本设置 |
| `Nav` | 通用导航 |
| `Welcome` | 欢迎首页 |

- 版本：`1.0.0`（`AppConstant::VERSION`），应用标识：`modstart-laravel9`
- [ModStart 官方网站](https://modstart.com)
- [模块市场](https://modstart.com/store)
- [开发文档](https://modstart.com/doc)

系统基于 **Apache License 2.0** 开源（见 [LICENSE](LICENSE)），允许商业使用。

### 技术栈

- [Laravel 9.52.6](https://laravel.com/)
- PHP 8.1 / 8.2
- MySQL 5.7+
- [ModStart 基础库](https://modstart.com/doc)
- [Vue](https://vuejs.org/) / [Element UI](https://element.eleme.io/) / [jQuery](https://jquery.com)

## 🎨 访问入口

### 前台首页

访问 `/` 或 `/welcome`：两者都由 `Welcome` 模块渲染，`/welcome` 为独立访问地址。

### 后台管理

`/admin/`（默认值，可在 `.env` 中通过 `ADMIN_PATH` 修改），管理员账号在安装时自行设置。默认后台菜单为：**系统概况**、**物料管理 → 导航设置**、**系统设置 → 基本设置**、**后台管理**（管理员 / 管理角色 / 管理日志）、**系统管理 → 模块管理**。

## 📁 目录结构

```text
app/
├── Admin/
│   ├── Controller/IndexController.php   后台仪表盘（安全提示 + 图标统计 + 服务器信息）
│   ├── Core/ModuleServiceProvider.php   注册后台菜单「系统概况」
│   └── routes.php                       后台路由，前缀由 ADMIN_PATH 控制
├── Api/
│   ├── Controller/                      接口控制器
│   └── routes.php                       /api 路由（Member 未启用时不挂会员中间件）
├── Web/
│   ├── Controller/                      前台控制器（首页入口等）
│   └── routes.php                       前台路由（Member 未启用时不挂会员中间件）
├── Constant/AppConstant.php             APP / APP_NAME / VERSION
└── Console/、Exceptions/、Http/、Providers/   骨架组件

config/module.php                        6 个系统模块的注册表
database/migrations/                     核心表迁移（config / data / admin 等）

module/                                  模块目录

public/
├── install.php                          安装向导
├── index.php                            入口文件
├── asset/                               基础库静态资源（vendor:publish 发布）
└── vendor/                              模块静态资源（模块安装时发布）

resources/views/
├── errors/404.blade.php、500.blade.php  错误页（支持 ErrorPage 配置覆盖）
└── theme/default/pc/                    默认 PC 主题（frame / header / footer / member frame）

env.example                              环境变量模板
vendor/modstart/modstart                 ModStart 基础库
```

## 🎁 模块市场

后台「系统管理 → 模块管理」可安装 `module/` 中已有的模块包；在线下载 / 升级模块需要联网并登录 ModStart 账号。

## 🔧 系统安装

### 环境要求

- `PHP 8.1.x` / `8.2.x`（`composer.json` 约束为 `^8.0`；8.3+ 未列入官方支持范围）
- `MySQL 5.7+` / `MariaDB 10.3+`（Laravel 9 官方要求，本项目已在 PHP 8.1.34 + MySQL 5.7.44 下验证）
- `PHP Extension`：`curl`、`zip`、`openssl`、`exif`、`bcmath`、`pdo_mysql`、`mbstring`、`tokenizer`、`fileinfo`
- 需允许的函数：`proc_open`、`putenv`、`proc_get_status`
- 目录可写：`bootstrap/`、`bootstrap/cache/`、`storage/`、`public/`
- `Apache / Nginx`：Web 根目录指向 `public/`，需支持 Rewrite，访问 `/install/ping` 应返回 `ok`
- 图像处理建议安装 `gd` 扩展

### 安装步骤

1. 获取代码（二选一）：

```bash
git clone https://github.com/open-tecmz/modstart-laravel9.git
# 或
git clone https://gitee.com/open-tecmz/modstart-laravel9.git
```

2. 安装依赖：

```bash
composer install
```

3. 复制环境变量模板。仓库中的文件名是 `env.example`（不是 `.env.example`）：

```bash
cp env.example .env
```

4. 编辑 `.env`：填写 `DB_HOST`、`DB_PORT`、`DB_DATABASE`、`DB_USERNAME`、`DB_PASSWORD`（Linux 下 MySQL 走 socket 报 `No such file or directory` 时，把 `DB_HOST` 改为 `127.0.0.1`）；把 `ENCRYPT_KEY` 改为 32 位随机字符串，不要保留模板中的全 `A` 占位值；按环境确认 `APP_ENV`（本地 `local` / 生产 `product`）与 `APP_DEBUG`（生产设为 `false`）。

5. 初始化系统（迁移、安装已启用模块、创建管理员、写入 `storage/install.lock`）：

```bash
php artisan key:generate
php artisan modstart:init
```

   `modstart:init` 会交互式询问管理员用户名和密码，也可以直接传参：

```bash
php artisan modstart:init --username=admin --password='你的密码'
```

6. 也可以使用安装向导：将 Web 根目录指向 `public/`，浏览器访问 `/install.php`，按向导填写数据库与管理员信息。向导会生成 `APP_KEY` / `ENCRYPT_KEY` 并写入 `storage/install.lock`。

> `php artisan migrate --force` 与 `php artisan modstart:module-install-all` 只负责建表和安装模块，**不会**创建管理员，也**不会**写入 `storage/install.lock`；只执行这两条命令时，访问 `/` 会一直跳转到 `/install.php`。纯命令行安装请使用 `php artisan modstart:init`。
>
> 首次安装只安装 `config/module.php` 中注册的 6 个模块；其余模块在后台「系统管理 → 模块管理」中安装。
>
> 如果 `public/asset` 为空，或升级后静态资源不同步，执行 `php artisan vendor:publish --provider="ModStart\ModStartServiceProvider"`。
>
> 需要重装时，删除 `storage/install.lock` 并清空数据库后重新安装。

### 升级

```bash
git pull
composer install
php artisan migrate --force
php artisan modstart:module-install-all
php artisan modstart:module-refresh-all
```

升级前请备份数据库与 `.env`；如果静态资源有更新，再执行：

```bash
php artisan vendor:publish --provider="ModStart\ModStartServiceProvider"
```

## 🔨 开发速看

以下以一个「新闻管理」模块 `DemoNews` 为例，快速了解 ModStart 模块开发流程。仓库中已有官方示例模块 `module/Demo`，可按同样结构对照阅读。

> 本文示例为手动创建模块的流程。若需要模块脚手架 / 打包工具，可在后台「系统管理 → 模块管理」安装 `ModuleDeveloper`（模块开发助手，默认未启用）。

### 模块目录

```text
module/DemoNews/
├── config.json
├── Core/ModuleServiceProvider.php
├── Admin/routes.php
├── Admin/Controller/DemoNewsController.php
└── Migrate/2024_01_01_000000_create_demo_news.php
```

### 模块声明 `config.json`

```json
{
    "name": "DemoNews",
    "title": "新闻管理",
    "version": "1.0.0",
    "require": ["Vendor"],
    "env": ["laravel9"],
    "modstartVersion": ">=6.0.0",
    "description": "示例新闻管理模块"
}
```

### 数据表迁移

文件位置：`module/DemoNews/Migrate/2024_01_01_000000_create_demo_news.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDemoNews extends Migration
{
    public function up()
    {
        Schema::create('demo_news', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('title', 200)->nullable()->comment('标题');
            $table->string('cover', 200)->nullable()->comment('封面');
            $table->string('summary', 200)->nullable()->comment('摘要');
            $table->text('content')->nullable()->comment('内容');
        });
    }

    public function down()
    {
        Schema::dropIfExists('demo_news');
    }
}
```

### 控制器

文件位置：`module/DemoNews/Admin/Controller/DemoNewsController.php`

```php
<?php

namespace Module\DemoNews\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Grid\GridFilter;

class DemoNewsController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('demo_news')
            ->field(function ($builder) {
                $builder->id('id', 'ID');
                $builder->text('title', '标题');
                $builder->image('cover', '封面');
                $builder->textarea('summary', '摘要');
                $builder->richHtml('content', '内容');
                $builder->display('created_at', '创建时间');
                $builder->display('updated_at', '更新时间');
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', 'ID');
                $filter->like('title', '标题');
            })
            ->title('新闻管理');
    }
}
```

### 注册后台路由

文件位置：`module/DemoNews/Admin/routes.php`。模块路由会由框架自动加上 `ADMIN_PATH` 前缀和 `\Module\DemoNews\Admin\Controller` 命名空间，因此下面的 `demo_news` 实际地址是 `/admin/demo_news`。

```php
<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'demo_news', 'DemoNewsController@index');
$router->match(['get', 'post'], 'demo_news/add', 'DemoNewsController@add');
$router->match(['get', 'post'], 'demo_news/edit', 'DemoNewsController@edit');
$router->match(['post'], 'demo_news/delete', 'DemoNewsController@delete');
$router->match(['get'], 'demo_news/show', 'DemoNewsController@show');
```

### 注册后台菜单

文件位置：`module/DemoNews/Core/ModuleServiceProvider.php`

```php
<?php

namespace Module\DemoNews\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(Dispatcher $events)
    {
        AdminMenu::register(function () {
            return [
                [
                    'title' => '新闻管理',
                    'icon' => 'list',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => '新闻列表',
                            'url' => '\Module\DemoNews\Admin\Controller\DemoNewsController@index',
                        ],
                    ],
                ],
            ];
        });
    }
}
```

### 注册并安装模块

在 `config/module.php` 的 `system` 中注册并启用：

```php
'DemoNews' => [
    'enable' => true,
],
```

然后执行安装（也可在后台「系统管理 → 模块管理」中安装）：

```bash
php artisan modstart:module-install DemoNews
```

安装完成后，后台「新闻管理 → 新闻列表」即可访问，一个带列表、新增、编辑、删除、详情的页面就完成了。

> 仓库中已存在 `module/News`（新闻模块，默认未启用）。上面的示例使用 `demo_news` 表名与 `CreateDemoNews` / `DemoNewsController` 类名，避免与 `module/News` 中的 `CreateNews` 迁移类重名（同名迁移类在同一进程内安装多个模块时会触发 `Cannot redeclare class`）。

## 📋 常见问题

遇到安装或使用问题，请先查阅官方常见问题：

- [https://modstart.com/doc/install/qa.html](https://modstart.com/doc/install/qa.html)

也可以到官方论坛交流：

- [https://modstart.com/forum](https://modstart.com/forum)

## ⭐️ 贡献代码

项目仓库：

- GitHub：[https://github.com/open-tecmz/modstart-laravel9](https://github.com/open-tecmz/modstart-laravel9)
- Gitee：[https://gitee.com/open-tecmz/modstart-laravel9](https://gitee.com/open-tecmz/modstart-laravel9)

欢迎通过 Issue 反馈问题、通过 Pull Request 提交改进。提交前请在本地完成一次安装，确认 `/`、`/admin/` 与「系统管理 → 模块管理」均可正常访问。

## ✉️ 使用交流

> 以下为 ModStart 官方交流群，添加好友请备注 ModStart；本项目问题请优先通过仓库 Issue 反馈。

<table width="100%">
    <thead>
        <tr>
            <th width="50%">微信交流群</th>
            <th>QQ交流群</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <img style="width:100%;"
                     src="https://open.tecmz.com/code_dynamic/wx" />
            </td>
            <td>
                <img style="width:100%;"
                     src="https://open.tecmz.com/code_dynamic/qq" />
            </td>
        </tr>
    </tbody>
</table>
