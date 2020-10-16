<?php
namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;

class Http extends Handle
{
    //错误信息处理
    public function render(Exception $e)
    {

        // 参数验证错误
        if ($e instanceof HttpException){

            $json['code'] = $e->getStatusCode();

            $json['msg'] = 'API地址不存在';

            $json['data'] = '';

        }else{

            $json['code'] = 402;

            $json['msg'] = '参数错误';

            $json['data'] = $e->getMessage();

        }

        return  json($json);


    }

}