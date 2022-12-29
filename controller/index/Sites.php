<?php
namespace addons\qubit_bt_manager\controller\index;

use addons\qubit_bt_manager\library\BTAPI;
use esa\Random;

class Sites extends Base
{
    protected $UnCheck = ["next"];
    public function index()
    {
        if($this->request->isAjax()){
            $model = $this->model("sites");
            return $model->where("server_id",$this->server_id)->where("uid",$this->auth->id)->order("id desc")->where("delete_time",0)->paginate($this->request->param("limit"));
        }
        return $this->fetch();
    }
    
    public function get_count()
    {
        $model = $this->model("sites");
        $total = $model->where("server_id",$this->server_id)->where("uid",$this->auth->id)->where("delete_time",0)->count();
        $wait = $model->where("server_id",$this->server_id)->where("uid",$this->auth->id)->where("delete_time",0)->where("siteStatus",0)->count();
        return $this->success("获取成功","",[$total,$wait]);
    }
    
    public function form()
    {
        $model = $this->model("sites",false);
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $status = "添加";
            
            $data = $this->decode_website($param['sites_text']);
            $time = time();
            foreach ($data as $k => $v){
                $data[$k]['pfid'] = PLATFORM_ID;
                $data[$k]['uid']   = $this->auth->id;
                $data[$k]['config_id'] = $param['config_id'];
                $data[$k]['server_id'] = $this->server_id;
                $data[$k]['create_time'] = $time;
            }
            $count = count($data);
            if($model->insertAll($data)){
                return $this->success("成功添加{$count}个站点数据","");
            }
            return $this->error("添加失败","");
        }
        $info = $model->where("uid",$this->auth->id)->where("id",$this->request->param("id"))->find();
        $this->assign("info", $info);
        return $this->fetch();
    }
    
    public function delete(){
        if($this->model("sites")->where("uid",$this->auth->id)->where("id",$this->request->param("id"))->update(["delete_time"=>time()])){
            return $this->success("删除成功","");
        }else{
            return $this->error("删除失败","");
        }
    }
    
    public function decode_website($text){
        $text = str_replace(["\r"," "],"",$text);
        $text = explode("\n",$text);
        $res = [];
        foreach ($text as $v){
            $site = explode(";",$v);
            if($site[0] == ""){
                continue;
            }
            $webs = explode(",",$site[0]);
            $web = explode(":",$webs[0]);
            $web_info['domain'] = $web[0];
            $web_info['port'] = 80;
            // 判断第一个是否存在端口配置
            if(count($web) == 2){
                $web_info['port'] = $web[1];
            }
            $web_info['domain_list'] = [];
            if(count($webs) > 1){
                foreach (array_slice($webs,1) as $v2){
                    $web_info['domain_list'][] = $v2;
                }
            }
            
            $web_info['source_path'] = "";
            $web_info['path'] = "";
            if(count($site) == 2){
                // 存在目标目录设置
                $paths = explode(":",$site[1]);
                $web_info['source_path'] = $paths[0];
                if(count($paths) == 2){
                    $web_info['path'] = $paths[1];
                }
            }
            
            $res[] = $web_info;
        }
        return $res;
    }
    
    public function get_ids()
    {
        $model = $this->model("sites");
        $ids = $model->where("server_id",$this->server_id)->where("uid",$this->auth->id)->where("delete_time",0)->where("siteStatus",0)->column("id");
        if(count($ids) == 0){
            return $this->error("无待创建数据","",[]);
        }
        return $this->success("获取成功","",$ids);
    }
    
    public function next(){
        // 判定用户会员是否到期
        if (empty($this->user)) {
            $this->error("用户信息不存在","");
        }
        if($this->user['vip_end_time'] < time()){
            if($this->user['num'] <= 0){
                return $this->error("建站数量不足，请在首页购买相关产品！");
            }else{
                $this->model("users")->where("id",$this->user['id'])->setDec("num");
            }
        }
        $where = [
            "uid"   => $this->auth->id,
            "delete_time"   => 0,
            "siteStatus"    => 0,
        ];
        $model = $this->model("sites",false)->with("Config")->where($where)->order("id asc")->get($this->request->param("id"));

        $config = $model['config'];
        if(empty($model)){
            return $this->error("站点信息不正确！","");
        }
        $www = [];
        if($config['www_status']){
            $www = ["www.".$model['domain']];
        }
        $base_path = $this->server_info['sites_path'];
        if($config['base_path_status']){
            $base_path = $config['base_path'];
        }
        $webname = [
            "domain"    => $model['domain'],
            "domainlist"=> array_merge($model['domain_list'],$www),
            "count"     => count($model['domain_list']),
        ];
        $site_path = !empty($model['path']) ? $model['path'] : $base_path."/".$model['domain'];
        if(!empty($config['path_status'])){
            $site_path = $config['target_path'];
        }
        $sql_user = empty($config['sql_status']) ? "" : substr(str_replace(".","_",$model['domain']),0,16);
        $sql_password = empty($config['sql_status']) ? "" : Random::alnum(8);
        $ftp_user = empty($config['ftp_username']) ? "" : substr(str_replace(".","_",$model['domain']),0,16);
        $ftp_password = empty($config['ftp_password']) ? "" : Random::alnum(8);
        // 创建相关站点
        $site_info = [
            "webname"       => json_encode($webname),
            // 根目录
            "path"          => $site_path,
            // 分类
            "type_id"       => empty($config['web_type']) ? 0 : $config['web_type'],
            // 项目类型
            "type"          => "PHP",
            // php版本
            "version"       => empty($config['php_version']) ? "00" : $config['php_version'],
            // 端口
            "port"          => $model['port'],
            // 备注
            "ps"            => "",
            // ftp
            "ftp"           => empty($config['ftp_status']) ? false : $config['ftp_status'],
            // ftp用户名
            "ftp_username"  => $ftp_user,
            // ftp密码
            "ftp_password"  => $ftp_password,
            // sql
            "sql"           => empty($config['sql_status']) ? false : "MySQL",
            // 数据库编码
            "codeing"       => empty($config['sql_codeing']) ? "" : $config['sql_codeing'],
            // 数据库账号
            "datauser"      => $sql_user,
            // 数据库密码
            "datapassword"  => $sql_password,
        ];
        
        $site_res = $this->BT->CreateSite($site_info);
        
        if(empty($site_res)){
            return $this->error($model['domain']."建站失败","");
        }
        
        if(isset($site_res['status']) && $site_res['status'] == false){
            return $this->error($model['domain']."建站失败<br>".(isset($site_res['msg'])?$site_res['msg']:""),"");
        }
        
// $site_res
// array(4) {
//   ["siteStatus"] => bool(true)
//   ["siteId"] => int(1)
//   ["ftpStatus"] => bool(false)
//   ["databaseStatus"] => bool(false)
// }

        $model->siteStatus = $site_res['siteStatus'];
        $model->siteId = $site_res['siteId'];
        $model->ftpStatus = $site_res['ftpStatus'];
        $model->databaseStatus = $site_res['databaseStatus'];
        $model->databaseUser = $site_info['datauser'];
        $model->databasePass = $site_info['datapassword'];
        
        // 清除默认文件
        if(!empty($config['clear_status'])){
            $clear_info = [
                "path"  => $site_path."/index.html",
            ];
            $clear_res_1 = $this->BT->DeleteFile($clear_info);
            
            $clear_info = [
                "path"  => $site_path."/404.html",
            ];
            $clear_res_2 = $this->BT->DeleteFile($clear_info);
            
            if(empty($clear_res_1) || empty($clear_res_2) || (isset($clear_res_1['status']) && $clear_res_1['status'] == false) || (isset($clear_res_2['status']) && $clear_res_2['status'] == false)){
                $model->clearStatus = 2;
            }else{
                $model->clearStatus = 1;
            }
            if(!empty($clear_res_1['msg'])){
                $model->clearResult .= " index.html:".$clear_res_1['msg'];
            }
            if(!empty($clear_res_2['msg'])){
                $model->clearResult .= " 404.html:".$clear_res_2['msg'];
            }
        }
        
        // 文件复制及修改
        $copy_file = [];
        if(!empty($model["source_path"])) {
            $copy_file = [
                "sfile" => $model['source_path'],      // 源文件目录
                "dfile" => $model['path']."/".$model['domain']."/",      // 目标目录
            ];
        }
        if(!empty($config['source_copy'])){
            $copy_file = [
                "sfile" => $config['source_path'],      // 源文件目录
                "dfile" => $model['path']."/".$model['domain']."/",      // 目标目录
            ];
        }
        if(!empty($copy_file)){
// array(2) {
//   ["status"] => bool(true)
//   ["msg"] => string(19) "目录复制成功!"
// }
            $copy_res = $this->BT->CopyFile($copy_file);
            if(empty($copy_res) || (isset($copy_res['status']) && $copy_res['status'] == false)){
                $model->copyStatus = 2;
            }else{
                $model->copyStatus = 1;
            }
            if(!empty($copy_res['msg'])){
                $model->copyResult = $copy_res['msg'];
            }
        }
        // ssl创建
        if(!empty($config['ssl_status'])){
            $ssl_info = [
                "siteName"  => $model['domain'],
                "email"     => $config['ssl_email'],
                "updateOf"  => 1,
                "domains"   => json_encode(array_merge([$model['domain']],$model['domain_list'])),
                "force"     => false,
            ];
            $ssl_res = $this->BT->CreateLet($ssl_info);
            if(empty($ssl_res) || (isset($ssl_res['status']) && $ssl_res['status'] == false)){
                $model->sslStatus = 2;
            }else{
                $model->sslStatus = 1;
            }
            if(!empty($ssl_res['msg'])){
                $model->sslResult = $ssl_res['msg'];
            }
        }
        
        
        // 设置伪静态
        if(!empty($config['rewrite_status'])){
            if($this->server_info['backup_path'] == "win"){
                $rewrite_info = [
                    "siteName"  => $model['domain'],
                    "data"      => $config['rewrite_config'],
                ];
                $rewrite_res = $this->BT->HttpPostCookie("site?action=SetSiteRewrite",$rewrite_info);
            }else{
                $rewrite_info = [
                    "path"  => "/www/server/panel/vhost/rewrite/".$model['domain'].".conf",
                    "data"  => $config['rewrite_config'],
                    "encoding"   => "utf-8",
                ];
// {"status": true, "msg": "文件已保存!", "historys": ["1671861203", "1671861187"], "st_mtime": "1671861215"}
                $rewrite_res = $this->BT->SaveFileBody($rewrite_info);
            }
            if(empty($rewrite_res) || (isset($rewrite_res['status']) && $rewrite_res['status'] == false)){
                $model->rewriteStatus = 2;
            }else{
                $model->rewriteStatus = 1;
            }
            if(!empty($rewrite_res['msg'])){
                $model->rewriteResult = $rewrite_res['msg'];
            }
        }
        
        
        $model->update_time = time();
        
        if($model->save()){
            return $this->success("处理{$model->id}成功","");
        }else{
            return $this->error("处理{$model->id}失败","");
        }
        
    }
    
}