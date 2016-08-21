var title = "";
// 加载完成后获取文章详情页数据
$(document).ready(function() {
    warpImageUserA();
    // 获取9号位，和10号位的广告
    ajaxGetAdvertisementByGroupId(14);
    ajaxGetAdvertisementByGroupId(15);
    //收藏文章
    $('#operation').on('click', '.add-favorite', function() {
        checkIsLogin();
        var thisButton = $(this);
        $.ajax({
            type: "post",
            url: ApiUrl+"User/addFavorite",
            dataType: "json",
            data: {
                'token': token,
                'articleId': articleId
            },
            success: function(result) {
                console.log(result);
                if (result.status == 1) {
                    thisButton.removeClass('add-favorite');
                    thisButton.addClass('delete-favorite');
                    var favoriteNumber = parseInt($('#article-favorite-number').html()) + 1;
                    $('#article-favorite-number').text(favoriteNumber);
                } else {
                    /*没有结果*/
                    alert(result.message);
                }
            },
            error: function(e) {
                /*获取失败*/
            }
        });
    });
    //取消收藏文章
    $('#operation').on('click', '.delete-favorite', function() {
        checkIsLogin();
        var thisButton = $(this);
        $.ajax({
            type: "post",
            url: ApiUrl+"User/deleteFavorite",
            dataType: "json",
            data: {
                'token': token,
                'articleId': articleId
            },
            success: function(result) {
                console.log(result);
                if (result.status == 1) {
                    thisButton.addClass('add-favorite');
                    thisButton.removeClass('delete-favorite');
                    var favoriteNumber = parseInt($('#article-favorite-number').html()) - 1;
                    $('#article-favorite-number').text(favoriteNumber);
                } else {
                    /*没有结果*/
                    alert(result.message);
                }
            },
            error: function(e) {
                /*获取失败*/
            }
        });
    });
    //点赞
    $('#operation').on('click', '.add-praise', function() {
        var thisButton = $(this);
        $.ajax({
            type: "post",
            url: ApiUrl+"Article/addPraise",
            dataType: "json",
            data: {
                'token': token,
                'articleId': articleId
            },
            success: function(result) {
                console.log(result);
                if (result.status == 1) {
                    thisButton.removeClass('add-praise');
                    thisButton.addClass('delete-praise');
                    var praiseNumber = parseInt($('#article-praise-number').html()) + 1;
                    $('#article-praise-number').text(praiseNumber);
                } else {
                    /*没有结果*/
                    alert(result.message);
                }
            },
            error: function(e) {
                /*获取失败*/
            }
        });
    });

    // 图片幻灯片
    $('.boxer').boxer({
        labels: {
            close: "关闭",
            count: "/",
            next: "下一个",
            previous: "上一个"
        }
    });
});

/**
 * 用A元素包裹img元素，用于播放幻灯片
 * @Author   罗江涛
 * @DateTime 2016-07-28T14:25:19+0800
 */
function warpImageUserA(){
    $('#article-content img').each(function(index,data){
        var src = $(data).attr("src");
        var a = '<a title="" rel="gallery" class="boxer" href="' + src + '"></a>'
        $(data).wrap(a);
    });
}

window._bd_share_config = {
    common : {
        "bdText" : title,
        "bdDesc" : '',
        "bdUrl" : '',
        "bdPic" : '',
        "bdSnsKey":{},
        "bdMini":"2",
        "bdMiniList":false,
        "bdStyle":"0",
        "bdSize":"24"
    },
    share : [{
        "bdSize" : 16
    }]

}
with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];

