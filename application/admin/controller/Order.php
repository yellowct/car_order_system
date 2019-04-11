<?php

namespace app\Admin\controller;

use app\admin\model\OrderModel;
use think\Controller;
use think\Db;

class Order extends Controller
{
    public function index()
    {
        $map = [];
        $type = input('post.type');
        $num = input('post.num');
        $start = input('post.start');
        $end = input('post.end');
        if ($type && $type !== "") {
            $map['a.type'] = $type;
        }
        if ($num && $num !== "") {
            $map['a.num'] = ['like', "%" . $num . "%"];
        }
        if ($start && $start !== "") {
            $map['a.create_time'] = ['egt', strtotime($start)];
        }
        if ($end && $end !== "") {
            $map['a.create_time'] = ['elt', strtotime($end) + 86400];
        }
        if ($start && $end) {
            $map['a.create_time'] = [['egt', strtotime($start)], ['elt', strtotime($end) + 86400]];
        }
        $model = new OrderModel();
        $data = $model->index($map);
        $this->assign(['order' => $data['order'], 'count' => $data['count'], 'page' => $data['page']]);
        return $this->fetch('');
    }

    // 添加订单
    public function add()
    {
        $data = input('param.');
        if ($data && $data != '') {
            $model = new OrderModel();
            $flag = $model->add($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $user = Db::name('user')->field('id,nickname')->select();
        $this->assign('user', $user);
        return $this->fetch();
    }

//   启用订单
    public function start()
    {
        $id = input('param.id');
        Db::name('order')->where('id', $id)->setField('status', 1);
        writeLog(session('account'), '恢复', '订单' . $id, 1);
    }

//   禁用订单
    public function end()
    {
        $id = input('param.id');
        Db::name('order')->where('id', $id)->setField('status', 0);
        writeLog(session('account'), '暂停', '订单' . $id, 1);
    }

    // 编辑订单
    public function edit()
    {
        $model = new OrderModel();
        if (request()->isAjax()) {
            $data = input('post.');
            $flag = $model->edit($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $user = Db::name('user')->field('id,nickname')->select();
        $this->assign(['order' => $model->getOneList($id), 'user' => $user]);
        return $this->fetch();
    }

    // 删除订单
    public function delete()
    {
        $id = input('param.id');
        $model = new OrderModel();
        $flag = $model->del($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}
