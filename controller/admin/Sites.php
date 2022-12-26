<?php
namespace addons\qubit_bt_manager\controller\admin;

class Sites extends Base
{
    public function index()
    {
        if($this->request->isAjax()){
            $model = $this->model("sites")->with(["server","sys_user"]);
            return $model->order("id desc")->paginate($this->request->param("limit"));
        }
        return $this->fetch();
    }
}