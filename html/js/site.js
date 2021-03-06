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
// 親ウィンドウの存在確認.
function fnIsopener() {
    var ua = navigator.userAgent;
    if( !!window.opener ) {
        if( ua.indexOf('MSIE 4')!=-1 && ua.indexOf('Win')!=-1 ) {
            return !window.opener.closed;
        } else {
        	return typeof window.opener.document == 'object';
        }
	} else {
		return false;
	}
}

// 郵便番号入力呼び出し.
function fnCallAddress(php_url, tagname1, input1, input2) {
    zip = document.form1[tagname1].value;

    if(zip.length) {
        $.get(
            php_url,
            {zip: zip, input1: input1, input2: input2},
            function(data) {
                arrdata = data.split("|");
                if (arrdata.length > 1) {
                    fnPutAddress(input1, input2, arrdata[0], arrdata[1], arrdata[2]);
                } else {
                    alert(data);
                }
            }
        );
    } else {
        alert("郵便番号を正しく入力して下さい。");
	}
}

// 郵便番号から検索した住所を渡す.
function fnPutAddress(input1, input2, state, city, town) {
    if(state != "") {
        // 項目に値を入力する.
        document.form1[input1].selectedIndex = state;
        document.form1[input2].value = city + town;
    }
}

// 商品名入力呼び出し.
function fnCallProduct(php_url, tagname1, input1) {
    //search_product_name = document.form1[tagname1].value;
    search_product_name = $("#search_product_name").val();

    $.get(
        php_url,
        {search_product_name: search_product_name, input1: input1},
        function(html) {
            // 既存行を削除
            var rowCount    = product_list.rows.length;
            for (var i = 1; i < rowCount; i ++) {
                product_list.deleteRow(1);
            }

            if (html != "") {
                fnPutProduct(input1, html);
            } else {
                alert("該当する商品は見つかりませんでした");
            }
        }
    );
}

// 商品名から検索した商品情報を渡す.
function fnPutProduct(input1, html) {
    if(html != "") {
        // 項目に値を入力する.
        $("#product_list").append(html);
    }
}

function fnOpenNoMenu(URL) {
    window.open(URL,"nomenu","scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");
}

function fnOpenWindow(URL,name,width,height) {
    window.open(URL,name,"width="+width+",height="+height+",scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no");
}

function fnSetFocus(name) {
    if(document.form1[name]) {
        document.form1[name].focus();
    }
}

// セレクトボックスに項目を割り当てる.
function fnSetSelect(name1, name2, val) {
    sele1 = document.form1[name1];
    sele2 = document.form1[name2];

    if(sele1 && sele2) {
        index=sele1.selectedIndex;

        // セレクトボックスのクリア
        count=sele2.options.length
        for(i = count; i >= 0; i--) {
            sele2.options[i]=null;
        }

        // セレクトボックスに値を割り当てる。
        len = lists[index].length
        for(i = 0; i < len; i++) {
            sele2.options[i]=new Option(lists[index][i], vals[index][i]);
            if(val != "" && vals[index][i] == val) {
                sele2.options[i].selected = true;
            }
        }
    }
}

// Enterキー入力をキャンセルする。(IEに対応)
function fnCancelEnter()
{
    if (gCssUA.indexOf("WIN") != -1 && gCssUA.indexOf("MSIE") != -1) {
        if (window.event.keyCode == 13)
        {
            return false;
        }
    }
    return true;
}

// モードとキーを指定してSUBMITを行う。
function fnModeSubmit(mode, keyname, keyid) {
    switch(mode) {
    case 'delete_category':
        if(!window.confirm('選択したカテゴリとカテゴリ内のすべてのカテゴリを削除します')){
            return;
        }
        break;
    case 'delete':
        if(!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')){
            return;
        }
        break;
    case 'delete_order':
        if(!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？\n\n※ 在庫数は手動で戻してください。')){
            return;
        }
        mode = 'delete';
        break;
    case 'confirm':
        if(!window.confirm('登録しても宜しいですか')){
            return;
        }
        break;
    case 'delete_all':
        if(!window.confirm('検索結果をすべて削除しても宜しいですか')){
            return;
        }
        break;
    default:
        break;
    }
    document.form1['mode'].value = mode;
    if(keyname != "" && keyid != "") {
        document.form1[keyname].value = keyid;
    }
    document.form1.submit();
}

function fnFormModeSubmit(form, mode, keyname, keyid) {
    switch(mode) {
    case 'delete':
        if(!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')){
            return;
        }
        break;
    case 'confirm':
        if(!window.confirm('登録しても宜しいですか')){
            return;
        }
        break;
    case 'regist':
        if(!window.confirm('登録しても宜しいですか')){
            return;
        }
        break;
    default:
        break;
    }
    document.forms[form]['mode'].value = mode;
    if(keyname != "" && keyid != "") {
        document.forms[form][keyname].value = keyid;
    }
    document.forms[form].submit();
}

function fnSetFormSubmit(form, key, val) {
    document.forms[form][key].value = val;
    document.forms[form].submit();
    return false;
}

function fnSetVal(key, val) {
    fnSetFormVal('form1', key, val);
}

function fnSetFormVal(form, key, val) {
    document.forms[form][key].value = val;
}

function fnRemoveVal(key, delimiter, val) {
    fnRemoveFormVal('form1', key, delimiter, val);
}

function fnRemoveFormVal(form, key, delimiter, val) {
    value = document.forms[form][key].value;

    if (value.length == 0) {
        return;
    }

    array = value.split(delimiter);

    for (i = 0; i < array.length; ++i) {
        if (array[i] == val) {
            array.splice(i, 1);
			break;
        }
    }

    value = array.join(delimiter);

    document.forms[form][key].value = value;
}

function fnAppendVal(key, delimiter, val) {
    fnAppendFormVal('form1', key, delimiter, val);
}

function fnAppendFormVal(form, key, delimiter, val) {
    value = document.forms[form][key].value;

    if (value.length > 0) {
        value = value + delimiter;
    }

    document.forms[form][key].value = value + val;
}

function fnExistsVal(key, delimiter, val) {
    return fnExistsFormVal('form1', key, delimiter, val);
}

function fnExistsFormVal(form, key, delimiter, val) {
    value = document.forms[form][key].value;

    if (value.length == 0) {
        return false;
    }

    array = value.split(delimiter);

    for (i = 0; i < array.length; ++i) {
        if (array[i] == val) {
			return true;
        }
    }

    return false;
}

function fnChangeAction(url) {
    document.form1.action = url;
}

function fnFormChangeAction(form, url) {
    document.forms[form].action = url;
}

// ページナビで使用する。
function fnNaviPage(pageno) {
    document.form1['pageno'].value = pageno;
    document.form1.submit();
}

function fnSearchPageNavi(pageno) {
    document.form1['pageno'].value = pageno;
    document.form1['mode'].value = 'search';
    document.form1.submit();
    }

function fnSubmit(){
    document.form1.submit();
}

// 別のお届け先入力制限。
function fnCheckInputDeliv() {
    if(!document.form1) {
        return;
    }
    if(document.form1['deliv_check']) {
        list = new Array(
                        'shipping_name',
                        'shipping_kana',
                        'shipping_pref',
                        'shipping_zip01',
                        'shipping_zip02',
                        'shipping_addr01',
                        'shipping_addr02',
                        'shipping_tel'
                        );

        if(!document.form1['deliv_check'].checked) {
            fnChangeDisabled(list, '#dddddd');
        } else {
            fnChangeDisabled(list, '');
        }
    }
}

// 最初に設定されていた色を保存しておく。
var g_savecolor = new Array();

function fnChangeDisabled(list, color) {
    len = list.length;

    for(i = 0; i < len; i++) {
        if(document.form1[list[i]]) {
            if(color == "") {
                // 有効にする。
                document.form1[list[i]].disabled = false;
                document.form1[list[i]].style.backgroundColor = g_savecolor[list[i]];
            } else {
                // 無効にする。
                document.form1[list[i]].disabled = true;
                g_savecolor[list[i]] = document.form1[list[i]].style.backgroundColor;
                document.form1[list[i]].style.backgroundColor = color;//"#f0f0f0";
            }
        }
    }
}


// ログイン時の入力チェック
function fnCheckLogin(formname) {
    var lstitem = new Array();

    if(formname == 'login_mypage'){
    lstitem[0] = 'mypage_login_email';
    lstitem[1] = 'mypage_login_pass';
    }else{
    lstitem[0] = 'login_email';
    lstitem[1] = 'login_pass';
    }
    var max = lstitem.length;
    var errflg = false;
    var cnt = 0;

    //　必須項目のチェック
    for(cnt = 0; cnt < max; cnt++) {
        if(document.forms[formname][lstitem[cnt]].value == "") {
            errflg = true;
            break;
        }
    }

    // 必須項目が入力されていない場合
    if(errflg == true) {
        alert('メールアドレス/パスワードを入力して下さい。');
        return false;
    }
}

// 時間の計測.
function fnPassTime(){
    end_time = new Date();
    time = end_time.getTime() - start_time.getTime();
    alert((time/1000));
}
start_time = new Date();

//親ウィンドウのページを変更する.
function fnUpdateParent(url) {
    // 親ウィンドウの存在確認
    if(fnIsopener()) {
        window.opener.location.href = url;
    } else {
        window.close();
    }
}

//特定のキーをSUBMITする.
function fnKeySubmit(keyname, keyid) {
    if(keyname != "" && keyid != "") {
        document.form1[keyname].value = keyid;
    }
    document.form1.submit();
}

//文字数をカウントする。
//引数1：フォーム名称
//引数2：文字数カウント対象
//引数3：カウント結果格納対象
function fnCharCount(form,sch,cnt) {
    document.forms[form][cnt].value= document.forms[form][sch].value.length;
}


// テキストエリアのサイズを変更する.
function ChangeSize(buttonSelector, textAreaSelector, max, min) {

    if ($(textAreaSelector).attr('rows') <= min) {
        $(textAreaSelector).attr('rows', max);
        $(buttonSelector).text('縮小');
    } else {
        $(textAreaSelector).attr('rows', min);
        $(buttonSelector).text('拡大');
    }
}

// アンケート変更イベント
function fnChangeQuestionnaire() {
    if(!document.form1) {
        return;
    }

    obj1 = document.form1['questionnaire'];
    obj2 = document.form1['questionnaire_other'];
    obj3 = document.getElementById("questionnaire_other_text");
    if (obj1 == null || obj2 == null || obj3 == null) {
        return false;
    }

    if (obj1.options[obj1.selectedIndex].value < 50) {
        // 無効にする。
        obj2.disabled = true;
        
        obj3.innerText = "";
        // Firefox 対策。
        obj3.textContent = "";
        obj3.style.display="none";
        document.getElementById("one_maincolumn").style.margin = "0 auto 20px";
    } else {
        // 有効にする。
        obj2.disabled = false;
        
        obj3.innerText = "「" + obj1.options[obj1.selectedIndex].text + "」を選択された場合、何で知りましたか？";
        // Firefox 対策。
        obj3.textContent = "「" + obj1.options[obj1.selectedIndex].text + "」を選択された場合、何で知りましたか？";
        obj3.style.display="block";
    }
}
