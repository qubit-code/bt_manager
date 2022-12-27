<?php
namespace addons\qubit_bt_manager\controller\index;

use addons\qubit_bt_manager\library\BTAPI;

class Servers extends Base
{
    protected $UnCheck = ["*"];
    public function index()
    {
        if($this->request->isAjax()){
            $model = $this->model("servers");
            return $model->where("uid",$this->auth->id)->order("id desc")->paginate($this->request->param("limit"));
        }
        return $this->fetch();
    }
    
    public function form()
    {
        $model = $this->model("servers",false);
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $status = "添加";
            if(!empty($param['id'])){
                $model = $model->isUpdate(true);
                $status = "编辑";
            }
            $param['pfid']  = PLATFORM_ID;
            $param['uid']   = $this->auth->id;

            $BT = new BTAPI($param['bt_panel'],$param['key']);
            $res = $BT->GetConfig();
            
            if($res == null){
                return $this->error("服务器不存在，请核对信息后添加！");
            }
            
            if(!empty($res) && $res['status'] == false){
                return $this->error($res['msg']);
            }

            if(empty($param['name'])){
                $param['name'] = $param['bt_panel'];
            }
            $param['status'] = $res['status'];
            $param['mysql_root'] = $res['mysql_root'];
            $param['backup_path'] = $res['backup_path'];
            $param['webserver'] = $res['webserver'];
            $param['sites_path'] = $res['sites_path'];
            $param['email'] = $res['email'];

            if($model->save($param)){
                return $this->success($status."成功","");
            }else{
                return $this->error($status."失败","");
            }
        }
        $info = $model->where("uid",$this->auth->id)->where("id",$this->request->param("id"))->find();
        $this->assign("server_ip", get_addon_config("basics.server_ip"));
        $this->assign("info", $info);
        return $this->fetch();
    }
    
    public function delete(){
        if($this->model("servers")->where("uid",$this->auth->id)->where("id",$this->request->param("id"))->delete()){
            return $this->success("删除成功","");
        }else{
            return $this->error("删除失败","");
        }
    }
    
    public function set(){
        $info = $this->model("servers",false)->where("id",$this->request->param("id"))->find();
        $BT = new BTAPI($info['bt_panel'],$info['key']);
        $config = $BT->GetConfig();

        if(empty($info) || $config == NULL){
            return $this->error("服务器不存在","");
        }
        
        if($config['status'] == false){
            return $this->error($config['msg']);
        }
        $pfid = PLATFORM_ID;
        session("server_id",$info['id']);
        return $this->success("成功选中".$info['name'].session("server_id"),"");
    }
}