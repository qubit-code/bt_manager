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
    
    /**
     * 功能入口方法
     * @return bool
     */
    public function entrance(){
        $res[] = [
            "title" => "客户端页面",
            "url"   => esaurl("index.index/index",[],".html",true)
        ];
        return $res;
    }
    
    public function model($name,$status=true){
        $models = [
            "servers" => "\addons\qubit_bt_manager\model\Servers",
            "configs" => "\addons\qubit_bt_manager\model\Configs",
            "sites" => "\addons\qubit_bt_manager\model\Sites",
            "users" => "\addons\qubit_bt_manager\model\Users",
            "orders" => "\addons\qubit_bt_manager\model\Orders",
            "articles" => "\addons\qubit_bt_manager\model\Articles",
            "pay"   => "\addons\qubit_bt_manager\model\Pay",
        ];
        $model = model($models[$name]);
        if ($status) {
            $model = $model->where("pfid",PLATFORM_ID);
        }
        return $model;
    }
    
    public static function index_menu(){
        return [
            [
                "id"    => 1,
                "fid"   => 0,
                "name"  => "servers",
                "type"  => "system",
                "title" => "服务器管理",
                "title_sm"  => "服务器",
                "en"    => "servers",
                "icon"  => "fa fa-server",
                "level" => 1,
                "href"  => esaurl('index.servers/index'),
            ],
            [
                "id"    => 2,
                "fid"   => 0,
                "name"  => "sites",
                "type"  => "system",
                "title" => "站点管理",
                "title_sm"  => "站点",
                "en"    => "sites",
                "icon"  => "fa fa-sitemap",
                "level" => 1,
                "href"  => esaurl('index.sites/index'),
            ],
            [
                "id"    => 3,
                "fid"   => 0,
                "name"  => "configs",
                "type"  => "system",
                "title" => "站点配置管理",
                "title_sm"  => "配置",
                "en"    => "configs",
                "icon"  => "fa fa-tags",
                "level" => 1,
                "href"  => esaurl('index.configs/index'),
            ]
        ];
    }
}