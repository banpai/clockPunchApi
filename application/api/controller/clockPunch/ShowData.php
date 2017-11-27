<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/27 0027
 * Time: 9:58
 */

namespace app\api\controller\clockPunch;


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
        $size = 10;//每页显示数量
        // 查询数据
        $list = Db::table('t_members');
        if($startDate && $endDate){
            $list->where('createTime','between time',[$startDate,$endDate]);
        }
        if($searchName){
            $list->where(function ($query) use ($searchName) {
                $query
                    ->where('nickname', 'like', $searchName)
                    ->whereOr('country', 'like', $searchName)
                    ->whereOr('city', 'like', $searchName)
                    ->whereOr('province', 'like', $searchName);
            });
        }
        $list->order('createTime desc')
            ->limit(($current_page-1)*$size, $size);
        // 查询数据
        $listData = $list->select();
        // 查询总数
        $countList = Db::table('t_members');
        if($startDate && $endDate){
            $countList->where('createTime','between time',[$startDate,$endDate]);
        }
        if($searchName){
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
}