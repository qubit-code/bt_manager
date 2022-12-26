<?php
namespace addons\qubit_bt_manager\controller\admin;

class Servers extends Base
{
    public function index()
    {
        if($this->request->isAjax()){
            $model = $this->model("servers")->with(["user","sys_user"]);
            return $model->order("id desc")->paginate($this->request->param("limit"));
        }
        return $this->fetch();
    }
}