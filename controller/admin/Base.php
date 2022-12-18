<?php
namespace addons\qubit_bt_manager\controller\admin;

use addons\qubit_bt_manager\Main;

class Base extends Main
{
    protected $ESA_TYPE     = "ADMIN";
    
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
        $this->assign("server_info",$this->cm_info);
        $this->checkauth();
    }
}