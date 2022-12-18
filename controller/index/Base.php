<?php
namespace addons\qubit_bt_manager\controller\index;

use addons\qubit_bt_manager\Main;
use addons\qubit_bt_manager\library\BTAPI;

class Base extends Main
{
    protected $ESA_TYPE     = "INDEX";
    protected $BT = null;
    
    public function __construct()
    {
        parent::__construct();
        
        $server_id = session("server_id");
        if(empty($server_id)){
            $this->server_info = $this->model("servers")->find();
            $this->server_id = !empty($this->server_info) ? $this->server_info['id'] : 0;
        }else{
            $this->server_id = $server_id;
            $this->server_info = $this->model("servers")->where("id",$this->server_id)->find();
        }
        
        if(!empty($this->server_info)){
            $this->BT = BTAPI::instance($this->server_info['bt_panel']."/",$this->server_info['key']);
        }
        
        $this->assign("server_info",$this->server_info);
        $this->checkauth();
    }
}