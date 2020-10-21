<?php

namespace app\admin\model;

use think\Model;

/*
 * 图书
 */
class Borrow extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'borrow';

    //设置主键
    protected $pk = 'id';

    //关联分类表
    public function getBook(){

        return $this->hasMany('book', 'id', 'book_id');

    }
    //关联分类表
    public function getStudent(){

        return $this->hasMany('student', 'id', 'student_id');

    }

}