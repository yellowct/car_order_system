<?php

namespace app\admin\model;

use think\Model;

class NoticeModel extends Model
{
    protected $name = 'notice';
    protected $autoWriteTimestamp = true;

    public function index($map){
        $notice = $this->where($map)->order('id desc')->paginate(6);
        $list = $notice->items();    
        //   查询总条数
        $count = $this->where($map)->count();
        //   分页
        $page = $notice->render();
        return ['notice' => $list, 'count' =>$count, 'page' => $page];
    }


   
    public function edit($data)
    {
        $result = $this->allowField(true)->save($data, ['id' => $data['id']]);
        if (false === $result) {
            writeLog(session('account'),'编辑','公告',0);
            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
        } else {
            writeLog(session('account'),'编辑','公告',1);
            return ['code' => 1, 'data' => '', 'msg' => '修改成功'];
        }

    }

   

    public function getOneList($id)
    {
        return $this->where('id', $id)->find();
    }
}
