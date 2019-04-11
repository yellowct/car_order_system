<?php

namespace app\admin\model;
use think\Db;
use think\Model;

class GoodModel extends Model
{
    protected $name = 'good';
    protected $autoWriteTimestamp = true;

    public function index($map){
        $good = $this->where($map)->order('id desc')->paginate(6);
        $list = $good->items();
        foreach ($list as $key => $value) {
            $list[$key]['type'] = db('good_type')->where('id',$value['type'])->value('name');
        }    
        //   查询总条数
        $count = $this->where($map)->count();
        //   分页
        $page = $good->render();
        return ['good' => $list, 'count' =>$count, 'page' => $page];
    }


    public function add($data)
    {
        $result = $this->allowField(true)->save($data);
        if (false === $result) {
            writeLog(session('account'),'添加','商品',0);

            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
        } else {
            writeLog(session('account'),'添加','商品',1);

            return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
        }
    }
    public function edit($data)
    {
        $result = $this->allowField(true)->save($data, ['id' => $data['id']]);
        if (false === $result) {
            writeLog(session('account'),'编辑','商品',0);

            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
        } else {
            writeLog(session('account'),'编辑','商品',1);

            return ['code' => 1, 'data' => '', 'msg' => '修改成功'];
        }

    }

    public function del($id)
    {
        $this->where('id', 'in', $id)->delete();
        writeLog(session('account'),'删除','商品'.$id,1);

        return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
    }

    public function getOneList($id)
    {
        return $this->where('id', $id)->find();
    }

    public function type($map){
        $type = Db::name('good_type')->where($map)->order('id desc')->paginate(6);
        $list = $type->items();
        foreach ($list as $key => $value) {
            $list[$key]['create_time'] = date('Y-m-d h:i:s',$value['create_time']);
        }    
        //   查询总条数
        $count = $this->where($map)->count();
        //   分页
        $page = $type->render();
        return ['type' => $list, 'count' =>$count, 'page' => $page];
    }
    public function addType($data)
    {
        $data['create_time']=time();
        $result = Db::name('good_type')->insert($data);
        if (false === $result) {
            writeLog(session('account'),'添加','商品类型',0);
            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
        } else {
            writeLog(session('account'),'添加','商品类型',1);
            return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
        }
    }
    public function editType($data)
    {
        $data['update_time']=time();
        $result = Db::name('good_type')->where('id',$data['id'])->update($data);
        if (false === $result) {
            writeLog(session('account'),'编辑','商品类型',0);
            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
        } else {
            writeLog(session('account'),'编辑','商品类型',1);
            return ['code' => 1, 'data' => '', 'msg' => '修改成功'];
        }

    }

    public function delType($id)
    {
        Db::name('good_type')->where('id', 'in', $id)->delete();
        writeLog(session('account'),'删除','商品类型'.$id,1);
        return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
    }

    public function getOneType($id)
    {
        return Db::name('good_type')->where('id', $id)->find();
    }

}
