<?php
namespace addons\qubit_bt_manager\controller\index;

class Index extends Base
{
    protected $EXPOSURE = ['pay_result'];
    protected $UnCheck = ["*"];
    public function index()
    {
        $this->site_title = get_addon_config("basics.site_name");
        $this->ESA_CONFIG['menu'] = $this->index_menu();
        $this->ESA_CONFIG['site']['name'] = $this->site_title;
        $this->ESA_CONFIG['jsname'] = "/static/template/{$this->ESA_CONFIG['template']}/index/index";
        $this->assign("dashboard",esaurl("index.index/dashboard"));

        return $this->fetch("index");
    }
    
    public function dashboard()
    {
        // exit(dump($this->BT->CopyFile($copy_file)));
        $pays = $this->model("pays")->select();
        $this->assign("pays", $pays);
        return $this->fetch();
    }
    
    public function statistics()
    {
        $server_num = $this->model("servers")->where("uid",$this->auth->id)->count();
        $site_num = $this->model("sites")->where("uid",$this->auth->id)->count();
        return $this->success("获取成功","",["server_num"=>$server_num, "site_num"=>$site_num]);
    }
    
    public function article()
    {
        return $this->model("articles")->order("sort desc")->paginate($this->request->param("limit"));
    }
    
    public function article_detail()
    {
        $model = $this->model("articles")->get($this->request->param("id"));
        $model->setInc("view_num");
        $this->assign("info",$model);
        return $this->fetch();
    }
    
    public function pay(){
        // 保证调用支付的时候不会将菜单处理为空
        $this->ESA_CONFIG['menu'] = $this->index_menu();
        $id = $this->request->param("id");
        $info = $this->model("pays")->get($id);
        
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
            return $this->payment(["title"=>$name,"fee"=>$money,"order_sn"=>$order_sn,"callback"=>esaurl('index.index/index',null,"",true)]);
        }else{
            return $this->error("下单失败");
        }
    }
    
    public function pay_result(){
        $this->success("支付成功，等待跳转至首页！",esaurl('index.index/dashboard'));
    }
}