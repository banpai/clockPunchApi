<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/15 0015
 * Time: 10:36
 */

namespace app\api\model;


use think\Db;
use think\Model;

class Punch extends Model
{
    protected $table = 't_punch';

    private $openId;

    public function __construct($openid) {
        $this->openId = $openid;
    }
    /*
     * 判断该openid下,今天有没有打过卡
     * 如果已经打卡，则返回打卡的时间
     */
    public function isTodayPunch(){
        $Time = time();
        $Day = date("Y-m-d", $Time);
        $punch = $this
            ->where([
                'openid' => $this->openId,
                'punchDate' => $Day
            ])
            ->find();
        if($punch){
            return $punch;
        }else{
            return false;
        }
    }
    /*
     * 根据openid打卡，添加数据
     */
    public function toPunch(){
        $Time = time();
        $day = date("Y-m-d", $Time);
        $time = date("H:i:s", $Time);
        $this->openid = $this->openId;
        $this->punchDate = $day;
        $this->version = '1';
        $this->punchtatus = 1;
        $this->punchTime = $time;
        $flag = $this->save();
        // 添加user表的连续打卡天数的数据
        $userDate = Db::table('t_members')->where('openid', $this->openId)->find();
        // 计算这次打卡时间和上次打卡时间的差距
        $flagTime = (strtotime($day)-strtotime($userDate['punchDate']))/(3600*24);
        $punchDays = 1;
        if($flagTime <= 1){
            $punchDays = ($userDate['punchDays'] + 1);
        }
        Db::table('t_members')
            ->where('openid', $this->openId)
            ->update([
                'punchDate' => $day,
                'punchDays' => $punchDays
            ]);
        if($flag){
            return array([
                'punchTime' => $this->punchTime,
                'punchDays' => $punchDays
            ]);
        }else{
            return false;
        }
    }
    /*
     * 判断该openid下，连续打卡的天数
     */
    public function continuousPunch(){

    }
    /*
     * 判断该openid下，累积打卡的天数
     */
    public function countPunch(){
        $count = $this->where('openid',$this->openId)->count();
        return $count;
    }
    /*
     * 判断两个openid之间，共同打开的天数
     */

}