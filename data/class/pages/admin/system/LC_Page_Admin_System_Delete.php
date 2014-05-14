<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * メンバー削除 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_System_Delete.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_System_Delete extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        $objFormParam = new SC_FormParam;

        // パラメーターの初期化
        $this->initParam($objFormParam, $_GET);

        // パラメーターの検証
        if ($objFormParam->checkError()
            || !SC_Utils_ex::sfIsInt($id = $objFormParam->getValue('id'))) {

            GC_Utils_Ex::gfPrintLog("error id=$id");
            SC_Utils_Ex::sfDispError(INVALID_MOVE_ERRORR);
        }

        $id = $objFormParam->getValue('id');

        // レコードの削除
        $this->deleteMember($id);

        // リダイレクト
        $url = $this->getLocation(ADMIN_SYSTEM_URLPATH)
             . '?pageno=' . $objFormParam->getValue('pageno');
        SC_Response_Ex::sendRedirect($url);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメーター初期化.
     *
     * @param object $objFormParam
     * @param array  $arrParams  $_GET値
     * @return void
     */
    function initParam(&$objFormParam, &$arrParams) {

        $objFormParam->addParam('pageno', 'pageno', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('id', 'id', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->setParam($arrParams);

    }

    /**
     * メンバー情報削除の為の制御.
     *
     * @param integer $id 削除対象のmember_id
     * @return void
     */
    function deleteMember($id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        $this->renumberRank($objQuery, $id);
        $this->deleteRecode($objQuery, $id);

        $objQuery->commit();
    }

    /**
     * ランキングの振り直し.
     *
     * @param object $objQuery
     * @param integer $id 削除対象のmember_id
     * @return void|UPDATE の結果フラグ
     */
    function renumberRank(&$objQuery, $id) {

        // ランクの取得
        $where1 = "member_id = ?";
        $rank = $objQuery->get('rank', "dtb_member", $where1, array($id));

        // Updateする値を作成する.
        $where2 = "rank > ? AND del_flg <> 1";

        // UPDATEの実行 - 削除したレコードより上のランキングを下げてRANKの空きを埋める。
        return $objQuery->update("dtb_member", array(), $where2, array($rank), array('rank' => 'rank-1'));
    }

    /**
     * レコードの削除(削除フラグをONにする).
     *
     * @param object $objQuery
     * @param integer $id 削除対象のmember_id
     * @return void|UPDATE の結果フラグ
     */
    function deleteRecode(&$objQuery, $id) {

        // Updateする値を作成する.
        $sqlVal = array();
        $sqlVal['rank'] = 0;
        $sqlVal['del_flg'] = 1;
        $where = "member_id = ?";

        // UPDATEの実行 - ランクを最下位にする、DELフラグON
        return $objQuery->update("dtb_member", $sqlVal, $where, array($id));
    }
}
?>
