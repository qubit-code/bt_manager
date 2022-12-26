<?php
namespace addons\qubit_bt_manager\controller\admin;

class Users extends Base
{
    public function index()
    {
        if($this->request->isAjax()){
            $model = $this->model("users")->with("sys_user");
            return $model->order("id desc")->paginate($this->request->param("limit"))->each(function($d){
                $d['sys_user']['avatar'] = attach2url($d['sys_user']['avatar']);
                $d['vip_end_time'] = date("Y-m-d H:i:s",$d['vip_end_time']);
                return $d;
            });
        }
        return $this->fetch();
    }
    
    public function form(){
        $model = $this->model("users", false);
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $status = "添加";
            if(!empty($param['id'])){
                $model = $model->isUpdate(true);
                $status = "编辑";
            }
            $param['vip_end_time'] = strtotime($param['vip_end_time']);
            if($model->save($param)){
                return $this->success($status."成功","");
            }else{
                return $this->error($status."失败","");
            }
        }
        $info = $model->where("id",$this->request->param("id"))->find();
        $this->assign("info", $info);
        return $this->fetch();
    }
    
    public function delete(){
        if($this->model("users")->where("id",$this->request->param("id"))->delete()){
            return $this->success("删除成功","");
        }else{
            return $this->error("删除失败","");
        }
    }
}