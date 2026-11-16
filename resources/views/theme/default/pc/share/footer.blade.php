<footer class="ub-footer-link reverse">
    <div class="ub-container">
        <div class="row">
            {{-- 页脚导航依赖 Nav 模块，未启用时不渲染该区域（避免导航数据缺失与空列） --}}
            @if(modstart_module_enabled('Nav'))
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-6">
                            <div class="link">
                                <div class="title">
                                    关于
                                </div>
                                <div class="list">
                                    @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('foot') as $nav)
                                        <a href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue(empty($nav['openType'])?null:$nav['openType'])}}>{{$nav['name']}}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="link">
                                <div class="title">
                                    导航
                                </div>
                                <div class="list">
                                    @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('footSecondary') as $nav)
                                        <a href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue(empty($nav['openType'])?null:$nav['openType'])}}>{{$nav['name']}}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="{{modstart_module_enabled('Nav')?'col-md-4':'col-md-12'}}">
                <div class="link">
                    <div class="title">
                        {{ modstart_config('siteName','ModStart') }}
                    </div>
                    <div class="list tw-pt-4 tw-text-gray-400">
                        @if(modstart_config('Site_ContactEmail'))
                            <div class="tw-py-1">
                                <i class="iconfont icon-email tw-inline-block tw-w-4"></i>
                                {{modstart_config('Site_ContactEmail')}}
                            </div>
                        @endif
                        @if(modstart_config('Site_ContactPhone'))
                            <div class="tw-py-1">
                                <i class="iconfont icon-phone tw-inline-block tw-w-4"></i>
                                {{modstart_config('Site_ContactPhone')}}
                            </div>
                        @endif
                        @if(modstart_config('Site_ContactAddress'))
                            <div class="tw-py-1">
                                <i class="iconfont icon-address tw-inline-block tw-w-4"></i>
                                {{modstart_config('Site_ContactAddress')}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tw-p-4 tw-text-center" style="background:#444;">
        {{-- 未配置备案号（安装时写入的是 [网站备案信息] 占位）时不显示备案链接 --}}
        @if(modstart_config('siteBeian') && !starts_with(modstart_config('siteBeian'),'['))
            <a href="http://beian.miit.gov.cn" target="_blank" class="tw-text-gray-100 hover:tw-text-gray-100">
                {{modstart_config('siteBeian')}}
            </a>
        @endif
        @if(modstart_config('siteBeianGonganText'))
            <a href="{{modstart_config('siteBeianGonganLink')}}" target="_blank" class="tw-text-gray-100 hover:tw-text-gray-100">
                 <img alt="{{modstart_config('siteBeianGonganText')}}" src="@asset('vendor/Site/image/gongan.png')" />
                {{modstart_config('siteBeianGonganText')}}
            </a>
        @endif
        @if(modstart_config('Site_CopyrightOthers'))
            {!! modstart_config('Site_CopyrightOthers') !!}
        @endif
        &copy;{{modstart_config('siteDomain') ?: modstart_config('siteName','ModStart')}}
    </div>
</footer>
