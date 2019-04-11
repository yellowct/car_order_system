<?php

namespace app\Admin\controller;

use app\admin\model\LogModel;
use think\Controller;

class Log extends Controller
{
    public function index()
    {
        $map = [];
        $content = input('post.content');
        $start = input('post.start');
        $end = input('post.end');
        if ($content && $content !== "") {
            $map['content'] = ['like', "%" . $content . "%"];
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
        $model = new LogModel();
        $data = $model->index($map);
        $this->assign(['log' => $data['log'], 'count' => $data['count'], 'page' => $data['page']]);
        return $this->fetch('');
    }


    // 删除日志
    public function delete()
    {
        $id = input('param.id');
        $model = new LogModel();
        $flag = $model->del($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}
