<?php
namespace addons\qubit_bt_manager;

use ESA\AddonsHook;

class Hook extends AddonsHook
{
    // 支付回调
    function payResult($data){
        // file_put_contents(ADDONS_PATH."/qubit_bt_manager/payresult.txt",json_encode($data));
        if($data['from'] == "callback"){
            //$order = $data['order'];
            // $order = model("\addons\qubit_love\model\Order")->count();
            $order = model("\addons\qubit_bt_manager\model\Orders")->where("order_sn",$data['order']['order_sn'])->find();
            model("\addons\qubit_bt_manager\model\Orders")->where("order_sn",$data['order']['order_sn'])->update(["pay_time"=>time()]);

            
            $userInfo = model("\addons\qubit_bt_manager\model\Users")->where("pfid",$data['order']['pfid'])->where("uid",$data['order']['uid'])->find();
            $update = [
                'vip_end_time' => time() + $order['day'] * 24 * 60 * 60,
                'num'   => $userInfo['num'] + $order['num'],
            ];
            if($userInfo['vip_end_time'] > time()){
                $update['vip_end_time'] = $userInfo['vip_end_time'] + $order['day'] * 24 * 60 * 60;
            }
            model("\addons\qubit_bt_manager\model\Users")->where("pfid",$data['order']['pfid'])->where("uid",$data['order']['uid'])->update($update);
        }
    }
}