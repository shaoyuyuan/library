<?php

namespace app\admin\validate;

use think\Validate;

class Book extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [

        'title'      => 'require',
        'categoryId' => 'require|number',
        'author'     => 'require',
        'press'      => 'require',

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [

        'title'      => '请填写图书标题',
        'categoryId.require' => '请填选择图书分类',
        'categoryId.number'  => '图书分类参数错误',
        'author'     => '请填写图书作者',
        'press'      => '请填写图书出版社',

    ];
}