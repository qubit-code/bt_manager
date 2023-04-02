<?php
namespace addons\qubit_bt_manager\controller\admin;

use addons\qubit_bt_manager\Main;

class Base extends Main
{
    protected $ESA_TYPE     = "ADMIN";
    
    public function __construct()
    {
        parent::__construct();
        
        $this->checkauth();
        $this->assign("esa_addon", $this->ESA_ADDON);
    }
}