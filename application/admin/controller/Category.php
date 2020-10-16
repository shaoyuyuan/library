<?php
namespace app\admin\controller;

use think\Request;
use think\Config;   //加载配置
use app\common\redis\Redis;

/**
 * Class Category 书籍种类
 * @package app\admin\controller
 */
class Category extends Base
{

    /**
     * 书籍种类列表
     * @param $title strng 种类标题
     * @param $page int 页数
     */
    public function getLists()
    {

        $title = input('title');
        $page = (int)input('page', 0);

        //查询条件
        $where['is_del'] =  0;

        //设置名称查询
        if (!empty($title)) {

            $where['title'] = ['like', $title . "%"];
            
        }

        $number = 10;
        
        $count = db('category')->where($where)->count();

        $total = ceil($count / $number);

        //查询分类列表
        $list = db('category')
            ->field('id,title,created_at,updated_at')
            ->where($where)
            ->order('id desc')
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
     * 新建 书籍分类
     * @param $title string 分类名称
     */
    public function save()
    {

        $title = input('title');

        if (empty($title)) {

            return json([
                'code' => 102,
                'msg'  => '请填写分类名称',
                'data' => null
            ]);

        }

        //验证是否重名
        $checkTitle = db('category')
                    ->where(['title' => $title, 'is_del' => 0])
                    ->find();

        if ($checkTitle) {

            return json([
                'code' => 102,
                'msg'  => '分类名称重复',
                'data' => null
            ]);

        }

        //添加数据
        $categoryId = db('category')
                ->insertGetId([
                    'title' => $title,
                    'c_id'  => $this->token['id']
                ]);

        if ($categoryId) {

            //添加缓存
            $categorySetCache = $this->redis->initRedis(config('data_config.category')['select'])
                        ->setCache('category:' . $categoryId, 
                            [
                                'id'    => $categoryId,
                                'title' => $title,
                                'c_id'  => $this->token['id']
                            ], 
                            config('data_config.category')['timeout']);
            
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
     * @param $title string 分类名称
     * @param $id int 分类id
     */
    public function update()
    {

        $id = input('id');
        $title = input('title');

        if (empty($title) || empty($id)) {

            return json([
                'code' => 102,
                'msg'  => '参数错误',
                'data' => null
            ]);

        }
        //验证是否重名
        $checkTitle = db('category')
                    ->where([
                        ['title', '=', $title],
                        ['is_del', '=', 0],
                        ['id', '<>', $id],
                    ])
                    ->find();

        if ($checkTitle) {

            return json([
                'code' => 102,
                'msg'  => '分类名称重复',
                'data' => null
            ]);

        }

        $data = [

            'title' => $title,

        ];

        //修改数据
        $result = db('category')
                ->where(['id' => $id])
                ->update([
                    'title' => $title
                ]);

        if ($result) {

            //更新缓存
            $categorySetCache = $this->redis->initRedis(config('data_config.category')['select'])
                        ->setCache('category:' . $id, 
                            [
                                'id'    => $id,
                                'title' => $title
                            ], 
                            config('data_config.category')['timeout']);
            
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
     * 删除 书籍分类
     * @param $id int 分类id
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
        $result = db('category')
            ->where('id', $id)
            ->update([
                'is_del' => 1
            ]);

        if ($result) {

            //删除缓存
            $categoryDelCache = $this->redis->initRedis(config('data_config.category')['select'])
                    ->delCache('category:' . $id);

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
