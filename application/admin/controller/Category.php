<?php
namespace app\admin\controller;

use think\Request;
use think\Validate;

/**
 * Class CategoryService 书籍种类
 * @package app\admin\controller
 */
class Category extends Base
{

    //图书分类业务
    protected $categoryService = null;

    public function initialize()
    {

        parent::initialize();

        //实例化service
        $this->categoryService = controller("CategoryService", "service");

    }
    /**
     * 书籍种类列表
     * @param $title strng 种类标题
     * @param $page int 页数
     */
    public function getLists()
    {

        $result = $this->categoryService->getLists( input() );

        return $this->end($result);

    }

    
    /**
     * 新建 书籍分类
     * @param $title string 分类名称
     */
    public function save()
    {

        //验证器
        $validate = new \app\admin\validate\Category();

        //信息验证
        if (!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->categoryService->ctgSave( input() );

        return $this->end($result);

    }


    /**
     * 修改 书籍分类
     * @param $title string 分类名称
     * @param $id int 分类id
     */
    public function update()
    {

        $validate = new Validate([
            'id|主键' => 'require',
            'title|图书分类标题' => 'require'
        ]);

        if(!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->categoryService->ctgUpdate( input() );

        return $this->end($result);

    }

    /**
     * 删除 书籍分类
     * @param $id int 分类id
     */
    public function del()
    {

        $validate = new Validate([
            'id|主键' => 'require',
        ]);

        if(!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->categoryService->ctgDel( input() );

        return $this->end($result);

    }


}
