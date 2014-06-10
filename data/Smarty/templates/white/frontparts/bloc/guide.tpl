<div id="mypage-box">
	<p id="bt_cart" class="tocart"><a href="<!--{$smarty.const.CART_URLPATH}-->"><img src="http://www.soyafarm.com/common/img/navi/bt_cart.gif" alt="買い物かごの中を見る" width="182" height="59" class="btn"></a></p>
	<p id="link_nagare"><a href="<!--{$smarty.const.ROOT_URLPATH}-->flow/"><img src="<!--{$TPL_URLPATH}-->img/navi/txt_nagare.gif" alt="商品ご購入の流れ" width="146" height="20" class="btn"></a></p>
	<p id="bt_mypage" style="margin-bottom:10px;padding-top:10px;"><a href="<!--{$smarty.const.HTTPS_URL}-->mypage/"><img src="http://www.soyafarm.com/common/img/navi/bt_mypage.gif" alt="【Web会員専用】マイページ" width="180" height="49" class="btn"></a></p>
<!--{if $tpl_login && !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
	<input type="hidden" name="mode" value="login" />
	<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
	<p id="link_logout"><a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_logout.gif" alt="Web会員に新規登録する" width="166" height="31" border="0" class="btn"></a></p>
	</form>
<!--{/if}-->
</div>

			<ul id="global-box">
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_top.gif" alt="TOP" width="204" height="59" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->concept/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_concept.gif" alt="ソヤファームクラブとは？" width="204" height="59" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_syohin.gif" alt="商品ラインナップ" width="204" height="59" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->teiki/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_teiki.gif" alt="おトクな「定期購入」について" width="204" height="59" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->faq/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_faq.gif" alt="よくあるご質問" width="204" height="59" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->column/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_column.gif" alt="コラム 大豆のチカラ" width="204" height="59" class="btn"></a></li>
				<li><a href="http://www.fujioil.co.jp/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_company.gif" alt="会社概要" width="204" height="59" class="btn"></a></li>
			</ul>
			<ul id="sub-box">
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->links/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_links.gif" alt="リンク" width="200" height="44" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->sitemaps/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_sitemap.gif" alt="サイトマップ" width="200" height="44" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->info/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_info.gif" alt="利用規約" width="200" height="44" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->order/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_tokusyo.gif" alt="特定商取引に関する法律に基づく表示" width="200" height="63" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->guide/privacy.php"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_privacy.gif" alt="プライバシーポリシー" width="200" height="44" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->about/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_about.gif" alt="このサイトについて" width="200" height="44" class="btn"></a></li>
				<li><a href="<!--{$smarty.const.HTTPS_URL}-->contact/"><img src="<!--{$TPL_URLPATH}-->img/navi/bt_contact.gif" alt="お問い合わせ" width="200" height="44" class="btn"></a></li>
			</ul>
			<ul id="banner-box">
				<li><a href="http://www.fujioil.co.jp/"><img src="<!--{$TPL_URLPATH}-->img/navi/bnr_fujiseiyu.gif" alt="不二製油株式会社" width="200" height="50"></a></li>
				<!--■2014-0416 close
				<li><a href="http://www.daizupeptide.jp/"><img src="<!--{$TPL_URLPATH}-->img/navi/bnr_forum.gif" alt="大豆ペプチド健康フォーラム" width="200" height="50" /></a></li>
				-->
			</ul>
			<div id="verisign"><script src="https://seal.verisign.com/getseal?host_name=www.soyafarm.com&amp;size=L&amp;use_flash=YES&amp;use_transparent=YES&amp;lang=ja"></script><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" id="s_l" width="130" height="88" align=""> <param name="movie" value="https://seal.verisign.com/getseal?at=1&amp;sealid=0&amp;dn=www.soyafarm.com&amp;lang=ja"> <param name="loop" value="false"> <param name="menu" value="false"> <param name="quality" value="best"> <param name="wmode" value="transparent"> <param name="allowScriptAccess" value="always"> <embed src="https://seal.verisign.com/getseal?at=1&amp;sealid=0&amp;dn=www.soyafarm.com&amp;lang=ja" loop="false" menu="false" quality="best" wmode="transparent" swliveconnect="FALSE" width="130" height="88" name="s_l" align="" type="application/x-shockwave-flash" pluginspage="https://www.macromedia.com/go/getflashplayer" allowscriptaccess="always">  </object></div>

<!--{*
<div class="bloc_outer">
    <div id="guide_area" class="bloc_body">
        <!--{strip}-->
        <ul class="button_like">
        <li>
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->abouts/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "abouts"}--> selected<!--{/if}-->"
            >当サイトについて</a></li>
        <li>
            <a href="<!--{$smarty.const.HTTPS_URL}-->contact/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "contact"}--> selected<!--{/if}-->"
            >お問い合わせ</a></li>
        <li>
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->order/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "order"}--> selected<!--{/if}-->"
            >特定商取引に関する表記</a></li>
        <li>
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->guide/privacy.php" class="<!--{if $tpl_page_category == "order"}--> selected<!--{/if}-->"
            >プライバシーポリシー</a></li>
        <li>
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->faq/index.php" class="<!--{if $tpl_page_category == "faq"}--> selected<!--{/if}-->"
            >よくあるご質問</a></li>
        </ul>
        <!--{/strip}-->
        <div style="height: 0px; overflow: hidden;"></div>
    </div>
</div>
*}-->
