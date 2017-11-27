<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/30 0030
 * Time: 10:00
 */

namespace app\api\controller\report;


use think\Cookie;
use think\Db;

class Cms
{
    public function drop(){
        Cookie::delete('cms_username');
        return json([
            'code'=> 0,
            'msg'=> '成功'
        ]);
    }
    public function login(){
        $cms_username = input('ac');
        $cms_pass = input('se');
        if(config('cms_username') == $cms_username && config('cms_pass')){
//            setCookie("username",$_POST['username'],time()+3600*24*30);
            Cookie::set('cms_username', $cms_username, 30*60);
            return json([
                'code'=> 0,
                'msg'=> '登录成功'
            ]);
        }else{
            return json([
                'code'=> 1,
                'msg'=> '登录失败'
            ]);
        }
    }
    public function getData(){
        if(Cookie::has('cms_username')){
            if(Cookie::get('cms_username') != config('cms_username')){
                $url = '/cms/pages/login.html';
                return json([
                    'code'=> 1,
                    'msg'=> 'cookie失效'
                ]);
            }
        }else{
            $url = '/cms/pages/login.html';
            return json([
                'code'=> 1,
                'msg'=> 'cookie失效'
            ]);
        }
        $startTime = input('startTime');
        $endTime = input('endTime');
        $report_value = '%'.input('report_value').'%';
        if($startTime && $endTime){
            $list = Db::name('t_tips')
                ->where('createTime','between time',[$startTime,$endTime])
                ->where(function ($query) use ($report_value) {
                    $query->where('name', 'like', $report_value)
                        ->whereOr('tel', 'like', $report_value)
                        ->whereOr('address', 'like', $report_value);
                })
                ->order('createTime desc')
                ->paginate(10);
            return json($list);
        }else if ($startTime && (!$endTime)){
            $list = Db::name('t_tips')
                ->where('createTime','>= time',$startTime)
                ->where(function ($query) use ($report_value) {
                    $query->where('name','like',$report_value)
                        ->whereOr('tel','like',$report_value)
                        ->whereOr('address','like',$report_value);
                })->order('createTime desc')->paginate(10);
            return json($list);
        }else if((!$startTime) && $endTime){
            $list = Db::name('t_tips')
                ->where('createTime','<= time',$endTime)
                ->where(function ($query) use ($report_value){
                    $query->where('name','like',$report_value)
                        ->whereOr('tel','like',$report_value)
                        ->whereOr('address','like',$report_value);
                })->order('createTime desc')->paginate(10);
            return json($list);
        }else if((!$startTime) && (!$endTime)){
            $list = Db::name('t_tips')
                ->where('name','like',$report_value)
                ->whereOr('tel','like',$report_value)
                ->whereOr('address','like',$report_value)
                ->order('createTime desc')->paginate(10);
            return json($list);
        }
    }
}