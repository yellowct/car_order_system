<?php
/**
 * Created by PhpStorm.
 * User: Asuss
 * Date: 2018/8/31
 * Time: 20:45
 */

namespace app\Admin\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch('');
    }
    public function welcome()
    {
        $user = Db::name('user')->count();
        $car = Db::name('car')->count();
        $good = Db::name('good')->count();
        $order = Db::name('order')->count();
        $this->assign(['account' => session('account'), 'time' => session('login_time'),
            'user' => $user, 'car' => $car, 'good' => $good, 'order' => $order]);
        return $this->fetch();
    }

}
