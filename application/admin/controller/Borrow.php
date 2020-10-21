<?php
namespace app\admin\controller;

use think\Request;
use think\Validate;

/**
 * Class Borrow 借阅
 * @package app\admin\controller
 */
class Borrow extends Base
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
     * 学生借阅书籍
     * @param $bookId    int    书籍id
     * @param $studentId int 学生id
     * @param $back_at   date 归还时间
     */
    public function borrowBooks()
    {
        //验证器
        $validate = new \app\admin\validate\BorrowBook();

        //信息验证
        if (!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->bookService->borrowBooks( input() );

        return $this->end($result);

    }

    
    /**
     * 学生归还书籍
     * @param $bookId    int    书籍id
     * @param $studentId int 学生id
     */
    public function backBooks()
    {

        $validate = new Validate([
            'bookId|图书'    => 'require',
            'studentId|学生' => 'require',
        ]);

        if(!$validate->check(input())) {

            return $this->end('102', $validate->getError());

        }

        $result = $this->bookService->backBooks( input() );

        return $this->end($result);

    }


}
