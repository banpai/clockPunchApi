<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/20 0020
 * Time: 13:31
 */

namespace app\api\model;


use think\Model;

class FriendShip extends Model
{
    protected $table = 't_friendship';
    private $openId;
    private $otherOpenId;
    public function __construct($openid, $otherOpenid) {
        $this->openId = $openid;
        $this->otherOpenId = $otherOpenid;
    }
    /*
     * 判断是否存在该条数据
     */
    public function isFriendShip(){
        $fiend = $this
            ->where([
                'openid' => $this->openId,
                'otheropenid' => $this->otherOpenId
            ])
            ->find();
        if($fiend){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 增加新的好友
     */
    public function addFriends(){
        $Time = time();
        $createTime = date("Y-m-d H:i:s", $Time);
        $this->openid = $this->openId;
        $this->otheropenid = $this->otherOpenId;
        $this->version = '1';
        $this->createTime = $createTime;
        $this->updateTime = $createTime;
        $this->activity = 1;
        $flag = $this->save();
        if($flag){
            return $flag;
        }else{
            return false;
        }
    }
}