<?php
namespace app\admin\service;

use app\common\redis\Redis;
use think\Model;
use Request;

class Base extends Model
{

    //reids
    protected $redis = null;

    //分页每页条数
    protected $pages = 20;

    //admin信息
    protected $admin = null;

    //初始化

	public function initialize()
    {

    	parent::initialize();

    	//获取登陆者信息

        //实例化redis
        $this->redis = new Redis();

        $token = Request::instance()->header()['token'] ?? input('token');

        //测试使用,behavior已经验证token了，这步不需要验证了
        $adminGetCache = $this->redis->initRedis(config('data_config.admin')['select'])
            ->getCache($token);

        if ($adminGetCache['code'] == 200) {

            $this->admin = $adminGetCache['data'];

        }

    }

}
