<?php
namespace app\common\redis;

use think\Config;   //加载配置

/**
 * Class Redis redis封装
 * @package app\common\redis
 */
class Redis
{
    //声明
    protected $Redis;

    /**
    * 连接redis
    * @param $select_num redis数据库号，默认0号库
     * @return $this
    */
    public function initRedis($select_num = 0){


        $this->Redis = new \Redis();

        $this->Redis->pconnect(config("cache.redis")['host'], config("cache.redis")['port']);

        $this->Redis->auth(config("cache.redis")['password']);

        $this->Redis->select($select_num);

        return $this;

    }


    /**
     * redis-get操作  获取缓存
     * @return $this
     */

    public function getCache($keyName)
    {

        $redis_result =  $this->Redis->get($keyName);

        if($redis_result){

            if(is_null(json_decode($redis_result))){

                return [
                    'code' => 200,
                    'msg'  => 'success',
                    'data' => $redis_result
                ];

            }else{

                return [
                    'code' => 200,
                    'msg'  => 'success',
                    'data' => json_decode($redis_result,true)
                ];

            }

        }else{

            return [
                'code' => 102,
                'msg'  => '根据keyName找不到缓存值',
                'data' => $keyName
            ];

        }


    }

    /**
     * redis-set操作
     * @param $keyName string 
     * @param $value string 值
     * @param $ttl int 过期时间
     */

    public function setCache($keyName, $value,$ttl=null)
    {

        $value = is_array($value) ? json_encode($value):$value;

        if($ttl)
        {
            $ttl = intval($ttl);
        }

        if($this->Redis->set($keyName, $value, $ttl))
        {

            return [
                'code' => 200,
                'msg'  => 'success',
                'data' => ['key'=>$keyName,'value'=>$value]
            ];

        }else{

            return [
                'code' => 102,
                'msg'  => '失败',
                'data' => ['key'=>$keyName,'value'=>$value]
            ];

        }


    }

    /**
     * 删除
     * @param $keyName
     */
    public function delCache($keyName)
    {

        if( $this->Redis->del($keyName))
        {

            return [
                'code' => 200,
                'msg'  => 'success',
                'data' => null
            ];

        }else{

            return [
                'code' => 102,
                'msg'  => '不存在key值',
                'data' => ['key'=>$keyName]
            ];

        }


    }


}