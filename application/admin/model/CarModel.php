<?php

namespace app\admin\model;

use think\Model;

class CarModel extends Model
{
    protected $name = 'car';
    protected $autoWriteTimestamp = true;

    public function index($map){
        $car = $this->where($map)->order('id desc')->paginate(6);
        $list = $car->items();    
        foreach ($list as $key => $value) {
            $list[$key]['user'] = db('user')->where('id',$value['user_id'])->value('real_name');
        }
        //   查询总条数
        $count = $this->where($map)->count();
        //   分页
        $page = $car->render();
        return ['car' => $list, 'count' =>$count, 'page' => $page];
    }


    public function add($data)
    {
        $result = $this->allowField(true)->save($data);
        if (false === $result) {
            writeLog(session('account'),'添加','车辆',0);
            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
        } else {
            writeLog(session('account'),'添加','车辆',1);
            return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
        }
    }
    public function edit($data)
    {
        $result = $this->allowField(true)->save($data, ['id' => $data['id']]);
        if (false === $result) {
            writeLog(session('account'),'编辑','车辆',0);
            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
        } else {
            writeLog(session('account'),'编辑','车辆',1);
            return ['code' => 1, 'data' => '', 'msg' => '修改成功'];
        }

    }

    public function del($id)
    {
        $this->where('id', 'in', $id)->delete();
        writeLog(session('account'),'删除','车辆'.$id,1);
        return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
    }

    public function getOneList($id)
    {
        return $this->where('id', $id)->find();
    }
}
