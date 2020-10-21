<?php

namespace app\admin\service;

use app\admin\model\Book;
use app\admin\model\Borrow;
use think\Controller;

class BookService extends Base
{

    //model信息
    protected $bookModel;
    /**
     * @var mixed
     */
    protected $borrowModel;

    public function initialize()
    {

        parent::initialize();

        //图书model
        $this->bookModel = new Book();

        //借阅
        $this->borrowModel = new Borrow();

    }

    /**
     * 获取图书列表
     */
    public function getLists( $data ){

        //查询条件
        $where['book.is_del'] =  0;

        //设置名称查询
        if (!empty($title)) {

            $where['book.title'] = ['like', $data['title'] . "%"];

        }
        //设置分类查询
        if (!empty($categoryId)) {

            $where['book.category_id'] = ['=', $data['categoryId']];

        }
        //设置author查询
        if (!empty($author)) {

            $where['book.author'] = ['like', $data['author'] . "%"];

        }
        //设置出版社查询
        if (!empty($press)) {

            $where['book.press'] = ['like', $data['press'] . "%"];

        }

        //查询图书列表
        $bookList = $this->bookModel::with('getCategory')
            ->where($where)
            ->order('id desc')
            ->limit((($data['page'] ?? 1) - 1) * $this->pages, $this->pages)
            ->select()
            ->toArray();

        //总数查询
        $count = $this->bookModel::where($where)
            ->count();

        return [
            'code' => 200,
            'msg'  => 'success',
            'data' => [
                'count' => $count,
                'data'  => $bookList
            ]
        ];

    }

    /**
     * 保存图书
     * @param $title
     */
    public function bookSave($data) {

        //创建数据数量
        $count = $data['count'] ?? 1;

        //数据
        $array = [];

        for ($i = 0; $i < $count; $i++) {

            $array[] = [
                'category_id' => $data['categoryId'],
                'title'       => $data['title'],
                'author'      => $data['author'],
                'press'       => $data['press'],
                'c_id'        => $this->admin['id'],
                'status'      => 0
            ];

        }

        //添加数据
        $books = $this->bookModel->saveAll($array)->toArray();

        if ($books) {

            //添加缓存
            foreach ($books as $key => $value) {

                $bookSetCache = $this->redis->initRedis(config('data_config.book')['select'])
                    ->setCache('book:' . $value['id'],
                        $value);
            }

            return [
                'code' => 200,
                'msg'  => 'success',
                'data' => null
            ];

        } else {

            return [
                'code' => 102,
                'msg'  => '添加失败',
                'data' => null
            ];

        }

    }


    /**
     * 修改图书
     * @param $id
     * @param $title 图书标题
     * @param $categoryId 图书分类
     * @param $author 作者
     * @param $press 出版社
     */
    public function bookUpdate($data) {

        //修改数据
        $result = $this->bookModel::where(['id' => $data['id']])
            ->update([
                'title'  => $data['title'] ?? null,
                'category_id' => $data['categoryId'] ?? null,
                'author' => $data['author'] ?? null,
                'press'  => $data['press'] ?? null,
            ]);

        if ($result) {

            //查询书籍
            $info = $this->bookModel::get($data['id'])->toArray();

            //更新缓存
            $bookSetCache = $this->redis->initRedis(config('data_config.book')['select'])
                ->setCache('book:' . $data['id'],
                    $info);

            return [
                'code' => 200,
                'msg'  => 'success',
                'data' => null
            ];

        } else {

            return [
                'code' => 102,
                'msg'  => '添加失败',
                'data' => null
            ];

        }

    }


    /**
     * 删除图书
     * @param $id
     */
    public function bookDel($data) {

        //删除缓存
        $result = $this->bookModel::where('id', $data['id'])
            ->update([
                'is_del' => 1
            ]);

        if ($result) {

            //删除缓存
            $bookDelCache = $this->redis->initRedis(config('data_config.book')['select'])
                ->delCache('book:' . $data['id']);

            return [
                'code' => 200,
                'msg'  => '删除成功',
                'data' => null
            ];

        } else {

            return [
                'code' => 102,
                'msg'  => '删除失败',
                'data' => null
            ];

        }

    }

    /**
     * 学生借阅图书
     * @param $data
     */
    public function borrowBooks($data) {

        //验证书籍状态
        $bookGetCache = $this->redis->initRedis(config('data_config.book')['select'])
            ->getCache('book:' . $data['bookId']);

        //缓存过期
        if ($bookGetCache['code'] != 200) {

            //查询书籍信息
            return [
                'code' => 102,
                'msg'  => '无图书信息',
                'data' => null
            ];

        } elseif($bookGetCache['data']['status'] == 1) {
            //验证图书状态是否可借阅

            return [
                'code' => 102,
                'msg'  => '图书已被借阅',
                'data' => null
            ];

        }
        //学生借阅图书最大数限制
        $max = config('data_config.borrow_max');
        //查询学生没有归还数量
        $getStuBorrNum = $this->borrowModel
            ->where([
                'student_id' => $data['studentId'],
                'status' => 0
            ])->count();


        if ($getStuBorrNum >= $max) {

            return [
                'code' => 102,
                'msg'  => '每人同时最多可借阅' . $max . '本书籍',
                'data' => null
            ];

        }

        //创建借阅记录
        $borrow = $this->borrowModel::create([
                'book_id'     => $data['bookId'],
                'student_id'  => $data['studentId'],
                'estimate_back_at'     => $data['estimate_back_at']
            ], true)->toArray();


        if ($borrow) {

            //修改书籍状态
            $this->bookModel::where('id', $data['bookId'])
                ->update(['status' => 1]);

            $bookGetCache['data']['status'] = 1;

            //更新书籍缓存
            $bookSetCache = $this->redis->initRedis(config('data_config.book')['select'])
                ->setCache('book:' . $data['bookId'],
                    $bookGetCache['data']);

            //添加缓存
            $borrowSetCache = $this->redis->initRedis(config('data_config.borrow')['select'])
                ->setCache('borrow:student_' . $data['studentId'] . '_book_' . $data['bookId'],
                    [
                        'id'          => $borrow['id'],
                        'book_id'     => $data['bookId'],
                        'student_id'  => $data['studentId'],
                        'created_at'  => date('Y-m-d h:i:s'),
                        'estimate_back_at'     => $data['estimate_back_at'],
                        'status'      => 0
                    ]);

            return [
                'code' => 200,
                'msg'  => 'success',
                'data' => null
            ];

        } else {

            return [
                'code' => 102,
                'msg'  => '添加失败',
                'data' => null
            ];

        }
    }

    /**
     * 归还图书
     * @param $data
     */
    public function backBooks($data) {

        //验证借阅状态
        $borrowGetCache = $this->redis->initRedis(config('data_config.borrow')['select'])
            ->getCache('borrow:student_' . $data['studentId'] . '_book_' . $data['bookId']);

        //缓存过期
        if ($borrowGetCache['code'] != 200) {

            return [
                'code' => 102,
                'msg'  => '图书与学生不匹配',
                'data' => null
            ];

        }

        //还书
        $getStuBorrNum = $this->borrowModel::where('id', $borrowGetCache['data']['id'])
            ->update([
                'status'  => 1,
                'back_at' => date('Y-m-d H:i:s')
            ]);

        //删除缓存
        $this->redis->initRedis(config('data_config.borrow')['select'])
            ->delCache('borrow:student_' . $data['studentId'] . '_book_' . $data['bookId']);

        //验证书籍状态
        $bookGetCache = $this->redis->initRedis(config('data_config.book')['select'])
            ->getCache('book:' . $data['bookId']);

        //缓存过期
        if ($bookGetCache['code'] != 200) {

            //查询书籍信息
            $bookGetCache['data'] = $this->bookModel::get($data['bookId'])->toArray();

        }

        //修改图书状态，可借阅
        $this->bookModel::where('id', $data['bookId'])
            ->update([
                'status'  => 0,
            ]);

        $bookGetCache['data']['status'] = 0;

        //更新图书缓存
        $bookSetCache = $this->redis->initRedis(config('data_config.book')['select'])
            ->setCache('book:' . $data['bookId'],
                $bookGetCache['data']);

        return [
            'code' => 200,
            'msg'  => 'success',
            'data' => null
        ];

    }

}