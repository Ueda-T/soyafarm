<?php

require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';

/**
 * 検索ブロック のページクラス.
 *
 * 2013.01.07 nitta
 * ヘッダーから検索ブロックを使用するため
 * すべての記述をLC_Page.phpに移動しました
 */
class LC_Page_FrontParts_Bloc_SearchProducts extends LC_Page_FrontParts_Bloc {
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

	// 2013.01.07 START nitta
	$objPage = new LC_Page_Ex();
	$objPage->init();

        // 選択中のカテゴリIDを判定する
        $this->category_id = $objPage->category_id;

        // 選択中のメーカーIDを判定する
        $this->maker_id = $objPage->maker_id;

        // カテゴリ検索用選択リスト
        $this->arrCatList = $objPage->arrCatList;

        // ブランド検索用選択リスト
        $this->arrBrandList = $objPage->arrBrandList;

        // メーカー検索用選択リスト
        $this->arrMakerList = $objPage->arrMakerList;

	// 2013.01.07 END nitta

    }
}
?>
