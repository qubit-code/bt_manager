<?php
namespace addons\qubit_bt_manager\model;

use think\Model;

class Sites extends Model {
    // 设置数据表（不含前缀）
    protected $name = 'qubit_bt_manager_sites';

    // 开启时间自动写入
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 自动完成
    protected $auto       = [];
    protected $insert     = [];
    protected $update     = [];
    
    protected $json=['domain_list'];
    
    public function Server(){
        return $this->hasOne("addons\qubit_bt_manager\model\Servers","id","server_id");
    }
    
    public function Config(){
        return $this->hasOne("addons\qubit_bt_manager\model\Configs","id","config_id");
    }
    
    public function sysUser(){
        return $this->hasOne("app\common\model\User","id","uid");
    }
}