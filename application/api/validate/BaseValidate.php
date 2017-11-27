<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/26 0026
 * Time: 0:58
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck(){
        // 获取http传入的参数
        // 对这些参数做校验
        $request = Request::instance();
        $params = $request->param();
        $result = $this->batch()->check($params);
        if(!$result){
            $e = new ParameterException([
                'msg' => $this->error
            ]);
            throw $e;
        }else{
            return true;
        }
    }
}