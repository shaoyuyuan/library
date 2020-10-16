<?php
namespace app\admin\controller;

use think\Request;
use think\Config;   //加载配置
use app\common\redis\Redis;

/**
 * Class Book 书籍
 * @package app\admin\controller
 */
class Book extends Base
{

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

        $title = input('title');
        $categoryId = input('categoryId');
        $author = input('author');
        $press = input('press');
        $page = (int)input('page', 0);

        //查询条件
        $where['book.is_del'] =  0;

        //设置名称查询
        if (!empty($title)) {

            $where['book.title'] = ['like', $title . "%"];
            
        }
        //设置分类查询
        if (!empty($categoryId)) {

            $where['book.category_id'] = ['=', $categoryId];
            
        }
        //设置author查询
        if (!empty($author)) {

            $where['book.author'] = ['like', $author . "%"];
            
        }
        //设置出版社查询
        if (!empty($press)) {

            $where['book.press'] = ['like', $press . "%"];
            
        }

        $number = 10;
        
        $count = db('book')->where($where)->count();

        $total = ceil($count / $number);

        //查询书籍列表
        $list = db('book')
            ->alias('book')
            ->field('book.id,book.title,book.created_at,book.updated_at,book.author,book.press,ctg.title as ctg_title')
            ->leftjoin('category ctg', 'book.category_id = ctg.id')
            ->where($where)
            ->order('book.id desc')
            ->limit($page * $number . ',' . $number)
            ->select();


        return json([
            'code' => 200,
            'msg'  => 'success',
            'data' => [
                'count'  => $count,
                'total'  => $total,
                'page'   => $page,
                'msg'    => $list,
            ]
        ]);
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

        $title      = input('title');
        $categoryId = (int)input('categoryId');
        $author     = input('author');
        $press      = input('press');
        $count      = (int)input('count', 1);

        if (empty($title)) {

            return json([
                'code' => 102,
                'msg'  => '请填写书籍名称',
                'data' => null
            ]);

        }
        if (empty($categoryId)) {

            return json([
                'code' => 102,
                'msg'  => '请选择书籍分类',
                'data' => null
            ]);

        }

        //数据
        $data = [];

        for ($i = 0; $i < $count; $i++) { 
            
            $data[] = [
                'category_id' => $categoryId,
                'title'       => $title,
                'author'      => $author,
                'press'       => $press,
                'c_id'        => $this->token['id']
            ];

        }
        //添加数据
        $result = db('book')
                ->insertAll($data);

        if ($result) {

            //查询对应的书籍更新缓存
            $books = db('book')
                    ->where([
                        'category_id' => $categoryId,
                        'title'       => $title,
                        'author'      => $author,
                        'press'       => $press,
                        'c_id'        => $this->token['id']
                    ])
                    ->select();
            //添加缓存
            foreach ($books as $key => $value) {
                
                $bookSetCache = $this->redis->initRedis(config('data_config.book')['select'])
                            ->setCache('book:' . $value['id'], 
                                $value, 
                                config('data_config.book')['timeout']);
            }
            
            return json([
                'code' => 200,
                'msg'  => 'success',
                'data' => null
            ]);

        } else {

            return json([
                'code' => 102,
                'msg'  => '添加失败',
                'data' => null
            ]);

        }

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

        $id = input('id');
        $title      = input('title');
        $categoryId = (int)input('categoryId');
        $author     = input('author');
        $press      = input('press');

        if (empty($id)) {

            return json([
                'code' => 102,
                'msg'  => '参数错误',
                'data' => null
            ]);

        }

        //修改数据
        $result = db('book')
                ->where(['id' => $id])
                ->update([
                    'title' => $title,
                    'category_id' => $categoryId,
                    'author' => $author,
                    'press' => $press,
                ]);

        if ($result) {

            //查询书籍
            $info = db('book')
                ->where(['id' => $id])
                ->find();
            //更新缓存
            $bookSetCache = $this->redis->initRedis(config('data_config.book')['select'])
                        ->setCache('book:' . $id, 
                            $info, 
                            config('data_config.book')['timeout']);
            
            return json([
                'code' => 200,
                'msg'  => 'success',
                'data' => null
            ]);

        } else {

            return json([
                'code' => 102,
                'msg'  => '添加失败',
                'data' => null
            ]);

        }

    }

    /**
     * 删除 书籍
     * @param $id int 书籍id
     */
    public function del()
    {
        $id = input('id');

        if (empty($id)) {

            return json([
                'code' => 102,
                'msg'  => '参数错误',
                'data' => null
            ]);

        }

        //删除缓存
        $result = db('book')
            ->where('id', $id)
            ->update([
                'is_del' => 1
            ]);

        if ($result) {

            //删除缓存
            $bookDelCache = $this->redis->initRedis(config('data_config.book')['select'])
                    ->delCache('book:' . $id);

            return json([
                'code' => 200,
                'msg'  => '删除成功',
                'data' => null
            ]);

        } else {

            return json([
                'code' => 102,
                'msg'  => '删除失败',
                'data' => null
            ]);

        }
    }


}
