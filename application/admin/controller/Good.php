<?php

namespace app\Admin\controller;

use app\admin\model\GoodModel;
use think\Controller;
use think\Db;

class Good extends Controller
{
    public function index()
    {
        $map = [];
        $type = input('post.type');
        $name = input('post.name');
        $start = input('post.start');
        $end = input('post.end');
        if ($type && $type !== "") {
            $map['type'] = $type;
        }
        if ($name && $name !== "") {
            $map['name'] = ['like', "%" . $name . "%"];
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
        // 不查询已下架的类型所属的商品
        $type=Db::name('good_type')->where('status',0)->column('id');
        $map['type']=array('not in',$type);
        
        $model = new GoodModel();
        $data = $model->index($map);
        $type = Db::name('good_type')->field('id,name')->select();
        $this->assign(['good' => $data['good'], 'count' => $data['count'], 'page' => $data['page'], 'type' => $type]);
        return $this->fetch('');
    }

//   启用商品
    public function start()
    {
        $id = input('param.id');
        Db::name('good')->where('id', $id)->setField('status', 1);
        writeLog(session('account'),'上架','商品'.$id,1);
    }

//   禁用商品
    public function end()
    {
        $id = input('param.id');
        Db::name('good')->where('id', $id)->setField('status', 0);
        writeLog(session('account'),'下架','商品'.$id,1);

    }

    // 添加商品
    public function add()
    {
        $data = input('param.');
        if ($data && $data != '') {
            $model = new GoodModel();
            $flag = $model->add($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $type = Db::name('good_type')->field('id,name')->select();
        $this->assign('type', $type);
        return $this->fetch();
    }

    // 编辑商品
    public function edit()
    {
        $model = new GoodModel();
        if (request()->isAjax()) {
            $data = input('post.');
            $flag = $model->edit($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $type = Db::name('good_type')->field('id,name')->select();
        $this->assign(['good' => $model->getOneList($id), 'type' => $type]);
        return $this->fetch();
    }

    // 删除商品
    public function delete()
    {
        $id = input('param.id');
        $model = new GoodModel();
        $flag = $model->del($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
// 商品类型
    public function type()
    {
        $map = [];
        $name = input('post.name');
        $start = input('post.start');
        $end = input('post.end');
        if ($name && $name !== "") {
            $map['name'] = ['like', "%" . $name . "%"];
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
        $model = new GoodModel();
        $data = $model->type($map);
        $this->assign(['type' => $data['type'], 'count' => $data['count'], 'page' => $data['page']]);
        return $this->fetch('');
    }
    //   启用商品类型
    public function up()
    {
        $id = input('param.id');
        Db::name('good_type')->where('id', $id)->setField('status', 1);
        writeLog(session('account'),'上架','商品类型'.$id,1);

    }

//   禁用商品类型
    public function down()
    {
        $id = input('param.id');
        Db::name('good_type')->where('id', $id)->setField('status', 0);
        writeLog(session('account'),'下架','商品类型'.$id,1);

    }

    // 添加商品类型
    public function add_type()
    {
        $data = input('param.');
        if ($data && $data != '') {
            $model = new GoodModel();
            $flag = $model->addType($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }

    // 编辑商品类型
    public function edit_type()
    {
        $model = new GoodModel();
        if (request()->isAjax()) {
            $data = input('post.');
            $flag = $model->editType($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('type' ,$model->getOneType($id));
        return $this->fetch();
    }

    // 删除商品类型
    public function delete_type()
    {
        $id = input('param.id');
        $model = new GoodModel();
        $flag = $model->delType($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}
