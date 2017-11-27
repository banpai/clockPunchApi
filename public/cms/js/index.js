var pageIndex = 1,
    moreDataFlag = true;


/*
    * 获取数据 分页
    * params:
    * pageIndex - {int} 分页下表  1开始
    */
function getOrders() {
    var startTime = $('#startTime').val();
    var endtime = $('#endtime').val();
    var report_value = $('#report_value').val();
    var params = {
        url: '/api/report/getData',
        data: {
            page: pageIndex,
            size: 20,
            startTime: startTime,
            endTime: endtime,
            report_value: report_value
        },
        tokenFlag: true,
        sCallback: function (res) {
            if(res.code){
                if(res.code ==1){
                    window.location.href = 'login.html';
                }
            }else{
                var str = getOrderHtmlStr(res);
                $('#order-table').append(str);
            }
        }
    };
    window.base.getData(params);
}
/*控制加载更多按钮的显示*/
function ctrlLoadMoreBtn() {
    if (moreDataFlag) {
        $('.load-more').hide().next().show();
    }
}
/*拼接html字符串*/
function getOrderHtmlStr(res) {
    var data = res.data;
    if (data) {
        var len = data.length,
            str = '', item;
        if (len > 0) {
            for (var i = 0; i < len; i++) {
                item = data[i];
                if (item.picpath) {
                    str += '<tr>' +
                        '<td>' + item.name + '</td>' +
                        '<td>' + item.tel + '</td>' +
                        '<td style="word-wrap:break-word;">' + item.address + '</td>' +
                        '<td style="word-wrap:break-word;">' + item.detail + '</td>' +
                        '<td style="word-wrap:break-word;"><a href="' + item.picpath + '" class="swipebox" title="图"><img src="' + item.picpath + '" style="width:100%;alt="image"></a></td>' +
                        '<td style="word-wrap:break-word;">' + item.createTime + '</td>' +
                        '</tr>';
                } else {
                    str += '<tr>' +
                        '<td>' + item.name + '</td>' +
                        '<td>' + item.tel + '</td>' +
                        '<td style="word-wrap:break-word;">' + item.address + '</td>' +
                        '<td style="word-wrap:break-word;">' + item.detail + '</td>' +
                        '<td style="word-wrap:break-word;"></td>' +
                        '<td style="word-wrap:break-word;">' + item.createTime + '</td>' +
                        '</tr>';
                }
            }
        }
        else {
            ctrlLoadMoreBtn();
            moreDataFlag = false;
        }
        return str;
    }
    return '';
}

/*根据订单状态获得标志*/
function getOrderStatus(status) {
    var arr = [{
        cName: 'unpay',
        txt: '未付款'
    }, {
        cName: 'payed',
        txt: '已付款'
    }, {
        cName: 'done',
        txt: '已发货'
    }, {
        cName: 'unstock',
        txt: '缺货'
    }];
    return '<span class="order-status-txt ' + arr[status - 1].cName + '">' + arr[status - 1].txt + '</span>';
}

/*根据订单状态获得 操作按钮*/
function getBtns(status) {
    var arr = [{
        cName: 'done',
        txt: '发货'
    }, {
        cName: 'unstock',
        txt: '缺货'
    }];
    if (status == 2 || status == 4) {
        var index = 0;
        if (status == 4) {
            index = 1;
        }
        return '<span class="order-btn ' + arr[index].cName + '">' + arr[index].txt + '</span>';
    } else {
        return '';
    }
}
function loadMore() {
    console.log(pageIndex);
    if (moreDataFlag) {
        pageIndex++;
        getOrders(pageIndex);
    }
}
$(function () {

    if (!window.base.getLocalStorage('token')) {
        // window.location.href = 'login.html';
    }




    /**
     * 点击查询
     */
    $(document).on('click', '#groub_search_12', function () {
        moreDataFlag=true;
        pageIndex = 1;
        $('.load-more').show();
        $('.no-more').hide();
        $("#order-table").empty();
        /*加载更多*/
        getOrders(1);
    });

    /*加载更多*/
    $(document).on('click', '.load-more', function () {

    });
    /*发货*/
    $(document).on('click', '.order-btn.done', function () {
        var $this = $(this),
            $td = $this.closest('td'),
            $tr = $this.closest('tr'),
            id = $td.attr('data-id'),
            $tips = $('.global-tips'),
            $p = $tips.find('p');
        var params = {
            url: 'order/delivery',
            type: 'put',
            data: { id: id },
            tokenFlag: true,
            sCallback: function (res) {
                if (res.code.toString().indexOf('2') == 0) {
                    $tr.find('.order-status-txt')
                        .removeClass('pay').addClass('done')
                        .text('已发货');
                    $this.remove();
                    $p.text('操作成功');
                } else {
                    $p.text('操作失败');
                }
                $tips.show().delay(1500).hide(0);
            },
            eCallback: function () {
                $p.text('操作失败');
                $tips.show().delay(1500).hide(0);
            }
        };
        window.base.getData(params);
    });

    /*退出*/
    $(document).on('click', '#login-out', function () {
        // window.base.deleteLocalStorage('token');
        var params = {
            url: '/api/report/exit',
            data: {},
            tokenFlag: true,
            sCallback: function (res) {
                if(res.code == 0){
                    window.location.href = 'login.html';
                }
            }
        };
        window.base.getData(params);
    });
    /* Basic Gallery */
    $('.swipebox').swipebox();

    /* Video */
    $('.swipebox-video').swipebox();
    getOrders(pageIndex);
});