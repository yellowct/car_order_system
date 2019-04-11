<?php

namespace app\admin\controller;
use think\Controller;

class Base extends Controller{
  
    public function _initialize(){      
            if(!session('id')||!session('account')){
               return $this->error('您还未登录，请先登录！','admin/login/index', 1);
            }
        

    }
}
