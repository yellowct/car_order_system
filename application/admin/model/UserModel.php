<?php

namespace app\admin\model;

use think\Model;

class UserModel extends Model
{
    protected $name = 'user';
    protected $autoWriteTimestamp = true;
// 进入首页
    public function index($map){
        $user = $this->where($map)->order('id desc')->paginate(6);
        $list = $user->items();    
        // foreach ($list as $key => $value) {
        //     $list[$key]['create_time'] = date("Y-m-d H:i:s", $value['create_time']);
        // }
        //   查询总条数
        $count = $this->where($map)->count();
        //   分页
        $page = $user->render();
        return ['user' => $list, 'count' =>$count, 'page' => $page];
    }

// 编辑用户
    public function edit($data)
    {
        $result = $this->allowField(true)->save($data, ['id' => $data['id']]);
        if (false === $result) {
            writeLog(session('account'),'编辑','用户',0);
            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            
        } else {
            writeLog(session('account'),'编辑','用户',1);
            return ['code' => 1, 'data' => '', 'msg' => '修改成功'];
            
        }

    }
// 删除用户
    public function del($id)
    {
        $this->where('id', 'in', $id)->delete();
        writeLog(session('account'),'删除','用户'.$id,1);
        return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
    }
// 获取一条用户信息
    public function getOneList($id)
    {
        return $this->where('id', $id)->find();
    }
}
