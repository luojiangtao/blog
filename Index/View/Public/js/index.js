// 第一页用PHP获取数据用于搜索引擎抓取，所以这里从第二页开始
var page = 2;
var categoryId = 0;
var articleListData = {
    newTodo: '',
    todos: []
};
var articleListVue = new Vue({
    el: '#article-list'
});
articleListVue.$data = articleListData;
$(document).ready(function() {
    // 获取1-8号位广告图并显示到界面
    for (var i = 1; i <= 10; i++) {
        ajaxGetAdvertisementByGroupId(i);
    }
    // 点击左侧栏目，刷新文章列表
    $("#category-list").on("click", "a", function() {
        $(".phpGetList").hide();
        $("a").removeClass("active");
        $(this).addClass("active");
        showListLoading();
        categoryId = $(this).attr("categoryId");
        page = 1;
        while (articleListVue.todos.length != 0) {
            articleListVue.todos.shift();
        }
        ajaxGetArticleList();
    });

    // 加载更多文章
    $('#load-more-article-list').on('click', function() {
        var $btn = $(this).button('loading');
        ajaxGetArticleList();
            // business logic...
        // $btn.button('reset');
    });
});

/**
 * 获取文章列表页数据
 * @Author   罗江涛
 * @DateTime 2016-03-24T16:10:45+0800
 */
function ajaxGetArticleList() {
    $.ajax({
        type: "post",
        url: ApiUrl+"Article/getIndexArticleList",
        dataType: "json",
        data: {
            'token': token,
            'page': page,
            'categoryId': categoryId,
        },
        success: function(result) {
            if (result.status == 1) {
                var list = result.data.information;
                for (var i = 0; i < list.length; i++) {
                    // 组装数据
                    if(list[i].categoryId != 2992){
                        articleListVue.todos.push({
                            page: page,
                            categoryName: list[i].categoryName,
                            title: list[i].title,
                            creatorName: list[i].creatorName,
                            summary: list[i].summary,
                            publishTime: list[i].publishTime,
                            logo: list[i].logo,
                            searchAuthorUrl: index+"searchArticle/"+list[i].creatorName+"/searchtype/author",
                            articleDetailUrl: index+list[i].articleId+".html"
                        });
                    }
                    
                }

                if (list.length < pageSize) {
                    showNoMoreData();
                }

                page++;
                $('#load-more-article-list').button('reset');
            } else {
                // 没有结果
            console.log('get data false');
            }
        },
        error: function(e) {
            // 获取失败
            console.log('get data false');
        }
    });
}