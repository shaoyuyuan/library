<?php
namespace app\admin\controller;

use think\Request;
use think\Validate;

/**
 * Class Book 书籍
 * @package app\admin\controller
 */
class Book extends Base
{

    //图书业务
    protected $bookService = null;

    public function initialize()
    {

        parent::initialize();

        //实例化service
        $this->bookService = controller("BookService", "service");

    }
    /**
     * 书籍列表
     * @param $title strng 书籍名称
     * @param $categoryId int 书籍分类id
     * @param $author string 作者
     * @param $press string 出版社
     * @param $page int 页数
     */
    public function getLists()
    {

        $result = $this->bookService->getLists( input() );

        return $this->end($result);

    }

    
    /**
     * 新建 书籍(批量添加相同书籍)
     * @param $title      string 书籍名称
     * @param $categoryId int 书籍分类id
     * @param $author     string 作者
     * @param $press      string 出版社
     * @param $count     int 书籍数
     */
    public function save()
    {

        //验证器
        $validate = new \app\admin\validate\Book();

        //信息验证
        if (!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->bookService->bookSave( input() );

        return $this->end($result);

    }


    /**
     * 修改 书籍分类
     * @param $id         int 书籍id
     * @param $title      string 书籍名称
     * @param $categoryId int 书籍分类id
     * @param $author     string 作者
     * @param $press      string 出版社
     */
    public function update()
    {

        $validate = new Validate([
            'id|主键' => 'require',
        ]);

        if(!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->bookService->bookUpdate( input() );

        return $this->end($result);

    }

    /**
     * 删除 书籍
     * @param $id int 书籍id
     */
    public function del()
    {

        $validate = new Validate([
            'id|主键' => 'require',
        ]);

        if(!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->bookService->bookDel( input() );

        return $this->end($result);

    }


}
