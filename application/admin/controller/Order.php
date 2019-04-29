<?php

namespace app\Admin\controller;

use app\admin\model\OrderModel;
use think\Controller;
use think\Db;

class Order extends Controller
{
    public function index()
    {
        $map = [];
        $type = input('post.type');
        $select = input('post.num');
        $start = input('post.start');
        $end = input('post.end');
        if ($type == 3) {
            $map['type'] = $type;
            $status=1;
        }else{
            $map['type'] = ['in','1,2'];
            $status=0;
        }
        if ($select && $select !== "") {
            $map['real_name|good_name|phone|company'] = ['like', "%" . $select . "%"];
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
        $model = new OrderModel();
        $data = $model->index($map);
        $excel=['start'=>$start,'end'=>$end,'status'=>$status];
        $this->assign(['order' => $data['order'], 'count' => $data['count'], 'page' => $data['page'],'excel'=>$excel]);
        return $this->fetch('');
    }

    // 添加订单
    public function add()
    {
        $data = input('param.');
        if ($data && $data != '') {
            $model = new OrderModel();
            $flag = $model->add($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $user = Db::name('user')->field('id,nickname')->select();
        $this->assign('user', $user);
        return $this->fetch();
    }

//   启用订单
    public function start()
    {
        $id = input('param.id');
        Db::name('order')->where('id', $id)->setField('status', 1);
        writeLog(session('account'), '恢复', '订单' . $id, 1);
    }

//   禁用订单
    public function end()
    {
        $id = input('param.id');
        Db::name('order')->where('id', $id)->setField('status', 0);
        writeLog(session('account'), '暂停', '订单' . $id, 1);
    }

    public function finish()
    {
        $id = input('param.id');
        Db::name('order')->where('id', $id)->setField('type', 3);
        Db::name('order')->where('rank','egt',1)->setDec('rank',1);
        Db::name('order')->where('rank',1)->setField('type',2);
        writeLog(session('account'), '完成', '订单' . $id, 1);
    }

    // 编辑订单
    public function edit()
    {
        $model = new OrderModel();
        if (request()->isAjax()) {
            $data = input('post.');
            $flag = $model->edit($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $user = Db::name('user')->field('id,nickname')->select();
        $this->assign(['order' => $model->getOneList($id), 'user' => $user]);
        return $this->fetch();
    }


    // 删除订单
    public function delete()
    {
        $id = input('param.id');
        $model = new OrderModel();
        $flag = $model->del($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
// 生成excel
    public function excel_out(){
        $map = [];
        $status = input('param.status');
        $start = input('param.start');
        $end = input('param.end');
        if ($status == 0) {
            $map['type'] = ['in','1,2']; 
        }else{
            $map['type'] = 3; 
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
        $list  = Db::name('order')->field('order_id,real_name,phone,good_name,company,type,create_time')->where($map)->order('id desc')->select();
        foreach ($list as $key => $value) {
            $list[$key]['create_time']=date('Y-m-d H:i',$value['create_time']);
        }
        $field = array(
            'A' => array('order_id', '预约编号'),
            'B' => array('real_name', '司机'),
            'C' => array('phone', '联系电话'),
            'D' => array('good_name', '商品名称'),
            'E' => array('company', '单位'),
            'F' => array('type',  '状态(1:待装,2:正装,3:已装)'),
            'G' => array('create_time', '预约时间'),
        );
        $this->phpExcelList($field,$list,$status);
        // if(!$excel){
        //    writeLog(session('account'), '导出', '订单', 0);
        // }else{
        //     writeLog(session('account'), '导出', '订单', 1);
        // }
    }

    public function phpExcelList($field, $list,$status){
        if($status==0){
            $title='进行中订单'; 
        }else{
            $title='已完成订单'; 
        }
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel); //设置保存版本格式
        foreach ($list as $key => $value) {
            foreach ($field as $k => $v) {
                if ($key == 0) {
                    $objPHPExcel->getActiveSheet()->setCellValue($k . '1', $v[1]);
                }
                $i = $key + 2; //表格是从2开始的
                $objPHPExcel->getActiveSheet()->setCellValue($k . $i, $value[$v[0]]);
            }

        }
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename='.$title.'.xls');
        header("Content-Transfer-Encoding:binary");
//        $objWriter->save($title.'.xls');
        $objWriter->save('php://output');

        writeLog(session('account'), '导出', '订单', 1);
    }
}
