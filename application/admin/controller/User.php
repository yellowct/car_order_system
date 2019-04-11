<?php

namespace app\Admin\controller;

use app\admin\model\UserModel;
use think\Controller;
use think\Db;

class User extends Controller
{
    public function index()
    {
        $map = [];
        $nickname = input('post.nickname');
        $start = input('post.start');
        $end = input('post.end');
        if ($nickname && $nickname !== "") {
            $map['nickname'] = ['like', "%" . $nickname . "%"];
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
        $model = new UserModel();
        $data = $model->index($map);
        $this->assign(['user' => $data['user'], 'count' => $data['count'], 'page' => $data['page']]);
        return $this->fetch('');
    }

//   启用用户
    public function start()
    {
        $id = input('param.id');
        Db::name('user')->where('id', $id)->setField('status', 1);
        writeLog(session('account'),'启用','用户'.$id,1);
    }

//   禁用用户
    public function end()
    {
        $id = input('param.id');
        Db::name('user')->where('id', $id)->setField('status', 0);
        writeLog(session('account'),'禁用','用户'.$id,1);
    }

    // 添加用户
    public function add()
    {
        $data = input('param.');
        if ($data && $data != '') {
            $model = new UserModel();
            $flag = $model->add($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }

    // 上传头像
    public function upload()
    {
        $file = request()->file('file');
        // if($file && $file == ''){
            dump($file);
            $model = new UserModel();
            $flag = $model->upload($file);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);


    }

    // 编辑用户
    public function edit()
    {
        $model = new UserModel();
        if (request()->isAjax()) {
            $data = input('post.');
            $flag = $model->edit($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('user', $model->getOneList($id));
        return $this->fetch();
    }

    // 删除用户
    public function delete()
    {
        $id = input('param.id');
        $model = new UserModel();
        $flag = $model->del($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}
