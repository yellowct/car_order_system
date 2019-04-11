<?php

namespace app\admin\model;

use think\Model;

class OrderModel extends Model
{
    protected $name = 'order';
    protected $autoWriteTimestamp = true;

    public function index($map)
    {
        $order=$this->where($map)->order('rank asc')->paginate(6);

        $list = $order->items();

        $count=$this->count();
        //   分页
        $page = $order->render();

        return ['order' => $list, 'count' => $count, 'page' => $page];
    }

    // public function add($data)
    // {
    //     $result = $this->allowField(true)->save($data);
    //     if (false === $result) {

    //         return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
    //     } else {

    //         return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
    //     }
    // }
    // public function edit($data)
    // {
    //     $result = $this->allowField(true)->save($data, ['id' => $data['id']]);
    //     if (false === $result) {

    //         return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
    //     } else {

    //         return ['code' => 1, 'data' => '', 'msg' => '修改成功'];
    //     }

    // }

    public function del($id)
    {
        $this->where('id', 'in', $id)->delete();
        writeLog(session('account'),'删除','订单'.$id,1);
        return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
    }

    public function getOneList($id)
    {
        return $this->where('id', $id)->find();
    }
}
