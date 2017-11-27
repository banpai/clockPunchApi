<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/16 0016
 * Time: 16:33
 */

namespace app\api\model;


use think\Model;

class Like extends Model
{
    protected $table = 't_like';
    private $openId;
    private $otherOpenId;

    public function __construct($openid, $otherOpenid) {
        $this->openId = $openid;
        $this->otherOpenId = $otherOpenid;
    }
    /*
     * 判断该openid下，有没有点赞过otheropenid
     * 如果已经点赞则返回true,如果没有点赞返回false
     */
    public function isTodayLike(){
        $Time = time();
        $Day = date("Y-m-d", $Time);
        $like = $this
            ->where([
                'openid' => $this->openId,
                'otheropenid' => $this->otherOpenId,
                'likeDate' => $Day
            ])
            ->find();
        if($like){
            return true;
        }else{
            return false;
        }
    }
    /**
     *  增加点赞的数据
     */
    public function toLike(){
        $Time = time();
        $day = date("Y-m-d", $Time);
        $time = date("H:i:s", $Time);
        $this->openid = $this->openId;
        $this->otheropenid = $this->otherOpenId;
        $this->likeTime = $time;
        $this->likeDate = $day;
        $this->version = '1';
        $this->likestatus = 1;
        $flag = $this->save();
        if($flag){
            return $this->likestatus;
        }else{
            return false;
        }
    }
}