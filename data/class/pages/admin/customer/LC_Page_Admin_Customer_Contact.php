<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * お問い合わせ一覧 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Customer_Contact.php 21118 2011-08-03 12:28:10Z kajiwara $
 */
class LC_Page_Admin_Customer_Contact extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
	    parent::init();//親クラスの"init"メソッドの呼び出し
	    $this->tpl_mainpage = 'customer/contact.tpl';//表示するtplの指定
	    $this->tpl_mainno = 'customer';//メニューのカテゴリの指定
	    $this->tpl_subnavi = 'customer/subnavi.tpl';//メニューtplの指定
	    $this->tpl_subno = 'contact';//自身のページの指定
	    $this->tpl_pager = 'pager.tpl';//ページ送りのtplの指定
	    $this->tpl_subtitle = '問い合わせ管理';//ページタイトルの指定

	    $masterData = new SC_DB_MasterData_Ex();//マスターデータ管理クラスのインスタンス化
	    $this->arrPageMax = $masterData->getMasterData("mtb_page_max");//ページ送り情報をマスターデータから取得
	    $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));//都道府県データをマスターデータから取得
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
function action() {
	// 認証可否の判定
	$objSess = new SC_Session();//セッションクラスのインスタンス化
	SC_Utils_Ex::sfIsSuccess($objSess);//認証に失敗していればエラーページを表示

	// モードチェック
	if(!isset($_POST['mode'])) {//"mode"がNULLならば
		$_POST['mode'] = "";//空を入力
	} elseif($_POST['mode'] == 'delete') {//"mode"が"delete"であれば
		if(SC_Utils_Ex::sfIsInt($_POST['contact_id'])) {//"contact_id"が数字ならば
			$objQuery = new SC_Query();//データベース操作クラスをインスタンス化
			$where = "contact_id = ?";
			$sqlval['del_flg'] = '1';
			//"contact_id"を検索条件として、"del_flg"を"1"とするSQLを実行
			$objQuery->update("dtb_contact", $sqlval, $where, array($_POST['contact_id']));
		}
	}

	// 表示順の指定
	$order = "create_date DESC";
	// 読み込む列とテーブルの指定
	$col = "*";
	$from = "dtb_contact";
	$where = "del_flg = 0";
	$objQuery = new SC_Query();
	// 消去状態になっていない問い合わせの件数の取得
	$linemax = $objQuery->count($from, $where);
	$this->tpl_linemax = $linemax;//tplで件数表示用変数

	// ページ送り用
	if(is_numeric($_POST['search_page_max'])) {//数字ならば
		$page_max = $_POST['search_page_max'];//POSTされた値を最大表示数とする
	} else {//数字でなければ
		$page_max = SEARCH_PMAX;//定数"SEARCH_MAX"を最大表示数とする
	}

	// ページ送りの取得
	$this->arrHidden['search_pageno'] =isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";
	// ページ送りの情報を設定
	$objNavi = new SC_PageNavi($this->arrHidden['search_pageno'],$linemax, $page_max,"fnNaviSearchPage", NAVI_PMAX);
	// 開始番号行の取得
	$startno = $objNavi->start_row;
	// ページ数の取得
	$this->arrPagenavi = $objNavi->arrPagenavi;

	// 取得範囲の指定(開始行番号、行数のセット)
	$objQuery->setlimitoffset($page_max, $startno);
	// 表示順序
	$objQuery->setorder($order);
	// 検索結果の取得
	$this->arrResults = $objQuery->select($col, $from, $where);
}

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

}
?>
