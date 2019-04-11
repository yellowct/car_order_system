<?php

namespace app\admin\model;

use think\Model;

class UserModel extends Model
{
    protected $name = 'user';
    protected $autoWriteTimestamp = true;

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

    public function upload($file){
        $info = $file->move(ROOT_PATH . '/public' . DS . 'uploads/images');
        if($info){
            $url='/hct/public/uploads/images/'.$info->getSaveName();
            return ['code' => 1, 'data' => $url, 'msg' =>'上传成功' ];   
        }else{
            return ['code' => 0, 'data' => '', 'msg' => $file->getError()];
        }
    }
    
    public function add($data)
    {
        $result = $this->allowField(true)->save($data);
        if (false === $result) {
            writeLog(session('account'),'添加','用户',0);
            return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            
        } else {
            writeLog(session('account'),'添加','用户',1);
            return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
            
        }
    }
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

    public function del($id)
    {
        $this->where('id', 'in', $id)->delete();
        writeLog(session('account'),'删除','用户'.$id,1);
        return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
    }

    public function getOneList($id)
    {
        return $this->where('id', $id)->find();
    }
}
