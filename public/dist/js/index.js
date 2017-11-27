/**
 * Created by 解飞.
 * on 2017/10/26.
 */
var localIds = '';

/**
 * 上传图片
 */
function serverPic(cb){
    // alert(typeof(localIds));
    // alert(localIds[0]);
    // var index = localIds.lastIndexOf("\\");  
    // alert(index);
    // alert(localIds.length);
    // var id  = localIds.substring(19, localIds.length);
    // alert(id);
    var id = localIds;
    if(typeof(localIds) == 'object'){
        id = localIds[0];
    }
    // alert(id);
    wx.uploadImage({
        localId: id, // 需要上传的图片的本地ID，由chooseImage接口获得
        isShowProgressTips: 1, // 默认为1，显示进度提示
        success: function (res) {
            // alert(res.serverId);
            var serverId = res.serverId; // 返回图片的服务器端ID
            cb(serverId);
        },
        fail: function(res) {
            weui.alert(JSON.stringify(res));
        }
    });
}

// 图片路径
var picpath = '';
// 上传个数统计
var uploadCount = 0;
// 上传图片的flag
var uppicfalg = true;
// 上传图片dom

// var uppicdom;
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}

/**
 * 倒计时60秒回调
 */
var timeBack = (function () {
    var time = 60;
    var init = function (cb) {
        if (time > 0) {
            time = time - 1;
            cb(time, false);
            setTimeout(function () {
                init(cb);
            }, 1000);
        } else {
            cb(60, true);
            time = 60;
        }
    }
    return {
        init: init
    }
}());

/**
 * 接口封装
 */
var api = (function () {
    var host = '';
    /**
     * 获取验证码的接口
     * @url /getyzm
     * @http POST
     * @tel 传递的手机号
     * @
     */
    var getYzm = function (data, cb) {
        $.post('/api/report/getYzm', data, function (response) {
            console.log(response);
            if (cb) {
                cb(response);
            }
            // process response
        });
    };
    /**
     * 插入数据的接口
     */
    var addData = function (data, cb) {
        $.post('/api/report/addData', data, function (response) {
            console.log(response);
            if (cb) {
                cb(response);
            }
            // process response
        });
    }
    /**
     * 初始化获取openid
     */
    var getopenid = function (cb) {
        var url = window.location.href;
        var data = {
            code: getQueryString('code'),
            url: url
        }
        $.post('/api/report/jssdk', data, function (response) {
            console.log(response);
            if(response.signPackage){
                var signPackage = response.signPackage;
                wx.config({
                    debug: false,
                    appId: signPackage["appId"],
                    timestamp: signPackage["timestamp"],
                    nonceStr: signPackage["nonceStr"],
                    signature: signPackage["signature"],
                    jsApiList: [
                        // 所有要调用的 API 都要加到这个列表中
                        'chooseImage',
                        'previewImage',
                        'uploadImage'
                    ]
                  });
                  wx.ready(function () {
                    
                });
            }
            if (response.code == '0') {
                if (cb) {
                    cb(response);
                }
            } else {
                window.location.href = response.url;
            }
        });
    };
    return {
        getYzm: getYzm,
        addData: addData,
        getopenid: getopenid
    }
}());
/**
 * 页面方法
 */
var pagefun = (function () {
    /**
     * check称呼
     */
    var checkName = function (name, cb) {
        if (name.length > 50) {
            weui.topTips('称呼少于50字!');
        } else {
            cb();
        }
    };
    /**
     * check地点
     */
    var checkAddress = function (address, cb) {
        console.log(address);
        console.log(!address);
        if (!address) {
            weui.topTips('请填写投诉地点！');
        } else if (name.length > 100) {
            weui.topTips('称呼少于50字');
        } else {
            cb();
        }
    };
    /**
     * check手机号
     */
    var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    var checkPhone = function (tel, cb) {
        if (!myreg.test(tel)) {
            weui.topTips('请输入有效的手机号码！');
            return false;
        } else {
            cb();
        }
    };
    /**
     * check地点
     */
    var checkDetail = function (detail, cb) {
        if (!detail) {
            weui.topTips('请填写详细描述！');
        } else if (name.length > 300) {
            weui.topTips('详细描述少于300字');
        } else {
            cb();
        }
    };
    /**
     * check验证码
     */
    var re = /^[1-9]+[0-9]*]*$/;
    var checkYzm = function (yzm, cb) {
        if (yzm.length != 6) {
            weui.topTips('验证码错误！');
        } else {
            cb();
        }
    };
    /**
     * 获取验证码
     */
    var getYzmflag = true;
    var getYzm = function () {
        checkPhone($('#phonenum').val(), function () {
            if (getYzmflag) {
                getYzmflag = false;
                // 差调取后台的接口
                api.getYzm({
                    tel: $('#phonenum').val()
                }, function (m) {
                    if (m.code != 0) {
                        if (m.msg) {
                            weui.topTips(m.msg);
                        } else {
                            weui.topTips('短信发送失败！');
                        }
                    }
                });
                timeBack.init(function (time, bol) {
                    if (bol) {
                        getYzmflag = true;
                        $('.weui-vcode-btn').html('获取验证码');
                    } else {
                        $('.weui-vcode-btn').html(time);
                    }
                });
            } else {
                weui.topTips('60秒后再获取验证码', 3000);
            }
        });
    };
    /**
     * 提交数据
     */
    var sub = function () {
        var data = {
            name: $('#report_name').val(),
            tel: $('#phonenum').val(),
            yzm: $('#yzm').val(),
            address: $('#report_kkkkkkkk').val(),
            detail: $('#content_report').val(),
            picpath: picpath
        };
        checkYzm(data.yzm, function () {
            checkName(data.name, function () {
                checkPhone(data.tel, function () {
                    checkAddress(data.address, function () {
                        checkDetail(data.detail, function () {
                            // weui.alert(localIds);
                            if(localIds == ''){
                                // alert('d');
                                var loading = weui.loading('上传中', {
                                    className: 'custom-classname'
                                });
                                // alert(serverId);
                                data.serverId = '';
                                api.addData(data, function (m) {
                                    // alert(JSON.stringify(m));
                                    if (m.code === '0') {
                                        loading.hide(function () {
                                            console.log('上传成功');
                                        });
                                        weui.toast('操作成功', {
                                            duration: 1500,
                                            callback: function () {
                                                window.location.reload();
                                            }
                                        });
                                    } else {
                                        loading.hide(function () {
                                            console.log('上传失败');
                                        });
                                        weui.topTips('验证码错误！');
                                    }
                                });
                            }else{
                                serverPic(function(serverId){
                                    var loading = weui.loading('上传中', {
                                        className: 'custom-classname'
                                    });
                                    // alert(serverId);
                                    data.serverId = serverId;
                                    api.addData(data, function (m) {
                                        // alert(JSON.stringify(m));
                                        if (m.code === '0') {
                                            loading.hide(function () {
                                                console.log('上传成功');
                                            });
                                            weui.toast('操作成功', {
                                                duration: 1500,
                                                callback: function () {
                                                    window.location.reload();
                                                }
                                            });
                                        } else {
                                            loading.hide(function () {
                                                console.log('上传失败');
                                            });
                                            weui.topTips('验证码错误！');
                                        }
                                    });
                                });
                            }
                           
                        });
                    });
                });
            });
        });
    };
    /**
     * 删除图片
     */
    var del = function (dom) {
        uploadCount = 0;
        $('.weui-uploader__file').remove();
        console.log('del');
    };
    return {
        getYzm: getYzm,
        sub: sub,
        del: del
    }
}());

/**
 * 初始化
 */
$(function () {
    /**
     * 解决移动端300毫秒的等待时间
     */
    function fastClick() {
        var supportTouch = function () {
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        }();
        var _old$On = $.fn.on;

        $.fn.on = function () {
            if (/click/.test(arguments[0]) && typeof arguments[1] == 'function' && supportTouch) { // 只扩展支持touch的当前元素的click事件
                var touchStartY, callback = arguments[1];
                _old$On.apply(this, ['touchstart', function (e) {
                    touchStartY = e.changedTouches[0].clientY;
                }]);
                _old$On.apply(this, ['touchend', function (e) {
                    if (Math.abs(e.changedTouches[0].clientY - touchStartY) > 10) return;

                    e.preventDefault();
                    callback.apply(this, [e]);
                }]);
            } else {
                _old$On.apply(this, arguments);
            }
            return this;
        };
    }
    /**
     *  .container 设置了 overflow 属性, 导致 Android 手机下输入框获取焦点时, 输入法挡住输入框的 bug
     * 相关 issue: https://github.com/weui/weui/issues/15
     * 解决方法:
     * 0. .container 去掉 overflow 属性, 但此 demo 下会引发别的问题
     * 1. 参考 http://stackoverflow.com/questions/23757345/android-does-not-correctly-scroll-on-input-focus-if-not-body-element
     * Android 手机下, input 或 textarea 元素聚焦时, 主动滚一把
     */
    function androidInputBugFix() {
        if (/Android/gi.test(navigator.userAgent)) {
            window.addEventListener('resize', function () {
                if (document.activeElement.tagName == 'INPUT' || document.activeElement.tagName == 'TEXTAREA') {
                    window.setTimeout(function () {
                        document.activeElement.scrollIntoViewIfNeeded();
                    }, 0);
                }
            })
        }
    }

    /**
     * 设置微信API
     */
    function setJSAPI() {
        var option = {
            title: 'WeUI, 为微信 Web 服务量身设计',
            desc: 'WeUI, 为微信 Web 服务量身设计',
            link: "https://weui.io",
            imgUrl: 'https://mmbiz.qpic.cn/mmemoticon/ajNVdqHZLLA16apETUPXh9Q5GLpSic7lGuiaic0jqMt4UY8P4KHSBpEWgM7uMlbxxnVR7596b3NPjUfwg7cFbfCtA/0'
        };
        $.getJSON('https://weui.io/api/sign?url=' + encodeURIComponent(location.href.split('#')[0]), function (res) {
            wx.config({
                beta: true,
                debug: true,
                appId: res.appid,
                timestamp: res.timestamp,
                nonceStr: res.nonceStr,
                signature: res.signature,
                jsApiList: [
                    'onMenuShareTimeline',
                    'onMenuShareAppMessage',
                    'onMenuShareQQ',
                    'onMenuShareWeibo',
                    'onMenuShareQZone',
                    // 'setNavigationBarColor',
                    'setBounceBackground'
                ]
            });
            wx.ready(function () {
                wx.onMenuShareTimeline(option);
                wx.onMenuShareQQ(option);
                wx.onMenuShareAppMessage({
                    title: 'WeUI',
                    desc: '为微信 Web 服务量身设计',
                    link: location.href,
                    imgUrl: 'https://mmbiz.qpic.cn/mmemoticon/ajNVdqHZLLA16apETUPXh9Q5GLpSic7lGuiaic0jqMt4UY8P4KHSBpEWgM7uMlbxxnVR7596b3NPjUfwg7cFbfCtA/0'
                });
            });
        });
    }
    /**
     * 表单
     */
    function biaodan() {
        var $tooltips = $('.js_tooltips');
        $('#showTooltips').on('click', function () {
            if ($tooltips.css('display') != 'none') return;
            // toptips的fixed, 如果有`animation`, `position: fixed`不生效
            $('.page.cell').removeClass('slideIn');
            
            $tooltips.css('display', 'block');
            setTimeout(function () {
                $tooltips.css('display', 'none');
            }, 2000);
        });
    }
    /**
     * 图片显示初始化
     */
    function showpic() {
        var tmpl = '<li class="weui-uploader__file" style="background-image:url(#url#)"></li>',
            $gallery = $("#gallery"), $galleryImg = $("#galleryImg"),
            $uploaderInput = $("#uploaderInput"),
            $uploaderFiles = $("#uploaderFiles");
        // $uploaderInput.on("change", function (e) {
        //     var src, url = window.URL || window.webkitURL || window.mozURL, files = e.target.files;
        //     for (var i = 0, len = files.length; i < len; ++i) {
        //         var file = files[i];
        //         if (url) {
        //             src = url.createObjectURL(file);
        //         } else {
        //             src = e.target.result;
        //         }
        //         $uploaderFiles.append($(tmpl.replace('#url#', src)));
        //     }
        // });
        $uploaderFiles.on("click", "li", function () {
            $galleryImg.attr("style", this.getAttribute("style"));
            $gallery.fadeIn(100);
        });
        $gallery.on("click", function () {
            $gallery.fadeOut(100);
        });
    }
    /**
     * 上传图片
     */
    function uppic() {
        weui.uploader('#uploader', {
            url: '/api/report/upload',
            auto: true,
            type: 'base64',
            fileVal: 'fileVal',
            compress: {
                width: 1600,
                height: 1600,
                quality: .8
            },
            onBeforeQueued: function (files) {
                // `this` 是轮询到的文件, `files` 是所有文件

                if (["image/jpg", "image/jpeg", "image/png", "image/gif"].indexOf(this.type) < 0) {
                    weui.alert('请上传图片');
                    return false; // 阻止文件添加
                }
                if (this.size > 10 * 1024 * 1024) {
                    weui.alert('请上传不超过10M的图片');
                    return false;
                }
                if (files.length > 1) { // 防止一下子选择过多文件
                    weui.alert('最多只能上传1张图片，请重新选择');
                    return false;
                }
                if (uploadCount + 1 > 1) {
                    weui.alert('最多只能上传1张图片');
                    return false;
                }

                ++uploadCount;

                // return true; // 阻止默认行为，不插入预览图的框架
            },
            onQueued: function () {
                uppicfalg = false;
                console.log(this);
                uppicdom = this;
                // console.log(this.status); // 文件的状态：'ready', 'progress', 'success', 'fail'
                // console.log(this.base64); // 如果是base64上传，file.base64可以获得文件的base64

                // this.upload(); // 如果是手动上传，这里可以通过调用upload来实现；也可以用它来实现重传。
                // this.stop(); // 中断上传

                // return true; // 阻止默认行为，不显示预览图的图像
            },
            onBeforeSend: function (data, headers) {
                console.log(this, data, headers);
                // $.extend(data, { test: 1 }); // 可以扩展此对象来控制上传参数
                // $.extend(headers, { Origin: 'http://127.0.0.1' }); // 可以扩展此对象来控制上传头部
                // return false; // 阻止文件上传
            },
            onProgress: function (procent) {
                console.log(this, procent);
                // return true; // 阻止默认行为，不使用默认的进度显示
            },
            onSuccess: function (ret) {
                uppicfalg = true;
                picpath = ret.path;
                console.log(ret);
                // return true; // 阻止默认行为，不使用默认的成功态
            },
            onError: function (err) {
                uppicfalg = true;
                console.log(this, err);
                // return true; // 阻止默认行为，不使用默认的失败态
            }
        });
    }
    /**
     * 监听文本框
     */
    function listntext() {
        $('#content_report').on('input', function (e) {
            var len = $('#content_report').val().length;
            //实时监听手机号码输入框变化
            if (len > 300) {
                var str = $('#content_report').val().substring(0, 300);
                $('#content_report').val(str);
                $('#content_report_num').html(300);
            } else {
                $('#content_report_num').html(len);
            }
        });
    }
    function init() {
            // alert('dlll');
        if (getQueryString('code')) {
            api.getopenid(function(){
                // setJSAPI();
            });
            uppic();
            fastClick();
            androidInputBugFix();
            // uppic();
            biaodan();
            listntext();
            // showpic();
            $('.weui-tab__content').show();
            $('.report_head').show();
        } else {
            // 跳转授权页面
            api.getopenid();
        }
    }
    init();
});

/**
 * 打开图片
 */
function bp_uppic(){
    wx.chooseImage({
        count: 1, // 默认9
        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
        success: function (res) {
            $('#uploaderFiles li').remove();
            localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
            var src = localIds;
            if(localIds[0]){
                src = localIds[0];
            }
            // alert(src);
            var str = '<li class="weui-uploader__file" data-id="1"  webkit-playsinline playsinline style="overflow: hidden;" onclick="showpic()"><img src="'+src+'" style="width:100%;height:100%;" alt=""></li>';
            $('#uploaderFiles').append(str);
        }
    });
}

/**
 * 预览图片
 */
function showpic(){
    wx.previewImage({
        current: '', // 当前显示图片的http链接
        urls: localIds // 需要预览的图片http链接列表
    });
}

