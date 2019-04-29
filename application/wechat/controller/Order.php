<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 9:39
 */

namespace app\wechat\controller;

use think\Controller;
use think\Db;

class Order extends Controller
{
    public function index()
    {
        $user = Db::name('user')->where('openid', session('openid'))->find();
        $type = Db::name('good_type')->where('status', 1)->field('id,name')->select();
        $this->assign(['user' => $user, 'type' => $type]);
        return $this->fetch('index');
    }

    // 获取商品名称
    public function get_good()
    {
        $type_id = input('post.type');
        $good = Db::name('good')->alias('a')
            ->join('good_type b', 'a.type=b.id')
            ->where('b.id', $type_id)->column('a.name');
        return $good;
    }

    // 默认信息
    public function defult_input()
    {
        $info = Db::name('user')->alias('a')
            ->join('order b', 'b.real_name=a.real_name')
            ->order('b.id desc')
            ->field('a.real_name,a.phone,a.company,b.car_num,b.status,b.type')->where('a.openid', session('openid'))->select();
        return $info;
    }
    // 提交预约
    public function submit_order()
    {
        $data['order_id'] = rand(00000001, 99999999);
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['real_name']=input('post.real_name');
        $data['car_num']=input('post.car_num');
        $data['company']=input('post.company');
        $data['good_name']=input('post.good_name');
        $data['phone']=input('post.phone');
        $sum=Db::name('order')->where('type','in','1,2')->count();
        $data['rank']=$sum+1;
        if($data['rank'] == 1){
            $data['type']=2;
        }else{
            $data['type']=1;
        }
        Db::name('order')->insert($data);
        return '预约成功!';

    }

}
