@extends($_viewFrame)

@section('pageTitleMain')
    ModStart 模块化开发框架
@endsection

@section('headAppend')
    @parent
    <style type="text/css">
        .ms-welcome-hero {
            position: relative;
            overflow: hidden;
            background: var(--color-content-bg);
            border-bottom: 1px solid var(--color-body-line);
        }

        .ms-welcome-hero::before {
            content: "";
            position: absolute;
            top: -30%;
            right: -10%;
            left: -10%;
            height: 130%;
            background: radial-gradient(60% 70% at 12% 0, var(--color-primary-soft) 0, rgba(0, 0, 0, 0) 60%),
            radial-gradient(55% 65% at 92% 10%, var(--color-primary-glow) 0, rgba(0, 0, 0, 0) 62%);
            pointer-events: none;
        }

        .ms-welcome-hero-inner {
            position: relative;
            max-width: 56rem;
            padding: 5rem 0 4rem;
        }

        .ms-welcome-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--size-radius-full);
            background: var(--color-primary-soft);
            color: var(--color-primary);
            font-size: 0.75rem;
            letter-spacing: 0.02em;
        }

        .ms-welcome-title {
            margin: 1rem 0 0;
            font-size: clamp(1.75rem, 4vw, 2.75rem);
            line-height: 1.25;
            color: var(--color-heading);
        }

        .ms-welcome-sub {
            margin: 1rem 0 0;
            max-width: 40rem;
            font-size: 1rem;
            line-height: 1.8;
            color: var(--color-text);
        }

        .ms-welcome-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.75rem;
        }

        .ms-welcome-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.625rem 1.25rem;
            border-radius: var(--size-radius-sm);
            border: 1px solid var(--color-body-line);
            background: var(--color-content-bg);
            color: var(--color-text);
            font-size: 0.9375rem;
            text-decoration: none;
            transition: var(--transition-fast);
        }

        .ms-welcome-btn:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .ms-welcome-btn-primary {
            border-color: var(--color-primary);
            background: var(--color-primary);
            color: var(--color-text-reverse);
        }

        .ms-welcome-btn-primary:hover {
            opacity: 0.9;
            color: var(--color-text-reverse);
        }

        .ms-welcome-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem 1.25rem;
            margin-top: 1.75rem;
            font-size: 0.8125rem;
            color: var(--color-muted);
        }

        /* 演示后台账号提示 */
        .ms-welcome-demo {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem 0.5rem;
            margin-top: 1.25rem;
            padding: 0.5rem 0.875rem;
            border-radius: var(--size-radius-sm);
            background: var(--color-primary-light-bg);
            color: var(--color-text);
            font-size: 0.8125rem;
            line-height: 1.6;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .ms-welcome-demo a {
            color: var(--color-primary);
            text-decoration: none;
        }

        .ms-welcome-demo a:hover {
            text-decoration: underline;
        }

        .ms-welcome-demo code {
            padding: 0.0625rem 0.375rem;
            border-radius: var(--size-radius-xs);
            background: var(--color-body-block-bg);
            color: var(--color-heading);
            font-family: var(--font-family-mono);
            font-size: 0.75rem;
        }

        .ms-welcome-section {
            padding: 3.5rem 0;
        }

        .ms-welcome-section + .ms-welcome-section {
            border-top: 1px solid var(--color-body-line);
        }

        .ms-welcome-section-head {
            max-width: 42rem;
            margin-bottom: 1.75rem;
        }

        .ms-welcome-section-title {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
            color: var(--color-heading);
        }

        .ms-welcome-section-desc {
            margin: 0;
            font-size: 0.9375rem;
            line-height: 1.8;
            color: var(--color-tertiary);
        }

        .ms-welcome-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .ms-welcome-card {
            padding: 1.25rem;
            border: 1px solid var(--color-body-line);
            border-radius: var(--size-radius);
            background: var(--color-content-bg);
            box-shadow: var(--box-shadow-xs);
            overflow-wrap: break-word;
            word-break: break-word;
            transition: transform 0.2s var(--ease-out), box-shadow 0.2s var(--ease-out), border-color 0.2s var(--ease-out);
        }

        .ms-welcome-card:hover {
            transform: translateY(-2px);
            border-color: var(--color-tertiary-light);
            box-shadow: var(--box-shadow-hover);
        }

        .ms-welcome-card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            margin-bottom: 0.75rem;
            border-radius: var(--size-radius-sm);
            background: var(--color-primary-light-bg);
            color: var(--color-primary);
            font-size: 1rem;
        }

        .ms-welcome-card-title {
            margin: 0 0 0.375rem;
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-heading);
        }

        .ms-welcome-card-desc {
            margin: 0;
            font-size: 0.875rem;
            line-height: 1.7;
            color: var(--color-tertiary);
        }

        .ms-welcome-steps {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            counter-reset: mswstep;
        }

        .ms-welcome-step {
            position: relative;
            padding: 1.25rem 1.25rem 1.25rem 3.5rem;
            border: 1px solid var(--color-body-line);
            border-radius: var(--size-radius);
            background: var(--color-content-bg);
            overflow-wrap: break-word;
        }

        .ms-welcome-step::before {
            counter-increment: mswstep;
            content: counter(mswstep);
            position: absolute;
            top: 1.25rem;
            left: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            border-radius: var(--size-radius-full);
            background: var(--color-primary);
            color: var(--color-text-reverse);
            font-size: 0.8125rem;
        }

        .ms-welcome-step-title {
            margin: 0 0 0.375rem;
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--color-heading);
        }

        .ms-welcome-step-desc {
            margin: 0 0 0.75rem;
            font-size: 0.875rem;
            line-height: 1.7;
            color: var(--color-tertiary);
        }

        .ms-welcome-code {
            margin: 0;
            padding: 0.625rem 0.875rem;
            border-radius: var(--size-radius-sm);
            background: var(--color-body-block-bg);
            color: var(--color-text);
            font-family: var(--font-family-mono);
            font-size: 0.8125rem;
            line-height: 1.7;
            white-space: pre;
            overflow-x: auto;
        }

        .ms-welcome-modules {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .ms-welcome-module {
            display: inline-block;
            max-width: 100%;
            padding: 0.375rem 0.75rem;
            border-radius: var(--size-radius-full);
            background: var(--color-body-block-bg);
            color: var(--color-text);
            font-size: 0.8125rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ms-welcome-links {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .ms-welcome-link {
            display: block;
            padding: 1.125rem;
            border: 1px solid var(--color-body-line);
            border-radius: var(--size-radius);
            background: var(--color-content-bg);
            text-decoration: none;
            overflow-wrap: break-word;
            word-break: break-word;
            transition: transform 0.2s var(--ease-out), border-color 0.2s var(--ease-out), box-shadow 0.2s var(--ease-out);
        }

        .ms-welcome-link:hover {
            transform: translateY(-2px);
            border-color: var(--color-primary);
            box-shadow: var(--box-shadow-hover);
        }

        .ms-welcome-link-title {
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--color-heading);
        }

        .ms-welcome-link-desc {
            display: block;
            font-size: 0.8125rem;
            line-height: 1.6;
            color: var(--color-tertiary);
        }

        @media screen and (max-width: 56rem) {
            .ms-welcome-hero-inner {
                padding: 4rem 0 3rem;
            }

            .ms-welcome-grid,
            .ms-welcome-steps {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ms-welcome-links {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media screen and (max-width: 40rem) {
            .ms-welcome-hero-inner {
                padding: 3rem 0 2.5rem;
            }

            .ms-welcome-section {
                padding: 2.5rem 0;
            }

            .ms-welcome-grid,
            .ms-welcome-steps,
            .ms-welcome-links {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .ms-welcome-card,
            .ms-welcome-link,
            .ms-welcome-btn {
                transition: none;
            }

            .ms-welcome-card:hover,
            .ms-welcome-link:hover {
                transform: none;
            }
        }
    </style>
@endsection

@section('bodyContent')

    {!! \ModStart\ModStart::js('asset/common/scrollAnimate.js') !!}

    <div class="ms-welcome">

        <section class="ms-welcome-hero">
            <div class="ub-container">
                <div class="ms-welcome-hero-inner">
                    <span class="ms-welcome-badge">ModStart · 模块化开发框架</span>
                    <h1 class="ms-welcome-title">用模块搭建你的网站与业务系统</h1>
                    <p class="ms-welcome-sub">
                        ModStart 是一套基于 Laravel 的模块化开发框架：把能力拆成一个个独立模块，后台一键安装、按需启用，
                        前台与后台开箱即用。同一份模块代码可运行在 ModStart 的 Laravel 5.1 / 9 / 13 发行版上。
                    </p>
                    <div class="ms-welcome-actions">
                        <a class="ms-welcome-btn ms-welcome-btn-primary" href="{{modstart_admin_url('')}}" target="_blank" rel="noopener">
                            <i class="iconfont icon-desktop"></i>
                            进入后台管理
                        </a>
                        <a class="ms-welcome-btn" href="https://modstart.com/doc" target="_blank" rel="noopener">
                            <i class="iconfont icon-book"></i>
                            开发文档
                        </a>
                    </div>
                    <div class="ms-welcome-meta">
                        <span>Laravel {{ \Illuminate\Foundation\Application::VERSION }}</span>
                        <span>ModStart {{ modstart_version() }}</span>
                        <span>PHP {{ PHP_VERSION }}</span>
                        <span>已启用模块 {{ count($modules) }} 个</span>
                    </div>
                    <div class="ms-welcome-demo">
                        <i class="iconfont icon-user"></i>
                        <span>演示后台</span>
                        <a href="{{modstart_admin_url('')}}" target="_blank" rel="noopener">{{parse_url(modstart_admin_url(''), PHP_URL_PATH)}}</a>
                        <span>·</span>
                        <span>账号 <code>demo</code></span>
                        <span>密码 <code>123456</code></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="ms-welcome-section">
            <div class="ub-container">
                <div class="ms-welcome-section-head">
                    <h2 class="ms-welcome-section-title">框架特性</h2>
                    <p class="ms-welcome-section-desc">
                        内核只做通用能力，业务能力全部以模块形式存在，安装、升级、卸载互不影响。
                    </p>
                </div>
                <div class="ms-welcome-grid">
                    <div class="ms-welcome-card" data-scroll-animate="animated fadeInUp">
                        <span class="ms-welcome-card-icon"><i class="iconfont icon-cube"></i></span>
                        <h3 class="ms-welcome-card-title">全模块化</h3>
                        <p class="ms-welcome-card-desc">路由、模型、视图、菜单全部封装在模块内，像积木一样自由组合，随时启停。</p>
                    </div>
                    <div class="ms-welcome-card" data-scroll-animate="animated fadeInUp">
                        <span class="ms-welcome-card-icon"><i class="iconfont icon-tools"></i></span>
                        <h3 class="ms-welcome-card-title">后台开箱即用</h3>
                        <p class="ms-welcome-card-desc">RBAC 权限、数据表格、数据表单、文件上传、操作日志等后台能力内置，无需重复开发。</p>
                    </div>
                    <div class="ms-welcome-card" data-scroll-animate="animated fadeInUp">
                        <span class="ms-welcome-card-icon"><i class="iconfont icon-code"></i></span>
                        <h3 class="ms-welcome-card-title">一份代码多版本</h3>
                        <p class="ms-welcome-card-desc">模块遵循统一的语法约定，同一份模块代码兼容 Laravel 5.1 / 9 / 13 发行版。</p>
                    </div>
                    <div class="ms-welcome-card" data-scroll-animate="animated fadeInUp">
                        <span class="ms-welcome-card-icon"><i class="iconfont icon-shield-check"></i></span>
                        <h3 class="ms-welcome-card-title">内置实现优先</h3>
                        <p class="ms-welcome-card-desc">通用能力由基础库内置实现，不额外引入第三方 PHP 依赖，升级路径更可控。</p>
                    </div>
                    <div class="ms-welcome-card" data-scroll-animate="animated fadeInUp">
                        <span class="ms-welcome-card-icon"><i class="iconfont icon-magic-wand"></i></span>
                        <h3 class="ms-welcome-card-title">模块市场</h3>
                        <p class="ms-welcome-card-desc">后台「模块市场」在线安装、升级模块，业务能力按需扩展，不用改动核心代码。</p>
                    </div>
                    <div class="ms-welcome-card" data-scroll-animate="animated fadeInUp">
                        <span class="ms-welcome-card-icon"><i class="iconfont icon-mobile"></i></span>
                        <h3 class="ms-welcome-card-title">多端适配</h3>
                        <p class="ms-welcome-card-desc">PC 主题、H5 页面与接口共用同一套业务逻辑，后台也可在手机、平板上管理。</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="ms-welcome-section">
            <div class="ub-container">
                <div class="ms-welcome-section-head">
                    <h2 class="ms-welcome-section-title">三步启动</h2>
                    <p class="ms-welcome-section-desc">
                        准备 PHP 8.4+ 与 MySQL 8.0+ 环境，在项目根目录执行以下命令即可完成安装。
                    </p>
                </div>
                <div class="ms-welcome-steps">
                    <div class="ms-welcome-step" data-scroll-animate="animated fadeInUp">
                        <h3 class="ms-welcome-step-title">安装依赖</h3>
                        <p class="ms-welcome-step-desc">拉取 Laravel 与基础库依赖。</p>
                        <div class="ms-welcome-code">composer install</div>
                    </div>
                    <div class="ms-welcome-step" data-scroll-animate="animated fadeInUp">
                        <h3 class="ms-welcome-step-title">配置环境</h3>
                        <p class="ms-welcome-step-desc">生成配置文件并填写数据库连接。</p>
                        <div class="ms-welcome-code">cp .env.example .env
php artisan key:generate</div>
                    </div>
                    <div class="ms-welcome-step" data-scroll-animate="animated fadeInUp">
                        <h3 class="ms-welcome-step-title">初始化系统</h3>
                        <p class="ms-welcome-step-desc">执行迁移、安装已启用模块并创建管理员。</p>
                        <div class="ms-welcome-code">php artisan modstart:init</div>
                    </div>
                </div>
            </div>
        </section>

        @if(!empty($modules))
            <section class="ms-welcome-section">
                <div class="ub-container">
                    <div class="ms-welcome-section-head">
                        <h2 class="ms-welcome-section-title">当前启用的模块</h2>
                        <p class="ms-welcome-section-desc">
                            系统默认只启用必要的基础模块，其余能力可在后台「模块市场」按需安装。
                        </p>
                    </div>
                    <div class="ms-welcome-modules">
                        @foreach($modules as $module)
                            <span class="ms-welcome-module" title="{{ $module['description'] }}">{{ $module['title'] }}</span>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="ms-welcome-section">
            <div class="ub-container">
                <div class="ms-welcome-section-head">
                    <h2 class="ms-welcome-section-title">快捷入口</h2>
                    <p class="ms-welcome-section-desc">常用的文档与站点入口，收藏备用。</p>
                </div>
                <div class="ms-welcome-links">
                    <a class="ms-welcome-link" href="{{modstart_admin_url('')}}" target="_blank" rel="noopener">
                        <span class="ms-welcome-link-title">后台管理</span>
                        <span class="ms-welcome-link-desc">站点配置、内容管理与模块安装（演示账号 demo / 123456）</span>
                    </a>
                    <a class="ms-welcome-link" href="https://modstart.com/doc" target="_blank" rel="noopener">
                        <span class="ms-welcome-link-title">开发文档</span>
                        <span class="ms-welcome-link-desc">模块开发、字段与后台组件说明</span>
                    </a>
                    <a class="ms-welcome-link" href="https://modstart.com/store" target="_blank" rel="noopener">
                        <span class="ms-welcome-link-title">模块市场</span>
                        <span class="ms-welcome-link-desc">按需安装业务模块，扩展站点能力</span>
                    </a>
                    <a class="ms-welcome-link" href="https://modstart.com" target="_blank" rel="noopener">
                        <span class="ms-welcome-link-title">官方网站</span>
                        <span class="ms-welcome-link-desc">产品介绍、演示站点与更新公告</span>
                    </a>
                </div>
            </div>
        </section>

    </div>

@endsection
