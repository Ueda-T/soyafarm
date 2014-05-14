<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_header.tpl" subtitle="新しいお届け先の追加・変更"}-->
<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
var get_zip_addr = "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->";
</script>
<script src="<!--{$TPL_URLPATH}-->js/pschecker.js" type="text/javascript"></script>
<script src="<!--{$TPL_URLPATH}-->js/hanzenkaku.min.js" type="text/javascript"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.autoKana.js" type="text/javascript"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/mypage_basic.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
	/*
	 * 全半角を考慮し文字列のバイト数を求める
	 */
    function strlen(s) {
        length = 0;
        s = escape(s);
        for (i = 0; i < s.length; ++i, ++length) {
            if (s.charAt(i) == "%") {
                if (s.charAt(++i) == "u") {
                   i += 3;
                   ++length;
                }
                ++i;
            }
        }
        return length;
    }

    function setlimit() {
        // 選択された都道府県の文字列長を調べる
        var len = strlen($('#pref option:selected').text());
        // ガイダンス文字が選択された場合
        if (8 < len) {
          len = 0;
        }
        $("#addr1").attr("maxlength", 40 - len);
    }
    $('#pref').change(function() {
        setlimit();
        $('#addr1').keyup();
    });

	/*
	 * 住所簡易入力
	 */
    var _lookup = function() {
        var zip = $("#zip").val();
        if (zip) {
        $.ajax({
            url: "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->",
            data: {zip:zip},
            success:function(result) {
                addrs = result.split("||");
                length = addrs.length;
                $("#addr1").remove();
                
                if (length > 1) {
                    $("#addr1-div").html('<select name="addr01" id="addr01">');
                    
                    for (var i = 0; i < length; ++i) {
                    e = addrs[i].split("|");
                    if (e.length < 3) {
                       continue;
                    }
                    $("#pref").val(e[0]);
                    s = e[1] + e[2];
                    $("#addr01").append('<option label="' + s + '" value="' + s + '">' + s + '</option>');
                    }
                } else {
                    $("#addr1-div").html('<input type="text" name="addr01" id="addr01" class="box300" value=""; ime-mode: active;>');
                    e = addrs[0].split("|");
                    if (e.length < 3) {
                        return;
                    }
                    $("#pref").val(e[0]);
                    $("#addr01").val(e[1] + e[2]);
                    $("#addr01").removeClass("valNone").addClass("selected");
                    // 住所１の入力文字数を制限
                    setlimit();
                }
            },
            error: function(data) {
                alert("error.");
            },
        });
        }
    };
    $('#zip').on("blur", _lookup);
    $('#easy').on("click", _lookup);

  /*
   * 番地なし
   */
    var _houseno = function(e) {
        var s = "";
        if ($(this).prop('checked')) {
            s = "番地なし";
        }
        $("#" + e.data.relative).val(s);
    };
    $('#house_no').on("change", {relative: "addr2"}, _houseno);

	/*
	 * 住所欄の文字数制限警告表示
	 */
    var _strlimit = function(e) {
	      if ($(this).attr("maxlength") < strlen($(this).val())) {
            $("#" + e.data.relative).css('display', 'inline');
        } else {
            $("#" + e.data.relative).css('display', 'none');
        }
    }
    $('#addr1').on("keyup", {relative: "addr1-navi"}, _strlimit);
    $('#addr2').on("keyup", {relative: "addr2-navi"}, _strlimit);
    $("#addr1-navi").css('display', 'none');
    $("#addr2-navi").css('display', 'none');
    $('#addr1').keyup();
    $('#addr2').keyup();

	/*
	 * フリガナ自動入力の設定
	 */
        $.fn.autoKana('#userName', '#userFurigana', {katakana:true});

	/*
	 * 自動フィールド移動の設定
	 */
        $('#zip').autotab_magic().autotab_filter({format: 'custom', pattern: '[^0-9-]'});
        $('#tel').autotab_magic().autotab_filter({format: 'custom', pattern: '[^0-9-]'});


  });
</script>
<section id="windowcolumn">
   <!--{*    <h2 class="title"><!--{$tpl_title|h}--></h2>*}-->
  
       <!--★インフォメーション★-->
       <div class="information">
          <p><span class="attention">※</span>は必須入力項目です。<br />
           最大20件まで登録できます。</p> 
       </div>

       <form name="form1" id="form1" method="post" action="<!--{$smarty.const.HTTPS_URL}-->mypage/delivery_addr.php">
           <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
           <input type="hidden" name="mode" value="edit" />
           <input type="hidden" name="other_deliv_id" value="<!--{$smarty.session.other_deliv_id}-->" />
           <input type="hidden" name="ParentPage" value="<!--{$ParentPage}-->" />

       <dl class="form_entry">
           <!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`frontparts/form_personal_input.tpl" flgFields=1 emailMobile=false prefix=""}-->
       </dl>

       <div class="btn_area">
          <input class="btn" type="submit" value="登録する" name="register" id="register" />
       </div>
       </form>
</section>

<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->
