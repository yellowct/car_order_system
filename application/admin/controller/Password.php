<?php


namespace app\admin\controller;
use think\Controller;

class Password extends Controller
{
  public function index(){
     return $this->fetch('index');
  }

  public function update(){
    $old=md5(input('post.oldpwd'));
    $account=session('account');
    $sql=db('admin')->where('account',$account)->find();
    if($sql['password']==$old){
        $password=md5(input('post.newpwd'));
        $sql=db('admin')->where('account',$account)->setField('password',$password);
        return true;
    }else{
        return false;
    }
}

}