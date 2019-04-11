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

class Mine extends Controller
{
    public function index()
    {

        return $this->fetch('');
    }
    public function about()
    {
        return $this->fetch('');
    }
    public function contact()
    {
        return $this->fetch('');
    }
    public function info()
    {
        $user = Db::name('user')->where('openid', session('openid'))->find();
        $this->assign('user', $user);
        return $this->fetch('');
    }
    public function records()
    {
        return $this->fetch('');
    }
    public function get_info(){
        $info = Db::name('user')->alias('a')
        ->join('car b', 'b.user_id=a.id','LEFT')
        ->join('order c', 'c.real_name=a.real_name','LEFT')
        ->field('a.*,b.num,c.car_num,c.type,c.rank')
        ->where('a.openid', session('openid'))->select();
        return $info;
    }
    public function get_records(){
        $order=Db::name('order')
        ->alias('a')
        ->join('user b', 'b.real_name=a.real_name')
        ->field('a.*')
        ->where('b.openid', session('openid'))->order('rank')->select();
        foreach ($order as $key => $value) {
            $order[$key]['create_time']=date('Y-m-d',$value['create_time']);
        }
        return $order;
    }

    public function edit_user()
    {

        $user['real_name'] = input('post.real_name');
        $user['phone'] = input('post.phone');
        $user['company'] = input('post.company');
        $car['num'] = input('post.num');
     
        Db::name('user')->where('openid', session('openid'))->update($user);
        $data = Db::name('car')->alias('a')
            ->join('user b', 'a.user_id=b.id')
            ->where('b.openid', session('openid'))->select();
        if ($data && $data != '') {
            $id=Db::name('user')->where('openid', session('openid'))->find();
            $id = $id['id'];
            Db::name('car')->where('user_id',$id)->setField('num',$car['num']);
        } else {
            $car['user_id'] = Db::name('user')->where('openid',session('openid'))->find();
            $car['user_id'] = $car['user_id']['id'];
            $car['create_time'] = time();
            Db::name('car')->insert($car);
        }
        return '添加司机信息成功';
    }
}
