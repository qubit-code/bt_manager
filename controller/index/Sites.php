<?php
namespace addons\qubit_bt_manager\controller\index;

use addons\qubit_bt_manager\library\BTAPI;

class Sites extends Base
{
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
        
        $webname = [
            "domain"    => $model['domain'],
            "domainlist"=> $model['domain_list'],
            "count"     => count($model['domain_list']),
        ];
        // 创建相关站点
        $site_info = [
            "webname"       => json_encode($webname),
            // 根目录
            "path"          => !empty($model['path']) ? $model['path'] : $this->server_info['sites_path']."/".$model['domain'],
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
            "ftp_username"  => empty($config['ftp_username']) ? "" : $config['ftp_username'],
            // ftp密码
            "ftp_password"  => empty($config['ftp_password']) ? "" : $config['ftp_password'],
            // sql
            "sql"           => empty($config['sql_status']) ? false : $config['sql_status'],
            // 数据库编码
            "codeing"       => empty($config['sql_codeing']) ? "" : $config['sql_codeing'],
            // 数据库账号
            "datauser"      => empty($config['sql_status']) ? "" : substr(str_replace(".","_",$model['domain']),0,16),
            // 数据库密码
            "datapassword"  => empty($config['sql_status']) ? "" : $config['ftp_password'],
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
        // 文件复制及修改
        $copy_file = [
            "sfile" => "",      // 源文件目录
            "dfile" => "",      // 目标目录
        ];
        // ssl创建
        $ssl_info = [
            "siteName"  => "",
            "email"     => "",
            "updateOf"  => 1,
            "domains"   => "",
            "force"     => false,
        ];
        // 设置伪静态
        $rewrite_info_linux = [
            "path"  => "",
            "data"  => "",
            "encoding"   => "utf-8",
        ];
        
        $rewrite_info_win = [
            "siteName"  => "",
            "data"      => "",
        ];
        
        $model->update_time = time();
        if($model->save()){
            return $this->success("处理{$model->id}成功","");
        }else{
            return $this->error("处理{$model->id}失败","");
        }
        
    }
    
}