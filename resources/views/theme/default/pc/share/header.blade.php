<header class="ub-header-b">
    <div class="ub-container">
        {{-- 会员入口依赖 Member 模块，未启用时不渲染（避免链接到不存在的登录 / 会员路由） --}}
        @if(modstart_module_enabled('Member'))
            <div class="menu">
                @if(\Module\Member\Auth\MemberUser::id())
                    <div class="item">
                        <a href="{{modstart_web_url('member')}}" class="sub-title">
                            <i class="iconfont icon-user"></i>
                            {{\Module\Member\Auth\MemberUser::get('username')}}
                        </a>
                        <div class="sub-nav">
                            {!! \Module\Member\Config\MemberNavMenu::render() !!}
                            <a class="sub-nav-item" href="{{modstart_web_url('member')}}">
                                用户中心
                            </a>
                            <a class="sub-nav-item" href="javascript:;"
                               data-href="{{modstart_web_url('logout')}}" data-confirm="确认退出登录？">
                                退出登录
                            </a>
                        </div>
                    </div>
                @else
                    <a href="{{modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::redirectUrl()])}}" rel="nofollow">
                        <i class="iconfont icon-user"></i> 
                        登录
                    </a>
                @endif
            </div>
        @endif
        <div class="logo">
            <a href="{{modstart_web_url('')}}">
                @if(modstart_config('siteLogo'))
                    <img alt="{{modstart_config('siteName')}}" src="{{modstart_config_asset_url('siteLogo')}}"/>
                @else
                    {{-- 未配置 Logo 时回退为站点名称，避免输出空 src 的破图 --}}
                    <span class="tw-font-bold">{{modstart_config('siteName','ModStart')}}</span>
                @endif
            </a>
        </div>
        <div class="nav-mask" onclick="MS.header.hide()"></div>
        <div class="nav">
            <?php $__searchBoxes = \Module\Vendor\Provider\SearchBox\SearchBoxProvider::all(); ?>
            @if(!empty($__searchBoxes))
                {{-- 搜索入口由已启用模块注册的 SearchBoxProvider 决定，没有搜索类模块时不显示 --}}
                <?php $__searchBox = $__searchBoxes[0]; ?>
                <div class="search">
                    <div class="box">
                        <form action="{{$__searchBox->url()}}" method="get">
                            <input type="text" name="keywords" value="{{empty($keywords)?'':$keywords}}" placeholder="{{$__searchBox->placeholder()}}"/>
                            <button type="submit"><i class="iconfont icon-search"></i></button>
                        </form>
                    </div>
                </div>
            @endif
            @if(modstart_module_enabled('Nav'))
                {!! \Module\Nav\View\NavView::position('head') !!}
            @endif
        </div>
        <a class="nav-toggle" href="javascript:;" onclick="MS.header.trigger()">
            <i class="show iconfont icon-list"></i>
            <i class="close iconfont icon-close"></i>
        </a>
    </div>
</header>
