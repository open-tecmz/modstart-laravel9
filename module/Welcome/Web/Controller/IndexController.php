<?php


namespace Module\Welcome\Web\Controller;


use ModStart\Module\ModuleBaseController;
use ModStart\Module\ModuleManager;

class IndexController extends ModuleBaseController
{
    public function index()
    {
        $modules = [];
        foreach (ModuleManager::listAllEnabledModules() as $module => $_) {
            $basic = ModuleManager::getModuleBasic($module);
            if (empty($basic)) {
                continue;
            }
            $modules[] = [
                'name' => $module,
                'title' => isset($basic['title']) ? $basic['title'] : $module,
                'description' => isset($basic['description']) ? $basic['description'] : '',
            ];
        }
        return $this->view('welcome.index', [
            'modules' => $modules,
        ]);
    }
}
