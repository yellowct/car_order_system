<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/8
 * Time: 16:06
 */

namespace app\admin\controller;
use think\Controller;
use think\Db;

class Login extends Controller
{
    public function index(){
        return  $this->fetch('');
    }
    public function check(){
        $data = input('param.');
        $account=Db::name('admin')->where('account',$data['account'])->find();
        if(!$account){
            return json(['code'=>-1,'msg'=>'该账号不存在']);
        }else {
            $admin=Db::name('admin')->where(['account'=>$data['account'],'password'=>md5($data['password'])])->find();
        }
        if($admin){
            session('account',$admin['account']);
            session('login_time',date("Y-m-d H:i:s"));
            writeLog(session('account'),'登录','后台',1);
            return json(['code'=>1,'msg'=>'登录成功']);
        }else{
            return json(['code'=>0,'msg'=>'账号或密码错误']);
        }
    }

    public function logout(){
        session(null);
        return $this->fetch('index');
    }
}