<?php


namespace Module\Welcome\Provider;


use Module\Vendor\Provider\HomePage\AbstractHomePageProvider;

class WelcomeHomePageProvider extends AbstractHomePageProvider
{
    const ACTION = '\\Module\\Welcome\\Web\\Controller\\IndexController@index';

    /**
     * 适配的类型（PC 与手机共用同一套视图）
     * @return array
     */
    public function type()
    {
        return [
            self::TYPE_PC,
            self::TYPE_MOBILE,
        ];
    }

    public function title()
    {
        return '欢迎首页';
    }

    public function action()
    {
        return self::ACTION;
    }
}
