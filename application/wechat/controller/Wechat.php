<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 13:55
 */

namespace app\wechat\controller;
use think\Controller;

class Index extends Controller
{
//    public function index(){
//        $appid = "wx0d1a53ea370268f3";
//        $redirect_uri="http://yijiangbangtest.wsandos.com/hct/index.php/wechat";
//        $scope="snsapi_userinfo";
//        $state="hct";
//        $secret = "d5ddf46fce2472028731efcfef96e3ee";
////        第一步：获取code
//        $web_url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
////        第二步：通过code换取网页授权access_token
//        if(!empty($_GET['code'])){
//            $code=$_GET['code'];
//            $state=$_GET['state'];
//            if($state="hct"){
//                $token_url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
//                $ret=$this->request_get($token_url);
//                $json_token=json_decode($ret,true);
////                第三步：通过access_token和openid拉取用户信息
//                $access_token=$json_token['access_token'];
//                $openid=$json_token['openid'];
//                $user_url="https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
//                $user=$this->request_get($user_url);
//                var_dump(json_decode($user,true));
//            }else{
//                echo "state错误";
//            }
//        }else{
//            $this->redirect("$web_url");
//        }
//    }
    function request_get($url){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        //执行命令
        $data = curl_exec($curl);

        $fail = curl_error($curl);

        //关闭URL请求
        curl_close($curl);
        //显示获得的数据

        return $data;
    }
    public function home(){
        return $this->fetch('home');
    }
    public function index(){
        $appid = "wx0d1a53ea370268f3";
        $secret = "d5ddf46fce2472028731efcfef96e3ee";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
        if(session('expires_in')<time()){
            $data = $this->request_get($url);
            $json_data = json_decode($data,true);
            $access_token = $json_data['access_token'];
            $expires_in = $json_data['expires_in'];
            session('access_token',$access_token);
            session('expires_in',time()+$expires_in);
        }
        else{
            $access_token=session('access_token');
            $expires_in=session('expires_in');
        }

        $menu_url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $menu_data='{ 
          "button":[ 
          { 
           "name":"车辆预约系统", 
           "sub_button":[ 
           { 
            "type":"view", 
            "name":"首页", 
            "url":"http://yijiangbangtest.wsandos.com/hct/wechat/car"
           }, 
           { 
            "type":"view", 
            "name":"用户", 
            "url":"http://yijiangbangtest.wsandos.com/hct/tp5/index.php/index/user"
              }
            ]   
           }
          ] 
        }';
        $menu=$this->request_post($menu_url,$menu_data);
        var_dump($menu);
    }
    function request_post($url='',$data=''){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $tmpInfo;
    }
}