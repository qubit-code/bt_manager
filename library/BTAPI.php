<?php
namespace addons\qubit_bt_manager\library;

class BTAPI {
    protected static $instance = null;
	private $BT_KEY = "";  //接口密钥
  	private $BT_PANEL = "http://www.test.com:1234/";	   //面板地址
	
  	//如果希望多台面板，可以在实例化对象时，将面板地址与密钥传入
	public function __construct($bt_panel = null,$bt_key = null){
		if($bt_panel) $this->BT_PANEL = $bt_panel;
		if($bt_key) $this->BT_KEY = $bt_key;
	}

	// public function apis($type){
	// 	$list = [
	// 		"sites"	=> "/data?action=getData&table=sites"
	// 	];
	// 	return $list[$type];
	// }

	// 获取网站列表
	public function GetSites(Type $var = null)
	{
		$url = 'data?action=getData&table=sites';
		// $p_data = $this->GetKeyData();
		return $this->HttpPostCookie($url);
		return $result;
	}

	// 获取宝塔配置信息
	public function GetConfig()
	{
		$url = 'config?action=get_config';
		return $this->HttpPostCookie($url);
	}

	// 获取站点分类
	public function GetSiteTypes()
	{
		$url = 'site?action=get_site_types';
		return $this->HttpPostCookie($url);
	}

	// 获取站点信息
	public function GetSite()
	{
		$url = 'data?action=getData';
		return $this->HttpPostCookie($url);
	}

	// 获取php版本
	public function GetPHPVersion()
	{
		$url = 'site?action=GetPHPVersion';
		return $this->HttpPostCookie($url);
	}

	// 创建站点
	public function CreateSite($data)
	{
		// exit(var_dump($data));
		$url = 'site?action=AddSite';
		return $this->HttpPostCookie($url,$data);
	}
	
	// 创建ssl
	public function CreateLet($data)
	{
		$url = 'site?action=CreateLet';
		return $this->HttpPostCookie($url,$data);
	}
	
	// 强制ssl
	public function HttpToHttps($data)
	{
		$url = 'site?action=HttpToHttps';
		return $this->HttpPostCookie($url,$data);
	}

	public function SetSshKey()
	{
		$data = ["ssh"=>"yes","type"=>"rsa"];
		$url = '/password?action=SetSshKey';
		return $this->HttpPostCookie($url,$data);
	}

	public function GetDir($data)
	{
		// $data = ['path'=>"/"];
		$url = '/files?action=GetDir&tojs=GetFiles&p=1&showRow=200';
		return $this->HttpPostCookie($url,$data);
	}

	public function GetFileBody($data)
	{
		// $data = ['path'=>"/test.txt"];
		$url = 'files?action=GetFileBody';
		return $this->HttpPostCookie($url,$data);
	}

	public function CreateFile($data)
	{
		// $data = ['path'=>"/test.txt"];
		$url = 'files?action=CreateFile';
		return $this->HttpPostCookie($url,$data);
	}

	public function SetBatchData($data)
	{
		$url = 'files?action=SetBatchData';
		return $this->HttpPostCookie($url,$data);
	}

	public function DeleteFile($data)
	{
		// $data = ['path'=>"/www/wwwroot/blog.takeup.me/1"];
		$url = 'files?action=DeleteFile';
		return $this->HttpPostCookie($url,$data);
	}

	// 复制目录
	public function CopyFile($data)
	{
		$url = 'files?action=CopyFile';
		return $this->HttpPostCookie($url,$data);
	}

	// 获取配置信息
	public function get_config()
	{
		$url = 'config?action=get_config';
		return $this->HttpPostCookie($url);
	}
	
	// 写入文件
	public function SaveFileBody($data)
	{
		// $data = [
		// 	"data"  => "test",
		// 	"path"  => "/test.txt",
		// 	"encoding"  => "utf-8"
		// ];
		$url = 'files?action=SaveFileBody';
		return $this->HttpPostCookie($url,$data);
	}

  	//示例取面板日志
	public function GetLogs(){
		//拼接URL地址
		$url = $this->BT_PANEL.'/data?action=getData';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['table'] = 'logs';
		$p_data['limit'] = 10;
		$p_data['tojs'] = 'test';
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}

	public function check(){
		return $this->GetKeyData();
	}
	
	
  	/**
     * 构造带有签名的关联数组
     */
  	private function GetKeyData(){
  		$now_time = time();
    	$p_data = array(
			'request_token'	=>	md5($now_time.''.md5($this->BT_KEY)),
			'request_time'	=>	$now_time
		);
    	return $p_data;    
    }
  	
  
  	/**
     * 发起POST请求
     * @param String $url 目标网填，带http://
     * @param Array|String $data 欲提交的数据
     * @return string
     */
    private function HttpPostCookie2($url, $data,$timeout = 60)
    {
    	//定义cookie保存位置
        $cookie_file=__DIR__.'/tmp/'.md5($this->BT_PANEL).'.cookie';
        if(!file_exists($cookie_file)){
            $fp = fopen($cookie_file,'w+');
            fclose($fp);
        }
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
	}
	
	/**
     * 发起POST请求
     * @param String $url 目标网填，带http://
     * @param Array|String $data 欲提交的数据
     * @return string
     */
    public function HttpPostCookie($url, $data=array(),$timeout = 60)
    {
    	//定义cookie保存位置
        $cookie_file=__DIR__.'/tmp/'.md5($this->BT_PANEL).'.cookie';
        if(!file_exists($cookie_file)){
            $fp = fopen($cookie_file,'w+');
            fclose($fp);
		}
		if (!isset($data['request_toke'])) {
			$data = array_merge($data,$this->GetKeyData());
		}
// 		exit(var_dump($data));

		$ch = curl_init();
		$url = $this->BT_PANEL.$url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
		curl_close($ch);
// 		exit(var_dump($output));
        return json_decode($output,true);
    }
    
    public function HttpPostCookie3($url,$timeout = 3)
    {
    	//定义cookie保存位置
        $cookie_file=__DIR__.'/tmp/'.md5($this->BT_PANEL).'.cookie';
        if(!file_exists($cookie_file)){
            $fp = fopen($cookie_file,'w+');
            fclose($fp);
		}
		$data = $this->GetKeyData();
			
		$ch = curl_init();
		$url = $this->BT_PANEL.$url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
// 		exit(var_dump($output));
        return $code;
    }
}



//实例化对象
// $api = new bt_api();
// //获取面板日志
// $r_data = $api->GetLogs();
// //输出JSON数据到浏览器
// echo json_encode($r_data);

// /site?action=get_site_types  #获取分类
/*
获取伪静态设置 /files?action=GetFileBody
	类型 POST
	参数
	path: /www/server/panel/vhost/rewrite/dy.bug-maker.com.conf
设置伪静态 /files?action=SaveFileBody
	类型 POST
	参数
	path: /www/server/panel/vhost/rewrite/dy.bug-maker.com.conf
	data: if (!-e $request_filename)
	{
		#地址作为将参数rewrite到index.php上。
		rewrite ^/(.*)$ /index.php?s=$1;
		#若是子目录则使用下面这句，将subdir改成目录名称即可。
		#rewrite ^/subdir/(.*)$ /subdir/index.php?s=$1;
	}
	encoding: utf-8
*/

?>