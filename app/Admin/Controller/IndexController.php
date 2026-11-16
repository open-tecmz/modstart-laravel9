<?php


namespace App\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\SecurityTooltipBox;
use ModStart\Layout\Row;
use Module\AdminManager\Widget\ServerInfoWidget;
use Module\Vendor\Admin\Widget\AdminWidgetDashboard;
use Module\Welcome\Widget\WelcomeInfoWidget;

class IndexController extends Controller
{
    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        /** @var AdminPage $page */
        $page = app(AdminPage::class);
        $page->pageTitle(L('Dashboard'));
        $page->row(new SecurityTooltipBox());
        // 顶部信息卡片：当前版本 + 版本 / 模块升级检测 + 版权说明（参考 Cms / Blog 的后台首页）
        $page->append(new WelcomeInfoWidget());
        $page->append(new Row(function (Row $row) {
            AdminWidgetDashboard::callIcon($row);
        }));
        AdminWidgetDashboard::call($page);
        $page->append(new ServerInfoWidget());
        return $page;
    }

}
