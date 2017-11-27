<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/27 0027
 * Time: 11:23
 */

namespace app\api\model;

use think\Db;
use think\Session;

class AddReport
{
    public static function addData($data){
        $msg = [
            'code'=> '1',
            'msg'=> '失败'
        ];
        /**
         * 5分钟后验证码过气
         */
        if(Session::has('yzm_date')){
            $now = time();
            $min = ($now - Session::get('yzm_date'))/60;
            if($min > 5){
                $msg['msg'] = '验证码时间过期！';
                return $msg;
            }
        }
        /**
         * 判断openid
         */
        $openid = '';
        if(Session::get('openid')){
            $openid = Session::get('openid');
        }
        if(Session::get('yzm') == $data['yzm']){
            /**
             * 根据serverId从微信服务器拉去图片，存到本地
             */
            $serverId = input('serverId');
            $saveImgPath = '';
//            var_dump($serverId);
            if($serverId != ''){
                $accessToken = Session::get('accessToken');
                // 要存在你服务器哪个位置？
                $saveImgPath = '/uploads'."/wechat/".date('YmdHis').'.jpg';
                $savePathFile = ROOT_PATH . 'public' . DS .$saveImgPath;
                $targetName = $savePathFile;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                $fp = fopen($targetName,'wb');
                curl_setopt($ch,CURLOPT_URL,"http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$accessToken}&media_id={$serverId}");
                curl_setopt($ch,CURLOPT_FILE,$fp);
                curl_setopt($ch,CURLOPT_HEADER,0);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
            }
            $updateTime = time();
            $createTime = date("Y-m-d H:i:s", $updateTime);
            $data = [
                'openid' => $openid,
                'name' => $data['name'],
                'tel' => $data['tel'],
                'address' => $data['address'],
                'detail' => $data['detail'],
                'picpath' => $saveImgPath,
                'entkbn' => '0',
                'updateTime' => $createTime,
                'createTime' => $createTime
            ];
            $result = Db::table('t_tips')->insert($data);
            if($result){
                $msg['code'] = '0';
                $msg['msg'] = '成功';
            }
        }else{
            $msg['msg'] = '验证码错误！';
        }
        return $msg;
    }
}