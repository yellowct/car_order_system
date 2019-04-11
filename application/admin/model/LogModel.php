<?php

namespace app\admin\model;

use think\Model;

class LogModel extends Model
{
    protected $name = 'log';
    protected $autoWriteTimestamp = true;

    public function index($map){
        $log = $this->where($map)->order('id desc')->paginate(6);
        $list = $log->items();    
        //   查询总条数
        $count = $this->where($map)->count();
        //   分页
        $page = $log->render();
        return ['log' => $list, 'count' =>$count, 'page' => $page];
    }



    public function del($id)
    {
        $this->where('id', 'in', $id)->delete();
        writeLog(session('account'),'删除','日志'.$id,1);
        return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
    }


}
