<?php
namespace addons\qubit_bt_manager;

use ESA\Addons;

class Main extends Addons
{
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
    
    public function model($model){
        $models = [
            "classes" => "\addons\qubit_bt_manager\model\Classes",
            "carousel"=> "\addons\qubit_bt_manager\model\Carousel",
        ];
        return model($models[$model]);
    }
}