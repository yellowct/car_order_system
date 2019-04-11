<?php

namespace app\Admin\controller;

use app\admin\model\NoticeModel;
use think\Controller;
use think\Db;

class Notice extends Controller
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
        $model = new NoticeModel();
        $data = $model->index($map);
        $this->assign(['notice' => $data['notice'], 'count' => $data['count'], 'page' => $data['page']]);
        return $this->fetch('');
    }


    
    // 编辑公告
    public function edit()
    {
        $model = new NoticeModel();
        if (request()->isAjax()) {
            $data = input('post.');
            $flag = $model->edit($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('notice',$model->getOneList($id));
        return $this->fetch();
    }

 
}
