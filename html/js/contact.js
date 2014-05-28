(function ($) {
    var _changeSubject = function(e) {
        // 定期購入について意外が選択された場合は何もしない
        if (this.value != 0) {
            return;
        }
        content = $("#" + e.data.relative).val();
        if (content.length == 0) {
            var template = "\
■ご希望のお届け間隔に「●」を付けて下さい。\n\
　【　】1ヶ月 【　】2ヶ月 【　】3ヶ月\n\
\n\
■ご希望のお届け日に「●」を付けて下さい。\n\
　【　】1日頃 【　】5日頃 【　】10日頃\n\
　【　】15日頃【　】20日頃【　】25日頃\n\
\n\
■変更する対象に「●」を付けて下さい。\n\
　【　】次回お届け時のみ 【　】次回以降全て\n\
\n\
■その他ご希望事項がある場合は以下にご記入下さい\n\
\n\
";
            $("#" + e.data.relative).val(template);
        }
    };

    $('#subject').on("change", {relative: 'contents'}, _changeSubject);
})(jQuery);
