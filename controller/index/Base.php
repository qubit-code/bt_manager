<?php
namespace addons\qubit_bt_manager\controller\index;

use addons\qubit_bt_manager\Main;
use addons\qubit_bt_manager\library\BTAPI;

class Base extends Main
{
    protected $ESA_TYPE     = "INDEX";
    protected $BT = null;
    protected $server_info = null;
    protected $server_id = 0;
    protected $UnCheck = [];
    protected $userInfo = null;
    
    public function __construct()
    {
        parent::__construct();
        
        $server_id = session("server_id");
        if(!empty($server_id)){
            $this->server_id = $server_id;
            $this->server_info = $this->model("servers")->where("id",$this->server_id)->find();
        }
        
        if(!empty($this->server_info)){
            $this->BT = new BTAPI($this->server_info['bt_panel']."/",$this->server_info['key']);
        }
        
        $this->checkServer();
        $this->assign("server_id", $this->server_id);
        $this->assign("server_info",$this->server_info);
        $this->checkauth();
        $this->checkUser();
        $this->assign("user",$this->userInfo);
        $this->assign("config", $this->ADDON_CONFIG);
    }
    
    public function checkServer(){
        if ($this->UnCheck == "*" || $this->UnCheck == ["*"] || in_array($this->action,$this->UnCheck)) {
            return true;
        }
        
        if(empty($this->server_id) || empty($this->BT)){
            return $this->error("未设置处理服务器，请至【服务器管理】中设置服务器!","","",0);
        }
        
        $config = $this->BT->GetConfig();

        if($config == NULL){
            return $this->error("服务器不存在，请至【服务器管理】中重新添加并设置服务器","","",0);
        }
        
        if($config['status'] == false){
            return $this->error($config['msg']." 请至【服务器管理】中重新处理服务器并设置服务器","","",0);
        }
    }
    
    public function checkUser(){
        if ($this->EXPOSURE == "*" || $this->EXPOSURE == ["*"] || in_array($this->action,$this->EXPOSURE)) {
            return true;
        }
        $user = $this->model("users",false)->where("pfid",PLATFORM_ID)->where('uid',$this->user->id)->find();
        if(empty($user)){
            $new_user = [
                "pfid"  => PLATFORM_ID,
                "uid"   => $this->user->id,
                "num"      => get_config("basics.free_num"),
                "create_time"   => time()
            ];
            $id = $this->model("users",false)->insertGetId($new_user);
            $user = $this->model("users",false)->where("id",$id)->find();
        }
        $this->userInfo = $user;
    }
}