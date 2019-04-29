<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Db;
// 应用公共文件

// 记录日志
function writeLog($username, $operate, $content, $result)
{
    // 操作者
    $data['username'] = $username;
    // 操作行为
    $data['operate'] = $operate;
    // 内容
    $data['content'] = $content;
    // 结果
    $data['result'] = $result;
    // ip地址
    $data['ip'] = request()->ip();
    // 创建时间
    $data['create_time'] = time();
    $log = Db::name('log')->insert($data);
}

