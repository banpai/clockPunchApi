<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/14 0014
 * Time: 16:50
 */

namespace app\api\model;


use think\Model;

class Project extends Model
{
    protected $table = 't_project';

    /**
     * 获取id为1的数据
     */
    public function getProject(){
        $data = $this->where('id','1')->find();
        if($data){
            $Time = time();
            $createTime = date("Y-m-d", $Time);
            $data['image'] = str_replace("\\", "/", $data['image']);
            $data['nowtime'] = $createTime;
        }
        return $data;
    }
    /*
     * 修改id为1的数据
     */
    public function changeProject($data){
        $project = $this::get(1);
        $Time = time();
        $createTime = date("Y-m-d H:i:s", $Time);
        if($data['title']){
            $project->title = $data['title'];
        }
        if($data['subtitle']){
            $project->subtitle = $data['subtitle'];
        }
        if($data['content']){
            $project->contant = $data['content'];
        }
        if ($data['image']){
            $project->image = $data['image'];
        }
        $project->flag = 1;
        $project->updateTime = $createTime;
        $data = $project->save();
        return $data;
    }
}