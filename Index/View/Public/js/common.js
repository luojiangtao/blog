$(function() {
    //点击热门搜索
    $(".hot-search a").click(function() {
        var keyword = $(this).html();
        $("input[name='keyword']").val(keyword);
        $("#top form").submit();
    });
});