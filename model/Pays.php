<?php
namespace addons\qubit_bt_manager\model;

use think\Model;

class Pays extends Model {
    // 设置数据表（不含前缀）
    protected $name = 'qubit_bt_manager_pay';

    // 开启时间自动写入
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 自动完成
    protected $auto       = [];
    protected $insert     = [];
    protected $update     = [];
}