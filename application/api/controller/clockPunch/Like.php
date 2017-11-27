<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/16 0016
 * Time: 16:14
 */

namespace app\api\controller\clockPunch;


use app\api\validate\OpenidValidate;
use app\api\validate\OtherOpenidValidate;
use app\api\model\Like as LikeModel;

class Like
{
    //用户点赞的接口
    public function likeDay(){
        (new OpenidValidate())->goCheck();
        (new OtherOpenidValidate())->goCheck();
        $openid = input('openid');
        $otherOpenid = input('otheropenid');
        $like = new LikeModel($openid, $otherOpenid);
        $flag = $like->isTodayLike();
        $backData = [
            'err_code' => 0
        ];
        if($flag){
            $backData['err_code'] = 1;
            $backData['msg'] = '今天已经点过赞了';
            return json($backData);
        }else{
            $likestatus = $like->toLike();
            if($likestatus){
                $backData['msg'] = '点赞成功！';
                return json($backData);
            }else{
                $backData['err_code'] = 1;
                $backData['msg'] = '点赞失败！';
                return json($backData);
            }
        }
    }
}