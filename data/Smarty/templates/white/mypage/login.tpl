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
<!--▼CONTENTS-->
<div class="wrapLogin02">
    <div id="undercolumn_login">
        <form name="login_mypage" id="login_mypage" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_mypage')">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="login" />
        <input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
			<table cellspacing="0">
				<tr>
					<td class="dyn1">
						<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/head3.gif" width="390" height="75" alt="ログインまたはお客様情報の確認"></h2>
						<div>
							<table cellspacing="0" class="innr">
								<tr>
									<!--{assign var=key value="login_email"}-->
									<span class="attention"><!--{$arrErr[$key]}--></span>
									<th><img src="<!--{$TPL_URLPATH}-->img/rohto/id.gif" width="85" height="25" alt="メールアドレス"></th>
									<td>
										<input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" />
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<p class="login_memory">
											<!--{assign var=key value="login_memory"}-->
<!--{*
											<input type="checkbox" name="<!--{$key}-->" value="1"<!--{$tpl_login_memory|sfGetChecked:1}--> id="login_memory" style="width:auto;" /><label for="login_memory" style="font-size:0.75em;">メールアドレスをコンピューターに記憶させる</label>
*}-->
										</p>
									</td>
								</tr>
								<tr>
									<!--{assign var=key value="login_pass"}-->
									<span class="attention"><!--{$arrErr[$key]}--></span>
									<th><img src="<!--{$TPL_URLPATH}-->img/rohto/pw.gif" width="85" height="25" alt="パスワード"></th>
									<td>
										<input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box300" />
									</td>
								</tr>
							</table>
							<input type="image" src="<!--{$TPL_URLPATH}-->img/rohto/login.gif" alt="ログイン" name="log" id="log" class="swp" />
							<p class="naked">
								<a class="icon1" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','460'); return false;" target="_blank">パスワードを忘れた方はこちら</a><br />
								※メールアドレスを忘れた方は、お手数ですが、<a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/<!--{$smarty.const.DIR_INDEX_PATH}-->">お問い合わせページ</a>からお問い合わせください。
							</p>
						</div>
					</td>
				</tr>
			</table>







<!--{*
        <div class="login_area">
            <h3>まだ会員登録されていないお客様</h3>
            <p class="inputtext">会員登録をすると便利なMyページをご利用いただけます。<br />
                また、ログインするだけで、毎回お名前や住所などを入力することなくスムーズにお買い物をお楽しみいただけます。
            </p>
            <div class="inputbox">
                <div class="btn_area">
                    <ul>
                        <li>
                            <a href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_entry_on.jpg','b_gotoentry');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_entry.jpg','b_gotoentry');">
                            <img src="<!--{$TPL_URLPATH}-->img/button/btn_entry.jpg" alt="会員登録をする" border="0" name="b_gotoentry" /></a>
                        </li>
                    </ul>
                </div>
            </div>
            </form>
        </div>
*}-->



    </div>
</div>







<div class="wrapPW20140317" style="width:700px; margin:0 auto;">           
<h2><img src="../image/pw20140317/info_title.gif" width="700" height="130" alt="ロート通販オンラインショップにご登録済みで、2014年3月17日（月）以降に初めてご利用のお客さまへ"/></h2>
<div style="border:3px solid #a10000; border-top:none; margin:0 0 30px 0;">
<p class="naked" style="margin:0; padding:30px 30px 0 30px;">2014年3月17日（月）に、ロート通販オンラインショップのシステムはリニューアルいたしました。リニューアルに伴いまして、お客さまには大変お手数をおかけいたしますが、パスワードの再発行をお願いいたします。<br />
パスワードの再発行は、青色の「パスワードを再発行する」ボタンを押すと手続きを開始していただけます。</p>
<p class="naked" style="color:#c65a5a; padding:0 30px;">●パスワードを再発行済のお客さまは、オレンジ色のログインボタンからログインしてください。<br />
●ロート通販オンラインショップでのお買い物が初めてのお客さまは「<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/welcome.php">はじめての方へ</a>」にお進みください。</p>
<p style="text-align:center; margin:0 auto; padding:10px 0 30px 0;"><a href="<!--{$smarty.const.ROOT_URLPATH}-->renewal/"><img src="../image/pw20140317/btn_pw.gif" width="310" height="45" alt="パスワードを再発行する" class="swp"/></a></p>
</div>
<h3 style="font-size:1.2em; font-weight:bold; border-bottom:1px solid #2490d7; padding:14px 0;">パスワード再発行の流れ<span style="font-size:80%;">～パスワードの再発行がお済みでないお客さまへ～</span></h3>
<p class="naked">パスワードの再発行がお済みでないお客さまは、恐れ入りますが、以下の手順に沿って再発行の手続きをおこなってください。</p>
<div style="padding:30px 0 50px 0;">
<table cellspacing="0">
<tr>
<td style="padding:5px 10px 5px 0; vertical-align:middle;"><img src="../image/pw20140317/step1.gif" width="30" height="36" alt="1"/></td>
<td style="padding:10px; border-left:1px solid #248fd6;"><p class="naked" style="margin:0;">青色の「パスワードを再発行する」ボタンをクリックして、再発行の画面に進んでください。</p></td>
</tr>
</table>
</div>
<div style="padding:50px 0; background:url(../image/pw20140317/arrow_next.gif) no-repeat 50% 0;">
<table cellspacing="0">
<tr>
<td style="padding:5px 10px 5px 0; vertical-align:middle;"><img src="../image/pw20140317/step2.gif" width="30" height="36" alt="2"/></td>
<td style="padding:10px; border-left:1px solid #248fd6;"><p class="naked" style="margin:0;">ご登録済のメールアドレスおよびご登録済のお名前（漢字氏名）を入力して、「次へ」をクリックしてください。</p></td>
</tr>
</table>
<div style="padding:20px 0 0 40px;"><img src="../image/pw20140317/step2_gamen.gif" width="620" height="145" alt=""/></div>
</div>
<div style="padding:50px 0; background:url(../image/pw20140317/arrow_next.gif) no-repeat 50% 0;">
<table cellspacing="0">
<tr>
<td style="padding:5px 10px 5px 0; vertical-align:middle;"><img src="../image/pw20140317/step3.gif" width="30" height="36" alt="3"/></td>
<td style="padding:10px; border-left:1px solid #248fd6;"><p class="naked" style="margin:0;">「パスワードの再発行が完了しました」というメッセージが画面に表示されるとともに、パスワード変更通知のメール（件名：【ロート製薬】 パスワードを変更いたしました。）をお届けします。その中に初回ログイン用のパスワードが書かれているのをご確認ください。万が一メールが届かない場合は<a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/">こちらへお問い合わせ</a>ください。</p></td>
</tr>
</table>
<div style="padding:20px 0 0 40px;"><img src="../image/pw20140317/step3_gamen.gif" width="620" height="165" alt=""/></div>
</div>
<div style="padding:50px 0; background:url(../image/pw20140317/arrow_next.gif) no-repeat 50% 0;">
<table cellspacing="0">
<tr>
<td style="padding:5px 10px 5px 0; vertical-align:middle;"><img src="../image/pw20140317/step4.gif" width="30" height="36" alt="4"/></td>
<td style="padding:10px; border-left:1px solid #248fd6;"><p class="naked" style="margin:0;">パスワード変更通知のメール（件名：【ロート製薬】 パスワードを変更いたしました。）内に書かれていたパスワードを用いて、マイページにログインしてください。</p></td>
</tr>
</table>
<div style="padding:20px 0 0 40px;"><img src="../image/pw20140317/step4_gamen.gif" width="620" height="150" alt=""/></div>
</div>
<div style="padding:50px 0; background:url(../image/pw20140317/arrow_next.gif) no-repeat 50% 0;">
<table cellspacing="0">
<tr>
<td style="padding:5px 10px 5px 0; vertical-align:middle;"><img src="../image/pw20140317/step5.gif" width="30" height="36" alt="5"/></td>
<td style="padding:10px; border-left:1px solid #248fd6;"><p class="naked" style="margin:0;">マイページにログイン後、「メールアドレスとパスワードの変更」メニューより、パスワードの変更をおこなってください。</p>
<p class="naked" style="color:#d80e16; margin:0;">【重要】 安全のため、届いたパスワードのままにはせず、必ずご自身で変更してください。</p></td>
</tr>
</table>
<div style="padding:20px 0 0 40px;"><img src="../image/pw20140317/step5_gamen.gif" width="620" height="145" alt=""/></div>
</div>
<h3 style="font-size:1.2em; font-weight:bold; border-bottom:1px solid #2490d7; padding:14px 0;">安全にご利用いただくためのお願い<span style="font-size:80%;">～パスワードの再発行がお済みのお客さまへ～</span></h3>
<p class="naked">安全のため、パスワード変更通知のメール（件名：【ロート製薬】 パスワードを変更いたしました。）で届いたパスワードのままにせず、お客さまご自身でパスワードを改めて設定いただきますようお願い申し上げます。パスワードの変更は、マイページ内の「メールアドレスとパスワードの変更」画面にておこなっていただけます（上記「STEP 5」）。</p>
<p style="margin:50px 0 30px 0;"><img src="../image/pw20140317/thanks.gif" width="700" height="140" alt="以上で、パスワードの再設定は完了です。ご協力ありがとうございました。引き続きロート通販オンラインショップで、お買い物をお楽しみください。"/></p>
</div><!--/wrapPW20140317-->


















<!--▲CONTENTS-->
