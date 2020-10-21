<?php

namespace app\admin\model;

use think\Model;

/*
 * 管理员用户
 */
class Admin extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'admin';

    //设置主键
    protected $pk = 'id';


}