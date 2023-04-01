<?php
namespace addons\qubit_bt_manager\controller\index;

use addons\qubit_bt_manager\library\BTAPI;

class Configs extends Base
{
    public function index()
    {
        if($this->request->isAjax()){
            $model = $this->model("configs");
            return $model->where("server_id",$this->server_id)->where("uid",$this->user->id)->order("id desc")->paginate($this->request->param("limit"));
        }
        return $this->fetch();
    }
    
    public function form()
    {
        $model = $this->model("configs",false);
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $status = "添加";
            if(!empty($param['id'])){
                $model = $model->isUpdate(true);
                $status = "编辑";
            }
            $param['pfid']  = PLATFORM_ID;
            $param['uid']   = $this->user->id;
            $param['server_id'] = $this->server_id;
            $base = [
                "ssl_status"    => 0,
                "base_path_status"  => 0,
                "path_status"   => 0,
                "source_copy"   => 0,
                "sql_status"    => 0,
                "rewrite_status"=> 0,
            ];
            if($model->save(array_merge($base,$param))){
                return $this->success($status."成功","");
            }else{
                return $this->error($status."失败","");
            }
        }
        $this->assign("web_types",$this->BT->GetSiteTypes());
        $this->assign("php_versions",$this->BT->GetPHPVersion());
        $info = $model->where("uid",$this->user->id)->where("id",$this->request->param("id"))->find();
        $this->assign("info", $info);
        return $this->fetch();
    }
    
    public function delete(){
        if($this->model("configs")->where("server_id",$this->server_id)->where("uid",$this->user->id)->where("id",$this->request->param("id"))->delete()){
            return $this->success("删除成功","");
        }else{
            return $this->error("删除失败","");
        }
    }
    
    public function set(){
        $info = $this->model("configs",false)->where("id",$this->request->param("id"))->find();
        if(empty($info)){
            return $this->error("服务器不存在","");
        }
        $pfid = PLATFORM_ID;
        session("server_id",$info['id']);
        return $this->success("成功选中".$info['name'].session("server_id"),"");
    }
}