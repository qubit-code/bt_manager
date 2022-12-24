<?php
namespace addons\qubit_bt_manager\controller\admin;

class Pay extends Base
{
    public function index()
    {
        if($this->request->isAjax()){
            $model = $this->model("pay");
            return $model->order("id desc")->paginate($this->request->param("limit"))->each(function($d){
                $d['image'] = attach2url($d['image']);
                return $d;
            });
        }
        return $this->fetch();
    }
    
    public function form(){
        $model = $this->model("pay", false);
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $status = "添加";
            if(!empty($param['id'])){
                $model = $model->isUpdate(true);
                $status = "编辑";
            }
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
        if($this->model("pay")->where("id",$this->request->param("id"))->delete()){
            return $this->success("删除成功","");
        }else{
            return $this->error("删除失败","");
        }
    }
}