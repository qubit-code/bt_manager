<?php
namespace addons\qubit_bt_manager\controller\index;

class Index extends Base
{
    protected $EXPOSURE = ['pay_result'];
    protected $UnCheck = ["*"];
    public function index()
    {
        $this->site_title = "宝塔管理器";
        $this->ESA_CONFIG['menu'] = $this->index_menu();
        $this->ESA_CONFIG['site']['name'] = $this->site_title;
        $this->ESA_CONFIG['jsname'] = "/static/template/{$this->ESA_CONFIG['template']}/index/index";
        $this->assign("dashboard",esaurl("index.index/dashboard"));

        return $this->fetch("index");
    }
    
    public function dashboard()
    {
        // exit(dump($this->BT->CopyFile($copy_file)));
        $pays = $this->model("pay")->select();
        $this->assign("pays", $pays);
        return $this->fetch();
    }
    
    public function article()
    {
        return $this->model("articles")->paginate($this->request->param("limit"));
    }
    
    public function pay(){
        $id = $this->request->param("id");
        $info = $this->model("pay")->get($id);
        
        if(empty($info)){
            return $this->error("商品不存在！");
        }
        $order_sn = date("YmdHi",time()).\esa\Random::numeric(12);
        $name = $info['name'];
        $money = $info['fee'];
        $order = [
            "pfid"  => PLATFORM_ID,
            "uid"   => $this->auth->id,
            "order_sn"  => $order_sn,
            "fee"   => $info['fee'],
            "day"   => $info['day'],
            "num"   => $info['num'],
            "create_time"   => time(),
        ];
        if($this->model("orders",false)->insert($order)){
            return $this->payment(["title"=>$name,"fee"=>$money,"order_sn"=>$order_sn,"callback"=>esaurl('index.index/pay_result',null,"",true)],'');
        }else{
            return $this->error("下单失败");
        }
    }
    
    public function pay_result(){
        $this->success("支付成功，等待跳转至首页！",esaurl('index.index/dashboard'));
    }
}