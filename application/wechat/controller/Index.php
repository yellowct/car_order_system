<?php


namespace app\wechat\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $notice = Db::name('notice')->value('content');
        $type = Db::name('good_type')->where('status', 1)->field('id,name')->select();
        $this->assign(['notice' => $notice, 'type' => $type]);
        return $this->fetch('');
    }
    public function get_order()
    {
        $map = [];
        //获取查询字段
        $good_name = input('post.good_name');
        $search_data=input('post.search_data');
        if ($good_name && $good_name !== "") {
            $map['good_name'] = $good_name;
        }
        if ($search_data && $search_data !== "") {
            $map['real_name|company|car_num|phone'] = ['like', "%" . $search_data . "%"];
        }
        // 获取预约记录(限定4条)
        $order = Db::name('order')
            ->where($map)
            ->where('type', 'in', '1,2')
            ->order('rank')->limit(4)->select();
        return $order;
    }
}
