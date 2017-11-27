<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::get('api/v1/banner/:id', 'api/v1.Banner/getBanner');

/**
 * 举报信息的接口路由设置
 */
Route::post('/api/report/getYzm', 'api/report.Yzm/getYzm');
Route::post('/api/report/addData', 'api/report.Add/addData');
Route::post('/api/report/upload', 'api/report.Upload/uploadImg');
Route::post('/api/report/openid', 'api/report.Openid/getOpenid');
Route::post('/api/report/jssdk', 'api/report.Openid/getJssdk');
Route::get('/report/index', 'api/report.Report/startReport');
Route::post('/api/report/login', 'api/report.Cms/login');
Route::get('/api/report/getData', 'api/report.Cms/getData');
Route::get('/api/report/exit', 'api/report.Cms/drop');

/**
 * 语音识别开关灯
 */
Route::post('/api/voice/jssdk', 'api/report.Voice/getJssdk');
Route::post('/api/voice/light', 'api/report.Voice/light');
Route::get('/api/voice/get', 'api/report.Voice/getLight');

/**
 * 早起打卡
 */
// cms接口
Route::post('/api/clockPunch/cms/login', 'api/clockPunch.Cms/login');
Route::post('/api/clockPunch/cms/changeName', 'api/clockPunch.Cms/changeName');
Route::post('/api/clockPunch/cms/changePassword', 'api/clockPunch.Cms/changePassword');
Route::post('/api/clockPunch/cms/putImage', 'api/clockPunch.Invitation/putImage');
Route::post('/api/clockPunch/cms/getData', 'api/clockPunch.Invitation/getData');
Route::post('/api/clockPunch/cms/ShowCard/changeData', 'api/clockPunch.ShowCard/changeData');
Route::post('/api/clockPunch/cms/ShowCard/getData', 'api/clockPunch.ShowCard/getData');
Route::post('/api/clockPunch/cms/getProgectData', 'api/clockPunch.Project/getData');
Route::post('/api/clockPunch/cms/putProgectData', 'api/clockPunch.Project/putData');
Route::post('/api/clockPunch/cms/uploadProgectImg', 'api/clockPunch.Project/uploadImg');
Route::post('/api/clockPunch/cms/uploadKefuImg', 'api/clockPunch.Cms/uploadKefuImg');
Route::post('/api/clockPunch/cms/getKefuData', 'api/clockPunch.Cms/getKefuData');
Route::post('/api/clockPunch/cms/runtime/upload', 'api/clockPunch.Cms/uploadTime');
Route::post('/api/clockPunch/cms/runtime/getData', 'api/clockPunch.Cms/getTime');
Route::get('/api/clockPunch/cms/runTime', 'api/clockPunch.Cms/runTime');
Route::get('/api/clockPunch/cms/sendAllTemplet', 'api/clockPunch.Cms/sendAllTemplet');
Route::post('/api/clockPunch/cms/ShowData/getMembers', 'api/clockPunch.ShowData/getMembers');
// 微信端接口
Route::any('/api/clockPunch/checkSignature', 'api/clockPunch.Init/checkSignature');
Route::post('/api/clockPunch/jssdk', 'api/clockPunch.Init/getJssdk');
Route::post('/api/clockPunch/sendNews', 'api/clockPunch.Init/sendNews');
Route::post('/api/clockPunch/sendTemplate', 'api/clockPunch.Init/sendTemplate');
Route::post('/api/clockPunch/sendTextNews', 'api/clockPunch.Init/sendTextNews');
Route::post('/api/clockPunch/sendInvitationCard', 'api/clockPunch.Wechat/sendCard');
Route::post('/api/clockPunch/sendPunchCard', 'api/clockPunch.Wechat/punchCard');
Route::post('/api/clockPunch/discList', 'api/clockPunch.Init/discList');
Route::post('/api/clockPunch/isHasOpenid', 'api/clockPunch.Init/isHasOpenid');
Route::post('/api/clockPunch/joinProject', 'api/clockPunch.Init/joinProject');
Route::post('/api/clockPunch/punchInit', 'api/clockPunch.Punch/punchInit');
Route::post('/api/clockPunch/punchDay', 'api/clockPunch.Punch/punchDay');
Route::post('/api/clockPunch/likeDay', 'api/clockPunch.Like/likeDay');
Route::post('/api/clockPunch/warn', 'api/clockPunch.Wechat/warn');
Route::post('/api/clockPunch/sendImg', 'api/clockPunch.Wechat/sendImg');