<?php

namespace app\admin\validate;

use think\Validate;

class BorrowBook extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [

        'bookId'      => 'require|number',
        'studentId' => 'require|number',
        'estimate_back_at'     => 'require',

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [

        'bookId.require'    => '请填选择图书',
        'bookId.number'     => '图书id必须是数字',
        'studentId.require' => '请选择借阅学生',
        'studentId.number'  => '借阅学生参数错误',
        'back_at'           => '归还日期必填',

    ];
}