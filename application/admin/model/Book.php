<?php

namespace app\admin\model;

use think\Model;

/*
 * 图书
 */
class Book extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'book';

    //设置主键
    protected $pk = 'id';

    //关联分类表
    public function getCategory(){

        return $this->hasMany('category', 'id', 'category_id');

    }

}