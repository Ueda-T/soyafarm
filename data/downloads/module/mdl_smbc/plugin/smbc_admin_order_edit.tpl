<!--{*
 * CategoryContents
 * Copyright (C) 2012 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *}-->

<!--▼SMBCプラグイン-->
<!--{if $tpl_smbc_sendmail}-->
    <li><a class="btn-action" href="javascript:;" onclick="return fnSendMailSmbc('send_data_smbc'); return false;"><span class="btn-next">支払方法選択依頼メールを送る</span></a></li>
    <script type="text/javascript">
    <!--
    function fnSendMailSmbc(mode) {
        
        if (!fnCheckNull('order_email')){
            alert('メールアドレスを入力してください。');
            return false;
        }
        if (!fnCheckNull('order_tel01') || !fnCheckNull('order_tel02') || !fnCheckNull('order_tel03')){
            alert('電話番号を入力してください。');
            return false;
        }

        if(!window.confirm('メールを送信してよろしいですか？')){
            return false;
        }
        document.form1['mode'].value = mode;
        document.form1.submit();
    }
    /* 必須入力チェック */
    function fnCheckNull(key) {
        if ($.trim($("*[name=" + key + "]").val()) == '') {
            return false;
        } else {
            return true;
        }
    }
    //-->
    </script>
<!--{/if}-->
<!--▲SMBCプラグイン-->
