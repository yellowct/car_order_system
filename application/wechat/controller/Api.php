<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 9:37
 */

namespace app\wechat\controller;

use think\Controller;
use think\Db;
class Api extends Controller
{
    /**
     * 微信配置验证url
     * @return bool
     */

    
public function index(){
    //查看用户是否授权登录过 没有的话跳转到授权登录页面
    if(empty(session('openid'))) {
        $this->login();
    }else{
        $this->redirect("index/index");
    }
}
    public function login()
    {
        $appid = "wx0d1a53ea370268f3";
        $redirect_uri = "http://chute.wero6.com/hct/wechat/api";
        $scope = "snsapi_userinfo";
        $state = "hct";
        $secret = "d5ddf46fce2472028731efcfef96e3ee";

//        第一步：获取code
        $web_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
        if (!empty($_GET['code'])) {
            $code = $_GET['code'];

            $state = $_GET['state'];
            if ($state = "hct") {
                //        第二步：通过code换取网页授权access_token
                $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
                $ret = $this->request_get($token_url);
                $json_token = json_decode($ret, true);
                $access_token = $json_token['access_token'];
                $openid = $json_token['openid'];
                session('openid',$openid);  
                // 第三步：通过access_token和openid拉取用户信息
                $user_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";

                $user = $this->request_get($user_url);
                $user_info = json_decode($user, true);

                $data=['openid'=>$user_info['openid'],'nickname'=>$user_info['nickname'],'headimgurl'=>$user_info['headimgurl']];
                $sql=Db::name('user')->where('openid',$openid)->find();
                if($sql){
                    $data['update_time']=time();
                    Db::name('user')->where('openid',$openid)->update($data);
                }else{
                    $data['create_time']=time();
                    Db::name('user')->insert($data);
                }
                $this->redirect("index/index");
            } else {
                echo "state错误";
            }
        } else {
            $this->redirect("$web_url");
        }
    }
    public function get_access_token()
    {
        // 获取access_token
        $appid = "wx0d1a53ea370268f3";
        $secret = "d5ddf46fce2472028731efcfef96e3ee";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
        if (session('expires_in') < time()) {
            $data = $this->request_get($url);
            $json_data = json_decode($data, true);
            $access_token = $json_data['access_token'];
            $expires_in = $json_data['expires_in'];
            session('access_token', $access_token);
            session('expires_in', time() + $expires_in);
        } else {
            $access_token = session('access_token');
            $expires_in = session('expires_in');
        }
        dump($access_token);
    }
    // public function menu(){
    //     // 自定义菜单
    //     $menu_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
    //     $menu_data = '{
    //         "button":[
    //             {
    //                  "type":"view",
    //                  "name":"排队查询",
    //                  "url":"http://119.23.215.238/hct/wechat/index"
    //              },
    //              {
    //                 "type":"view",
    //                 "name":"预约装车",
    //                 "url":"http://119.23.215.238/hct/wechat/order"
    //             },
    //             {
    //                 "type":"view",
    //                 "name":"个人中心",
    //                 "url":"http://119.23.215.238/hct/wechat/mine"
    //             }]

    //     }';
    //     $menu = $this->request_post($menu_url, $menu_data);
    //     var_dump($menu);
    // }
    public function request_get($url)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
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
    public function request_post($url = '', $data = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
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
