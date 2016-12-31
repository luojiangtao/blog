var page = 10;
var no_more_data = false;
var article_list_data = {
    newTodo: '',
    todos: []
};
var article_list_vue = new Vue({
    el: '#aritcle_list'
});
article_list_vue.$data = article_list_data;
/*加载完成后获取文章列表数据*/
$(document).ready(function() {
    $('.load_more').click(function() {
        if (!no_more_data) {
            ajax_get_article_list();
        }
    });
});

function ajax_get_article_list() {
    // 获取文章列表
    $.ajax({
        type: "post",
        url: ajax_get_article_list_url,
        dataType: "json",
        data: {
            'page': page,
            'category_id': category_id,
            'keyword': keyword
        },
        beforeSend: function(result) {
            $('.ias_loader').show();
            $('.ias_trigger').hide();
        },
        success: function(result) {
            if (result.status == 1) {
                var list = result.data;
                console.log(list);
                for (var i = 0; i < list.length; i++) {
                    /*组装数据*/
                    article_detail_url = root_path + "/index.php/" + list[i].article_id;
                    article_list_vue.todos.push({
                        category_name: list[i].category_name,
                        title: list[i].title,
                        summary: list[i].summary,
                        time: list[i].time,
                        publishTime: list[i].publishTime,
                        image: list[i].image,
                        click_number: list[i].click_number,
                        comment_number: list[i].comment_number,
                        article_detail_url: article_detail_url
                    });
                }
                page += 10;
            } else {
                /*没有结果*/
                $('.load_more a').text('没有啦');
                no_more_data = true;
            }
        },
        error: function(e) {
            /*获取失败*/
            console.log(e);
            $('.load_more a').text('没有啦');
        },
        complete: function(e) {
            $('.ias_loader').hide();
            $('.ias_trigger').show();
        }
    });
}