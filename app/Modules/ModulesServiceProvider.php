<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;

/**
 * Сервис провайдер для подключения модулей
 */
class ModulesServiceProvider extends ServiceProvider {

    public function boot() {
        //получаем список модулей, которые надо подгрузить
        $modules = config("module.modules");
        if($modules)
            $this->getModules($modules);
    }

    public function register() {
        //
    }

    private function getModules($modules, $modulePath = null){

        foreach ($modules as $key => $module){

            if(is_string($key))
                $this->getModules($module, $modulePath.'/'.$key);
            else
                $this->setModule($modulePath.'/'.$module);
        }
    }

    protected function setModule($module) {
        //Подключаем роуты для модуля
        if(file_exists(__DIR__.$module.'/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__.'/'.$module.'/routes/web.php');
        }
        if(file_exists(__DIR__.$module.'/routes/api.php')) {
            $this->loadRoutesFrom(__DIR__.$module.'/routes/api.php');
        }

        //Подгружаем миграции
        if(is_dir(__DIR__.$module.'/migration')) {
            $this->loadMigrationsFrom(__DIR__.$module.'/migration');
        }

        //Загружаем View view('Test::admin')
        if(is_dir(__DIR__.$module.'/Views')) {
            $this->loadViewsFrom(__DIR__.$module.'/Views', $module);
        }
        //Подгружаем переводы trans('Test::messages.welcome')
        if(is_dir(__DIR__.$module.'/lang')) {
            $this->loadTranslationsFrom(__DIR__.$module.'/lang', $module);
        }
    }
}
