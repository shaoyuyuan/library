<?php

namespace app\admin\service;

use app\admin\model\Category;
use think\Controller;

class CategoryService extends Base
{

    //model信息
    protected $ctgModel;

    public function initialize()
    {

        parent::initialize();

        $this->ctgModel = new Category();

    }

    /**
     * 获取图书分类列表
     * @author syy 新增
     *
     */
    public function getLists( $data ){

        //查询条件
        $where['is_del'] =  0;

        //设置名称查询
        if (!empty($data['title'])) {

            $where['title'] = ['like', $data['title'] . "%"];

        }

        //查询图书分类列表
        $ctgList = $this->ctgModel::where($where)
            ->order('id desc')
            ->limit((($data['page'] ?? 1) - 1) * $this->pages, $this->pages)
            ->select()
            ->toArray();

        //总数查询
        $count = $this->ctgModel::where($where)
            ->count();

        return [
            'code' => 200,
            'msg'  => 'success',
            'data' => [
                'count' => $count,
                'data'  => $ctgList
            ]
        ];

    }

    /**
     * 保存图书分类
     * @param $title
     */
    public function ctgSave($data) {

        $checkTitle = $this->ctgModel::get([
                'title' => $data['title'],
                'is_del'=>0
            ]);

        if ($checkTitle) {

            return [
                'code' => 102,
                'msg'  => '分类名称重复',
                'data' => null
            ];

        }

        //添加数据
        $category = $this->ctgModel::create([
                'title' => $data['title'],
                'c_id'  => $this->admin['id']
            ], true)->toArray();

        if ($category) {

            //添加缓存
            $categorySetCache = $this->redis->initRedis(config('data_config.category')['select'])
                ->setCache('category:' . $category['id'],
                    $category,
                    config('data_config.category')['timeout']);

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
     * 修改图书分类
     * @param $id
     * @param $title
     */
    public function ctgUpdate($data) {

        //验证是否重名
        $checkTitle = $this->ctgModel::where([
                ['title', '=', $data['title']],
                ['is_del', '=', 0],
                ['id', '<>', $data['id']],
            ])->find();

        if ($checkTitle) {

            return [
                'code' => 102,
                'msg'  => '分类名称重复',
                'data' => null
            ];

        }

        //修改数据
        $result = $this->ctgModel::where(['id' => $data['id']])
            ->update([
                'title' => $data['title']
            ]);

        if ($result) {

            //更新缓存
            $categorySetCache = $this->redis->initRedis(config('data_config.category')['select'])
                ->setCache('category:' . $data['id'],
                    [
                        'id'    => $data['id'],
                        'title' => $data['title']
                    ],
                    config('data_config.category')['timeout']);

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
     * 删除图书分类
     * @param $id
     */
    public function ctgDel($data) {

        //删除缓存
        $result = $this->ctgModel::where('id', $data['id'])
            ->update([
                'is_del' => 1
            ]);

        if ($result) {

            //删除缓存
            $categoryDelCache = $this->redis->initRedis(config('data_config.category')['select'])
                ->delCache('category:' . $data['id']);

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

}