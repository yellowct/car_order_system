<?php

namespace app\Admin\controller;

use app\admin\model\CarModel;
use think\Controller;
use think\Db;

class Car extends Controller
{
    public function index()
    {
        $map = [];
        $num = input('post.num');
        $start = input('post.start');
        $end = input('post.end');
        if ($num && $num !== "") {
            $map['num'] = ['like', "%" . $num . "%"];
        }
        if ($start && $start !== "") {
            $map['create_time'] = ['egt', strtotime($start)];
        }
        if ($end && $end !== "") {
            $map['create_time'] = ['elt', strtotime($end) + 86400];
        }
        if ($start && $end) {
            $map['create_time'] = [['egt', strtotime($start)], ['elt', strtotime($end) + 86400]];
        }
        $model = new CarModel();
        $data = $model->index($map);
        $this->assign(['car' => $data['car'], 'count' => $data['count'], 'page' => $data['page']]);
        return $this->fetch('');
    }

//   启用车辆
    public function start()
    {
        $id = input('param.id');
        Db::name('car')->where('id', $id)->setField('status', 1);
        writeLog(session('account'),'启用','车辆'.$id,1);
    }

//   禁用车辆
    public function end()
    {
        $id = input('param.id');
        Db::name('car')->where('id', $id)->setField('status', 0);
        writeLog(session('account'),'禁用','车辆'.$id,1);
    }

    // 添加车辆
    public function add()
    {
        $data = input('param.');
        if ($data && $data != '') {
            $model = new CarModel();
            $flag = $model->add($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $user=Db::name('user')->field('id,nickname')->select();
        $this->assign('user',$user);
        return $this->fetch();
    }

    
    // 编辑车辆
    public function edit()
    {
        $model = new CarModel();
        if (request()->isAjax()) {
            $data = input('post.');
            $flag = $model->edit($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $user=Db::name('user')->field('id,nickname')->select();
        $this->assign(['car'=>$model->getOneList($id),'user'=>$user]);
        return $this->fetch();
    }

    // 删除车辆
    public function delete()
    {
        $id = input('param.id');
        $model = new CarModel();
        $flag = $model->del($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}
