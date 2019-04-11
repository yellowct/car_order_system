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
    $data['username'] = $username;
    $data['operate'] = $operate;
    $data['content'] = $content;
    $data['result'] = $result;
    $data['ip'] = request()->ip();
    $data['create_time'] = time();
    $log = Db::name('log')->insert($data);
}

