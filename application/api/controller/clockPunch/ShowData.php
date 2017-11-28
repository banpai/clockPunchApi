<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/27 0027
 * Time: 9:58
 */

namespace app\api\controller\clockPunch;


use app\api\validate\OpenidValidate;
use think\Db;

class ShowData
{
    /*
     * 获取用户信息的分页数据
     */
    public function getMembers(){
        $current_page = input('current_page');
        if(!$current_page){
            $current_page = 1;
        }
        $startDate = input('startDate');
        $endDate = input('endDate');
        $searchName = input('searchName');
        $size = 9;//每页显示数量
        // 查询数据
        $list = Db::table('t_members');
        if($startDate && $endDate){
            $list->where('createTime','between time',[$startDate,$endDate]);
        }
        if($searchName){
            $searchName = '%' . $searchName . '%';
            $list->where(function ($query) use ($searchName) {
                $query
                    ->where('nickname', 'like', $searchName)
                    ->whereOr('country', 'like', $searchName)
                    ->whereOr('city', 'like', $searchName)
                    ->whereOr('province', 'like', $searchName);
            });
        }
        $list->order('createTime desc')
            ->limit(($current_page-1)*$size, $current_page*$size);
        // 查询数据
        $listData = $list->select();
        // 查询总数
        $countList = Db::table('t_members');
        if($startDate && $endDate){
            $countList->where('createTime','between time',[$startDate,$endDate]);
        }
        if($searchName){
            $searchName = '%' . $searchName . '%';
            $countList->where(function ($query) use ($searchName) {
                $query
                    ->where('nickname', 'like', $searchName)
                    ->whereOr('country', 'like', $searchName)
                    ->whereOr('city', 'like', $searchName)
                    ->whereOr('province', 'like', $searchName);
            });
        }
        $count = $countList->count();
        $data = [
            'total' => $count,
            'data' => $listData,
            'per_page' => $size
        ];
        return $data;
    }
    /**
     * 获取用户打卡数据
     */
    public function getPunchDate(){
        (new OpenidValidate())->goCheck();
        $openid = input('openid');
        $current_page = input('current_page');
        if(!$current_page){
            $current_page = 1;
        }
        $startDate = input('startDate');
        $endDate = input('endDate');
        $startTime = input('startTime');
        $endTime = input('endTime');
        $size = 9;//每页显示数量
        // 查询数据
        $list = Db::table('t_punch');
        $list->where('openid','=',$openid);
        if($startDate && $endDate){
            $list->where('punchDate','between time',[$startDate,$endDate]);
        }
        if($startTime && $endTime){
            $list->where('punchTime','between',[$startTime,$endTime]);
        }
        $list->order('punchDate desc')
            ->limit(($current_page-1)*$size, $current_page*$size);
        // 查询数据
        $listData = $list->select();
        // 查询总数
        $countList = Db::table('t_punch');
        $countList->where('openid','=',$openid);
        if($startDate && $endDate){
            $countList->where('punchDate','between time',[$startDate,$endDate]);
        }
        if($startTime && $endTime){
            $countList->where('punchTime','between',[$startTime,$endTime]);
        }
        $count = $countList->count();
        $data = [
            'total' => $count,
            'data' => $listData,
            'per_page' => $size
        ];
        return $data;
    }
    /**
     * 获取用户好友数据
     */
    public function getFriendsDate(){
        (new OpenidValidate())->goCheck();
        $openid = input('openid');
        $current_page = input('current_page');
        if(!$current_page){
            $current_page = 1;
        }
        $startDate = input('startDate');
        $endDate = input('endDate');
        $searchName = input('searchName');
        $size = 9;//每页显示数量
        // 查询数据
        $list = Db::field('members.*')->table([
            't_members' => 'members',
            't_friendship' => 'friendship'
        ]);
        $list->where('friendship.openid','=',$openid);
        $list->where('friendship.otheropenid = members.openid');
        if($startDate && $endDate){
            $list->where('members.createTime','between time',[$startDate,$endDate]);
        }
        if($searchName){
            $searchName = '%' . $searchName . '%';
            $list->where(function ($query) use ($searchName) {
                $query
                    ->where('members.nickname', 'like', $searchName)
                    ->whereOr('members.country', 'like', $searchName)
                    ->whereOr('members.city', 'like', $searchName)
                    ->whereOr('members.province', 'like', $searchName);
            });
        }
        $list->order('members.createTime desc')
            ->limit(($current_page-1)*$size, $current_page*$size);
        // 查询数据
        $listData = $list->select();
        // 查询总数
        $countList = Db::field('members.*')->table([
            't_members' => 'members',
            't_friendship' => 'friendship'
        ]);
        $countList->where('friendship.openid','=',$openid);
        $countList->where('friendship.otheropenid = members.openid');
        if($startDate && $endDate){
            $countList->where('members.createTime','between time',[$startDate,$endDate]);
        }
        if($searchName){
            $searchName = '%' . $searchName . '%';
            $countList->where(function ($query) use ($searchName) {
                $query
                    ->where('members.nickname', 'like', $searchName)
                    ->whereOr('members.country', 'like', $searchName)
                    ->whereOr('members.city', 'like', $searchName)
                    ->whereOr('members.province', 'like', $searchName);
            });
        }
        $count = $countList->count();
        $data = [
            'total' => $count,
            'data' => $listData,
            'per_page' => $size
        ];
        return $data;
    }
}