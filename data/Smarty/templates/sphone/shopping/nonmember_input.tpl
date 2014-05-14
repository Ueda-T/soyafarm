<!--{*
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
 *}-->
 
<script type="text/javascript">//<![CDATA[
$(function(){
    //お届け先エリアを非表示にする（初期値）
    if ('1' != '<!--{$arrForm.deliv_check.value}-->') {
        $("#add_deliv_area").hide();
    }
});
//お届け先エリアの表示/非表示
var speed = 1000; //表示アニメのスピード（ミリ秒）
var stateDeliv = 1;
function fnDelivToggle(areaEl) {
    areaEl.toggle(speed);
    if (stateDeliv == 0) {
        stateDeliv = 1;
    } else {
        stateDeliv = 0
    }
}
//]]>
</script>

<!--▼CONTENTS-->
<section id="undercolumn">
     <h2 class="title"><!--{$tpl_title|h}--></h2>
         <div class="information end">
                  <span class="attention">※</span>は必須入力項目です。
               </div>

      <form name="form1" id="form1" method="post" action="?">
          <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
          <input type="hidden" name="mode" value="nonmember_confirm" />
          <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

   <dl class="form_entry">
       <dt>お名前&nbsp;<span class="attention">※</span></dt>
         <dd>
                 <!--{assign var=key1 value="order_name"}-->
           <span class="attention"><!--{$arrErr[$key1]}--></span>
             <input type="text" name="<!--{$key1}-->" 
                value="<!--{$arrForm[$key1].value|h}-->" 
                maxlength="16" 
                style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" 
                class="boxLong text data-role-none" placeholder="お名前" />&nbsp;&nbsp;
<!--
                <input type="text" name="<!--{$key2}-->" 
                value="<!--{$arrForm[$key2].value|h}-->" 
                maxlength="<!--{$arrForm[$key2].length}-->" 
                style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" 
                class="boxHarf text data-role-none" placeholder="名"/>
-->
         </dd>

       <dt>お名前(フリガナ)&nbsp;<span class="attention">※</span></dt>
          <dd>
                 <!--{assign var=key1 value="order_kana"}-->
           <span class="attention"><!--{$arrErr[$key1]}--></span>
             <input type="text" name="<!--{$key1}-->" 
             value="<!--{$arrForm[$key1].value|h}-->" 
             maxlength="15" 
             style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" 
             class="boxLong text data-role-none" placeholder="お名前(フリガナ)"/>&nbsp;&nbsp;
<!--
                            <input type="text" name="<!--{$key2}-->" 
             value="<!--{$arrForm[$key2].value|h}-->" 
             maxlength="<!--{$arrForm[$key2].length}-->" 
             style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" 
             class="boxHarf text data-role-none" placeholder="メイ"/>
-->
          </dd>

        <dt>郵便番号&nbsp;<span class="attention">※</span></dt>
          <dd>
                     <!--{assign var=key1 value="order_zip01"}-->
            <!--{assign var=key2 value="order_zip02"}-->
            <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
             <p>
                  <input type="text" name="<!--{$key1}-->" 
                        value="<!--{$arrForm[$key1].value|h}-->" 
                        max="<!--{$arrForm[$key1].length}-->" 
                        style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;

                        <input type="text" name="<!--{$key2}-->" 
                        value="<!--{$arrForm[$key2].value|h}-->" 
                        max="<!--{$arrForm[$key2].length}-->" 
                        style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;
                        <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fn">郵便番号検索</span></a></p>

               <a href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01');" class="btn_sub btn_inputzip">郵便番号から住所自動入力</a>
          </dd>

        <dt>住所&nbsp;<span class="attention">※</span></dt>
          <dd>
                     <!--{assign var=key value="order_pref"}-->
            <span class="attention"><!--{$arrErr.order_pref}--><!--{$arrErr.order_addr01}--><!--{$arrErr.order_addr02}--></span>
              <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="boxHarf top data-role-none">
                  <option value="" selected="selected">都道府県</option>
                                       <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                  </select>
                  <!--{assign var=key value="order_addr01"}-->
                  <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" class="boxLong text top data-role-none" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" placeholder="市区町村名" />
                              <!--{assign var=key value="order_addr02"}-->
                              <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" class="boxLong text data-role-none" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" placeholder="番地・ビル名" />
          </dd>
  
        <dt>電話番号&nbsp;<span class="attention">※</span></dt>
          <dd>
            <!--{assign var=key1 value="order_tel"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
             <input type="tel" name="<!--{$key1}-->" 
               value="<!--{$arrForm[$key1].value|h}-->" 
                  maxlength="<!--{$arrForm[$key1].length*3}-->" 
                   style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" 
                    class="boxLong text data-role-none" />
<!-- 
             <input type="tel" name="<!--{$key2}-->" 
               value="<!--{$arrForm[$key2].value|h}-->" 
                maxlength="<!--{$arrForm[$key2].length}-->" 
                 style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" 
                   class="boxShort text data-role-none" />&nbsp;－&nbsp;

             <input type="tel" name="<!--{$key3}-->" 
               value="<!--{$arrForm[$key3].value|h}-->" 
                 maxlength="<!--{$arrForm[$key3].length}-->" 
                  style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" 
                  class="boxShort text data-role-none" />
-->
          </dd>
 
        <dt>FAX</dt>
          <dd>
            <!--{assign var=key1 value="order_fax"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
             <input type="tel" name="<!--{$key1}-->" 
               value="<!--{$arrForm[$key1].value|h}-->" 
                maxlength="<!--{$arrForm[$key1].length*3}-->" 
                 style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" 
                   class="boxLong text data-role-none" />
<!-- 
              <input type="tel" name="<!--{$key2}-->" 
                 value="<!--{$arrForm[$key2].value|h}-->" 
                   maxlength="<!--{$arrForm[$key2].length}-->" 
                     style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" 
                       class="boxShort text data-role-none" />&nbsp;－&nbsp;

                  <input type="tel" name="<!--{$key3}-->" 
                      value="<!--{$arrForm[$key3].value|h}-->" 
                         maxlength="<!--{$arrForm[$key3].length}-->" 
                           style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" 
                              class="boxShort text data-role-none" />
-->
          </dd>

        <dt>メールアドレス&nbsp;<span class="attention">※</span></dt>
           <dd>
                      <!--{assign var=key value="order_email"}-->
               <span class="attention"><!--{$arrErr[$key]}--></span>
             <input type="email" name="<!--{$key}-->" 
                value="<!--{$arrForm[$key].value|h}-->" 
                style="<!--{$arrErr[$key]|sfGetErrorColor}-->" 
                maxlength="<!--{$arrForm[$key].length}-->" class="boxLong text top data-role-none" /></br>
             <!--{assign var=key value="order_email02"}-->
              <span class="attention"><!--{$arrErr[$key]}--></span>
                                  <input type="email" name="<!--{$key}-->" 
                value="<!--{$arrForm[$key].value|h}-->" 
                style="<!--{$arrErr[$key]|sfGetErrorColor}-->" 
                maxlength="<!--{$arrForm[$key].length}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
          </dd>

        <dt>性別&nbsp;</dt>
          <dd>
                  <!--{assign var=key value="order_sex"}-->
             <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{if $arrErr[$key]}-->
              <!--{assign var=err value="background-color: `$smarty.const.ERR_COLOR`"}-->
            <!--{/if}-->
            <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
      <p><input type="radio" id="man" name="<!--{$key}-->" value="1" <!--{if $arrForm[$key].value eq 1}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="man">男性</label>&nbsp;&nbsp;
    <input type="radio" id="woman" name="<!--{$key}-->" value="2" <!--{if $arrForm[$key].value eq 2}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="woman">女性</label></p>
     </dd>
            </span>
           
   </dd>
    
        <dt>生年月日</dt>
         <dd>
                 <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
            <div class="attention"><!--{$errBirth}--></div>
              <select name="year" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
                            <!--{html_options options=$arrYear selected=$arrForm.year.value|default:''}-->
              </select><span class="selectdate">年</span>

              <select name="month" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
                <!--{html_options options=$arrMonth selected=$arrForm.month.value|default:''}-->
              </select><span class="selectdate">月</span>

              <select name="day" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
                <!--{html_options options=$arrDay selected=$arrForm.day.value|default:''}-->
              </select><span class="selectdate">日</span>
        </dd>


                <dt>メールマガジン送付について&nbsp;<span class="attention">※</span></dt>
                <dd>
                    <!--{assign var=key value="mail_flag"}-->
                    <!--{if $arrErr[$key]}-->
                        <div class="attention"><!--{$arrErr[$key]}--></div>
                    <!--{/if}-->
                    <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <input type="radio" name="<!--{$key}-->" value="1" id="yes" class="data-role-none" <!--{if $arrForm[$key].value eq '1'}--> checked="checked" <!--{/if}--> /><label for="yes">受け取る</label>
                        <input type="radio" name="<!--{$key}-->" value="0" id="no" class="data-role-none" <!--{if $arrForm[$key].value eq '0'}--> checked="checked" <!--{/if}--> /><label for="no">受け取らない</label>
                    </span>
                </dd>
                <dt>ＤＭ送付について&nbsp;<span class="attention">※</span></dt>
                <dd>
                    <!--{assign var=key value="dm_flg"}-->
                    <!--{if $arrErr[$key]}-->
                        <div class="attention"><!--{$arrErr[$key]}--></div>
                    <!--{/if}-->
                    <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <input type="radio" name="<!--{$key}-->" value="1" id="on" class="data-role-none" <!--{if $arrForm[$key].value eq '1'}--> checked="checked" <!--{/if}--> /><label for="on">受け取る</label>
                        <input type="radio" name="<!--{$key}-->" value="0" id="off" class="data-role-none" <!--{if $arrForm[$key].value eq '0'}--> checked="checked" <!--{/if}--> /><label for="off">受け取らない</label><br />
                    </span>
                </dd>
                <dt>アンケートについて&nbsp;<span class="attention">※</span></dt>
                <dd>
                    <!--{assign var=key value="questionnaire"}-->
                    当サイトをどこで知りましたか？
                    <br />
                    <!--{if $arrErr[$key]}-->
                        <div class="attention"><!--{$arrErr[$key]}--></div>
                    <!--{/if}-->
                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onChange="fnChangeQuestionnaire(); return false;">
                        <option value="" selected="selected">選択してください</option>
                        <!--{html_options options=$arrQuestionnaire selected=$arrForm[$key].value}-->
                    </select>
                    <br />
                    <div id="questionnaire_other_text"></div>
                    <!--{assign var=key value="questionnaire_other"}-->
                    <!--{if $arrErr[$key]}-->
                        <div class="attention"><!--{$arrErr[$key]}--></div>
                    <!--{/if}-->
                    <textarea name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="70" rows="8" class="txtarea" wrap="soft"><!--{$arrForm[$key].value|h}--></textarea>
                </dd>



        <dt class="bg_head">
           <!--{assign var=key value="deliv_check"}-->
              <input class="radio_btn data-role-none" type="checkbox" name="<!--{$key}-->" value="1" onchange="fnDelivToggle($('#add_deliv_area')); fnCheckInputDeliv();" <!--{$arrForm[$key].value|sfGetChecked:1}--> id="deliv_label" />         
             <label for="deliv_label"><span class="fb">お届け先を指定</span></label>
        
                                </dt>
        <dd><br />※上記に入力された住所と同一の場合は省略可能です。</dd>
<div id="add_deliv_area" >
              <dt>お名前&nbsp;<span class="attention">※</span></dt>
          <dd>
            <!--{assign var=key1 value="shipping_name"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <input type="text" name="<!--{$key1}-->" 
                 value="<!--{$arrForm[$key1].value|h}-->" 
                   maxlength="16" 
                     style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" 
                       class="boxLong text data-role-none" placeholder="お名前" />&nbsp;&nbsp;
<!--
                    <input type="text" name="<!--{$key2}-->" 
                      value="<!--{$arrForm[$key2].value|h}-->" 
                        maxlength="<!--{$arrForm[$key2].length}-->" 
                           style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" 
                             class="boxHarf text data-role-none" placeholder="名"/>
-->
                        </dd>

       <dt>お名前(フリガナ)&nbsp;<span class="attention">※</span></dt>
         <dd>
            <!--{assign var=key1 value="shipping_kana"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <input type="text" name="<!--{$key1}-->" 
               value="<!--{$arrForm[$key1].value|h}-->" 
                 maxlength="15" 
                  style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" 
                    class="boxLong text data-role-none" placeholder="お名前(フリガナ)"/>&nbsp;&nbsp;
<!--
                  <input type="text" name="<!--{$key2}-->" 
                    value="<!--{$arrForm[$key2].value|h}-->" 
                       maxlength="<!--{$arrForm[$key2].length}-->" 
                        style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" 
                           class="boxHarf text data-role-none" placeholder="メイ"/>
-->
                         </dd>

       <dt>郵便番号&nbsp;<span class="attention">※</span></dt>
         <dd>
           <!--{assign var=key1 value="shipping_zip01"}-->
           <!--{assign var=key2 value="shipping_zip02"}-->
           <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
            <p>
                <input type="text" name="<!--{$key1}-->" 
                   value="<!--{$arrForm[$key1].value|h}-->" 
                     max="<!--{$arrForm[$key1].length}-->" 
                       style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;
                         <input type="text" name="<!--{$key2}-->" 
                             value="<!--{$arrForm[$key2].value|h}-->" 
                                max="<!--{$arrForm[$key2].length}-->" 
                                  style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;
                                      <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fn">郵便番号検索</span></a></p>

               <a href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'shipping_zip01', 'shipping_zip02', 'shipping_pref', 'shipping_addr01');" class="btn_sub btn_inputzip">郵便番号から住所自動入力</a>

          </dd>
        
          <dt>住所&nbsp;<span class="attention">※</span></dt>
          <dd>
            <!--{assign var=key value="shipping_pref"}-->
            <span class="attention"><!--{$arrErr.shipping_pref}--><!--{$arrErr.shipping_addr01}--><!--{$arrErr.shipping_addr02}--></span>
               <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="boxHarf top data-role-none">
                  <option value="" selected="selected">都道府県</option>
                                       <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
               </select>
                                                  <!--{assign var=key value="shipping_addr01"}-->
              <input type="text" name="<!--{$key}-->" 
                 value="<!--{$arrForm[$key].value|h}-->" 
                  class="boxLong top data-role-none" 
                    style="<!--{$arrErr[$key]|sfGetErrorColor}-->" 
                      placeholder="市区町村名" />

              <!--{assign var=key value="shipping_addr02"}-->
              <input type="text" name="<!--{$key}-->" 
                  value="<!--{$arrForm[$key].value|h}-->" 
                    class="boxLong data-role-none" 
                      style="<!--{$arrErr[$key]|sfGetErrorColor}-->" 
                         placeholder="番地・ビル名" />
          </dd>
        
          <dt>電話番号&nbsp;<span class="attention">※</span></dt>
          <dd>
            <!--{assign var=key1 value="shipping_tel"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <input type="tel" name="<!--{$key1}-->" 
               value="<!--{$arrForm[$key1].value|h}-->" 
                 maxlength="<!--{$arrForm[$key1].length}-->" 
                  style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" 
                    class="boxLong text data-role-none" />
<!--
                       <input type="tel" name="<!--{$key2}-->" 
                          value="<!--{$arrForm[$key2].value|h}-->" 
                            maxlength="<!--{$arrForm[$key2].length}-->" 
                               style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" 
                                 class="boxShort text data-role-none" />&nbsp;－&nbsp;

                            <input type="tel" name="<!--{$key3}-->" 
                               value="<!--{$arrForm[$key3].value|h}-->" 
                                 maxlength="<!--{$arrForm[$key3].length}-->" 
                                   style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" 
                                    class="boxShort text data-role-none" />
-->
                                     </dd>
<!--
     <dd class="pb">
       <a class="btn_more" href="javascript:fnModeSubmit('multiple', '', '');">お届け先を複数指定する</a>
       </dd>
-->

</div>
                

     <div class="btn_area">
      <p><input type="submit" value="次へ" class="btn data-role-none" alt="次へ" name="next" id="next" /></p>
     </div>
     </dl>

</form>
</section>
<!--▲コンテンツここまで -->
