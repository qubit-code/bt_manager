<?php
namespace addons\qubit_bt_manager;

use esa\AddonsHook;

class Hooks extends AddonsHook
{
    /**
     * 功能入口方法
     * @return bool
     */
    public function entrance(){
        $domain = get_platform_config("web.bind_domain");
        $domain = $domain == "" ? true : $domain;
        $res[] = [
            "title" => "客户端页面",
            "url"   => esaurl("index.index/index",[],".html",$domain)
        ];
        return $res;
    }
    // 支付回调
    public function payResult($data){
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