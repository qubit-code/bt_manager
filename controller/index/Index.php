<?php
namespace addons\qubit_bt_manager\controller\index;

class Index extends Base
{
    public function index()
    {
        $this->site_title = "宝塔管理器";
        $this->ESA_CONFIG['menu'] = $this->index_menu();
        $this->ESA_CONFIG['site']['name'] = $this->site_title;
        $this->ESA_CONFIG['jsname'] = "/static/template/{$this->ESA_CONFIG['template']}/index/index";
        $this->assign("dashboard",esaurl("index.index/dashboard"));

        return $this->fetch("index");
    }
    
    public function dashboard()
    {
        return $this->fetch();
    }
}