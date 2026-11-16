<?php


namespace Module\Welcome\Widget;


use ModStart\Widget\AbstractWidget;

/**
 * 后台首页顶部信息卡片
 *
 * 参考 Cms / Blog 模块的后台首页卡片：左侧展示当前版本与版本 / 模块升级检测，
 * 右侧展示版权说明与客服入口。可直接用于自定义后台首页（App\Admin\Controller\IndexController）。
 */
class WelcomeInfoWidget extends AbstractWidget
{
    protected $view = 'module::Welcome.View.widget.welcomeInfo';

    protected function variables()
    {
        return [
            'attributes' => $this->formatAttributes(),
        ];
    }
}
