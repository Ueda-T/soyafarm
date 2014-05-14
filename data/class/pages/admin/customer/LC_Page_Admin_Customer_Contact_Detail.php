<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * お問い合わせ詳細 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Customer_Contact_Detail.php 21118 2011-08-03 12:28:10Z kajiwara $
 */
class LC_Page_Admin_Customer_Contact_Detail extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/contact_detail.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subnavi = 'customer/subnavi.tpl';
        $this->tpl_subno = 'contact_index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_subtitle = 'お問い合わせ詳細';

        $masterData = new SC_DB_MasterData_Ex();
        //都道府県データをマスターデータから取得する。
	$this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
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
	    $objSess = new SC_Session();
	    SC_Utils_Ex::sfIsSuccess($objSess);

	    $this->objQuery = new SC_Query_Ex();
	    //問合せ編集情報"contact_id"取得
	    //GET値→POST値の順に"contact_id"が存在するかチェックし、あれば取得
	    if(isset($_GET["contact_id"]) && is_numeric($_GET["contact_id"])) {
		    $contact_id = $_GET["contact_id"];
	    } elseif(isset($_POST["contact_id"]) && is_numeric($_POST["contact_id"])) {
		    $contact_id = $_POST["contact_id"];
	    }

	    if($contact_id) {
		    $sql = "SELECT * FROM dtb_contact WHERE del_flg = 0 AND contact_id = ?";
		    //消去状態になっていない、問合せデータ取得
		    $result = $this->objQuery->getAll($sql,array($contact_id));
		    //取得したデータの1つ目の要素を取り出す
		    $this->list_data = $result[0];

		    if($this->list_data["status"] == 0 && !isset($_POST["status"])) {//ステータスが未読("status"が"0")でステータスの変更がなければ
			    $this->list_data["status"] = 1;
			    $this->lfRegiserData(array("status"=>1),array(array("column"=>"status")));//ステータスを既読（"status"が"1"）に変更
		    } elseif($_POST["mode"] == "confirm") {
			    // 入力データの変換内容の指定
			    $arrRegisterColumn = array(array(  "column" => "status", "convert" => "n" ),
					    array(  "column" => "del_flg", "convert" => "n"),
					    );
			    $this->arrForm = $_POST;
			    $this->arrForm = $this->lfConvertParam($this->arrForm, $arrRegisterColumn);//入力データの変換
			    // 入力チェック
			    $this->arrErr = $this->lfErrorCheck($this->arrForm);
			    if ($this->arrErr) {// 入力エラー発生
				    foreach($this->arrForm as $key => $val) {
					    $this->list_data[ $key ] = $val;
				    }
			    } else {
				    $this->list_data["status"] = $this->arrForm["status"];
				    $this->lfRegiserData(array("status"=>$this->arrForm["status"]),array(array("column"=>"status")));//ステータスを変更
			    }
		    }
		    //問合せ履歴情報の取得
		    $this->arrContactHistory = $this->lfContactHistory($this->list_data['customer_id']);
	    } else {
		    $this->list_data = array();
	    }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }


   // 編集登録
    function lfRegiserData($array, $arrRegisterColumn) {

        foreach ($arrRegisterColumn as $data) {
            if($array[$data["column"]] != "") {
                $arrRegist[$data["column"]] = $array[$data["column"]];
            } else {
                unset($arrRegist[$data["column"]]);
            }
        }

        //-- 編集登録実行
        $this->objQuery->update("dtb_contact", $arrRegist, "contact_id = ?",array($this->list_data["contact_id"]));
    }

 
    //----　取得文字列の変換
    function lfConvertParam($array, $arrRegisterColumn) {
        /*
         *    文字列の変換
         *    K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *    C :  「全角ひら仮名」を「全角かた仮名」に変換
         *    V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *    n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // カラム名とコンバート情報
        foreach ($arrRegisterColumn as $data) {
            $arrConvList[ $data["column"] ] = $data["convert"];
        }
        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(strlen(($array[$key])) > 0) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    //---- 入力エラーチェック
    function lfErrorCheck($array) {

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("対応状態", 'status'), array("EXIST_CHECK"));
        return $objErr->arrErr;

   }

//問合せ履歴情報の取得
    function lfContactHistory($customer_id){
        $this->tpl_pageno = $_POST['search_pageno'];
        $this->edit_customer_id = $customer_id;

        // ページ送りの処理
        $page_max = SEARCH_PMAX;
        //問合せ履歴の件数取得
        $this->tpl_linemax = $this->objQuery->count("dtb_contact","customer_id=? AND del_flg = 0 ", array($customer_id));
        $linemax = $this->tpl_linemax;

        // ページ送りの取得
        $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage2", NAVI_PMAX);
        $this->arrPagenavi = $objNavi->arrPagenavi;
        $this->arrPagenavi['mode'] = '';
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $this->objQuery->setlimitoffset($page_max, $startno);
        // 表示順序
        $order = "contact_id DESC";
        $this->objQuery->setorder($order);
        //問合せ履歴情報の取得
        $arrContactHistory = $this->objQuery->select("*", "dtb_contact", "customer_id=? AND del_flg = 0 ", array($customer_id));
        return $arrContactHistory;
    }
}
?>
