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

        $count=$this->where($map)->count();
        //   分页
        $page = $order->render();

        return ['order' => $list, 'count' => $count, 'page' => $page];
    }



    public function del($id)
    {
        $rank=$this->where('id', $id)->value('rank');
        $this->where('rank','gt',$rank)->setDec('rank',1);
        $this->where('id', 'in', $id)->delete();
        writeLog(session('account'),'删除','订单'.$id,1);
        return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
    }

    public function getOneList($id)
    {
        return $this->where('id', $id)->find();
    }
}
