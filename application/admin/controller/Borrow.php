<?php
namespace app\admin\controller;

use think\Request;
use think\Config;   //加载配置
use app\common\redis\Redis;

/**
 * Class Borrow 借阅
 * @package app\admin\controller
 */
class Borrow extends Base
{

    /**
     * 学生借阅书籍
     * @param $bookId    int    书籍id
     * @param $studentId int 学生id
     * @param $back_at   date 归还时间
     */
    public function borrowBooks()
    {
        $bookId = input('bookId');
        $studentId = input('studentId');
        $back_at = input('back_at');

        if (empty($bookId) || empty($studentId) || empty($back_at)) {

            return json([
                'code' => 102,
                'msg'  => '参数错误',
                'data' => null
            ]);

        }
        //验证书籍状态
        $bookGetCache = $this->redis->initRedis(config('data_config.book')['select'])
                        ->getCache('book:' . $bookId);

        //缓存过期
        if ($bookGetCache['code'] != 200) {
            
            //查询书籍信息
            $bookGetCache['data'] = db('book')
                        ->where('id', $bookId)
                        ->find();

        } elseif($bookGetCache['data']['status'] == 1) {
            //验证图书状态是否可借阅

            return json([
                'code' => 102,
                'msg'  => '图书已被借阅',
                'data' => null
            ]);

        }
        //学生借阅图书最大数限制
        //查询学生没有归还数量
        $getStuBorrNum = db('borrow')
                    ->where([
                        'student_id' => $studentId,
                        'status' => 0
                    ])->count();

        if ($getStuBorrNum >= config('data_config.borrow_max')) {
            
            return json([
                'code' => 102,
                'msg'  => '每人同时最多可借阅' . config('data_config.borrow_max') . '本书籍',
                'data' => null
            ]);

        }

        //创建借阅记录
        $borrowId = db('borrow')
                ->insertGetId([
                    'book_id'     => $bookId,
                    'student_id'  => $studentId,
                    'back_at'      => $back_at
                ]);


        if ($borrowId) {

            //修改书籍状态
            db('book')
            ->where('id', $bookId)
            ->update(['status' => 1]);

            $bookGetCache['data']['status'] = 1;

            //更新书籍缓存
            $bookSetCache = $this->redis->initRedis(config('data_config.book')['select'])
                        ->setCache('book:' . $bookId, 
                            $bookGetCache['data'], 
                            config('data_config.book')['timeout']);

            //添加缓存
            $borrowSetCache = $this->redis->initRedis(config('data_config.borrow')['select'])
                        ->setCache('borrow:student_' . $studentId . '_book_' . $bookId, 
                            [
                                'id'          => $borrowId,
                                'book_id'     => $bookId,
                                'student_id'  => $studentId,
                                'created_at'  => date('Y-m-d h:i:s'),
                                'back_at'     => $back_at,
                                'status'      => 0
                            ]);
            
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
     * 学生归还书籍
     * @param $bookId    int    书籍id
     * @param $studentId int 学生id
     */
    public function backBooks()
    {
        $bookId = input('bookId');
        $studentId = input('studentId');

        if (empty($bookId) || empty($studentId)) {

            return json([
                'code' => 102,
                'msg'  => '参数错误',
                'data' => null
            ]);

        }

        //验证借阅状态
        $borrowGetCache = $this->redis->initRedis(config('data_config.borrow')['select'])
                    ->getCache('borrow:student_' . $studentId . '_book_' . $bookId);

        //缓存过期
        if ($borrowGetCache['code'] != 200) {
            
            return json([
                'code' => 102,
                'msg'  => '图书与学生不匹配',
                'data' => null
            ]);

        }

        //还书
        $getStuBorrNum = db('borrow')
                    ->where('id', $borrowGetCache['data']['id'])
                    ->update([
                        'status'  => 1,
                        'back_at' => date('Y-m-d h:i:s')
                    ]);
        //删除缓存
        $this->redis->initRedis(config('data_config.borrow')['select'])
                    ->delCache('borrow:student_' . $studentId . '_book_' . $bookId);

        //修改书籍状态

        //验证书籍状态
        $bookGetCache = $this->redis->initRedis(config('data_config.book')['select'])
                        ->getCache('book:' . $bookId);

        //缓存过期
        if ($bookGetCache['code'] != 200) {
            
            //查询书籍信息
            $bookGetCache['data'] = db('book')
                        ->where('id', $bookId)
                        ->find();

        }

        db('book')
            ->where('id', $bookId)
            ->update([
                'status'  => 0,
            ]);

        $bookGetCache['data']['status'] = 0;

        $bookSetCache = $this->redis->initRedis(config('data_config.book')['select'])
                    ->setCache('book:' . $bookId, 
                        $bookGetCache['data'], 
                        config('data_config.book')['timeout']);

        return json([
            'code' => 200,
            'msg'  => 'success',
            'data' => null
        ]);

    }


}
