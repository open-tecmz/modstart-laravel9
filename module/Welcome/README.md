# 欢迎首页

提供介绍型的站点首页：介绍 ModStart 框架能力，并给出后台、开发文档、模块市场的快捷入口。

## 基本信息

- 模块标识：`Welcome`
- 适用端：PC / Mobile（同一套视图，按屏幕宽度响应式）
- 依赖模块：`Vendor`
- 安装：`php artisan modstart:module-install Welcome`（本模块无数据表，不需要迁移）
- 启用：在 `config/module.php` 中注册（`'Welcome' => ['enable' => true]`），或在后台「模块管理」启用

## 目录结构

| 目录 | 说明 |
| --- | --- |
| `Core/` | 模块服务提供者，向首页提供者注册本模块首页 |
| `Provider/` | 首页提供者 `WelcomeHomePageProvider` |
| `Web/` | 前台控制器与路由（`welcome`） |
| `View/pc/` | 前台 Blade 模板 |
| `Widget/` | 后台首页信息卡片 `WelcomeInfoWidget` |
| `View/widget/` | 后台首页卡片模板 |
| `Docs/` | 模块介绍与更新记录 |

## 对外能力

- **首页**：注册为首页提供者（PC / Mobile），可在「首页切换器」中选择「欢迎首页」
- **前台页面**：`/welcome`（独立访问地址，与首页展示同一页面）
- **控制器动作**：`\Module\Welcome\Web\Controller\IndexController@index`，供 `HomePageProvider` 或其它页面跳转调用
- **后台首页卡片**：`\Module\Welcome\Widget\WelcomeInfoWidget`（系统信息 + 版本 / 模块升级检测 + 版权说明），在 `app/Admin/Controller/IndexController::dashboard()` 中挂载

## 后台首页信息卡片

参考 Cms / Blog 模块的后台首页，`WelcomeInfoWidget` 提供两栏卡片：

- 左：当前版本（`AppConstant::APP_NAME` + `VERSION`）、版本检测占位（`[data-admin-version]`）、模块升级提示（`[data-module-notice]`）
- 右：提交工单、开源协议说明、定制入口与客服二维码

版本检测由 `https://modstart.com/api/app/version_check?app=<AppConstant::APP>&version=<AppConstant::VERSION>` 返回脚本填充；
如需关闭，设置配置项 `modstart.admin.versionCheckDisable = true`。

挂载方式（`app/Admin/Controller/IndexController`）：

```php
$page->row(new SecurityTooltipBox());
$page->append(new WelcomeInfoWidget());
```

## 设为站点首页

`app/Web/Controller/IndexController::index()` 的默认首页动作指向本模块：

```php
return HomePageProvider::call(__METHOD__, '\\Module\\Welcome\\Web\\Controller\\IndexController@index');
```

其它系统接管首页时按同样方式修改该默认动作，或开启 `HomePageSwitcher` 在后台切换。

## 视图与配置

- 模板：`View/pc/welcome/index.blade.php`，继承主题框架 `$_viewFrame`
- 内容为静态文案，不依赖数据库与站点配置；后台、文档等链接由 `modstart_admin_url()` 与固定官网地址生成
- 首屏展示演示后台入口与演示账号（`demo` / `123456`），方便访客直接体验后台；生产环境如不需要，删除模板中的 `.ms-welcome-demo` 区块即可
- 页面底部「当前启用的模块」由 `ModuleManager::listAllEnabledModules()` 实时读取，模块启停后自动更新

## 边界约束

- 模块不含数据表、不含后台管理页面，仅提供前台首页
- 样式全部消费设计令牌（`--color-*` / `--size-radius*` / `--font-family-serif` 等），跟随站点主色调与深色模式
- 页面动效只使用 `transform` / `opacity`，并在 `prefers-reduced-motion` 下关闭

## 检查清单

- [ ] `config/module.php` 中已注册并启用 `Welcome`
- [ ] 访问 `/` 显示欢迎首页，`/welcome` 可独立访问
- [ ] 后台「首页切换器」中可看到「欢迎首页」选项

## 测试与截图

- 截图：`php module/Welcome/Test/ScreenShot.php`（PC / 手机各 1 张静态图 + 1 张整页长图）
- 页面渲染冒烟：`php85 scripts/page-smoke.php --filter=/welcome`
