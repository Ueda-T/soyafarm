<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 配送方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Delivery.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Basis_Delivery extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/delivery.tpl';
        $this->tpl_subno = 'delivery';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData("mtb_taxrule");
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '配送方法設定';
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
        $objDb = new SC_Helper_DB_Ex();
        $mode = $this->getMode();

        if (!empty($_POST)) {
            $objFormParam = new SC_FormParam_Ex();
            $objFormParam->setParam($_POST);

            $this->arrErr = $this->lfCheckError($mode, $objFormParam);
            if (!empty($this->arrErr['deliv_id'])) {
                SC_Utils_Ex::sfDispException();
                return;
            }
        }

        switch($mode) {
        case 'delete':
            // ランク付きレコードの削除
            $objDb->sfDeleteRankRecord("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            $this->objDisplay->reload(); // PRG pattern
            break;
        case 'up':
            $objDb->sfRankUp("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            $this->objDisplay->reload(); // PRG pattern
            break;
        case 'down':
            $objDb->sfRankDown("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            $this->objDisplay->reload(); // PRG pattern
            break;
        default:
            break;
        }

        $this->arrDelivList = $this->lfGetDelivList();
    }

    /**
     * 配送業者一覧の取得
     *
     * @return array
     */
    function lfGetDelivList() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    deliv_id,
    name,
    service_name
FROM
    dtb_deliv
WHERE
    del_flg = 0
ORDER BY rank DESC
EOF;
        return $objQuery->getAll($sql);
    }

    /**
     * 入力エラーチェック
     *
     * @param string $mode
     * @return array
     */
    function lfCheckError($mode, &$objFormParam) {
        $arrErr = array();
        switch ($mode) {
            case 'delete':
            case 'up':
            case 'down':
                $objFormParam->addParam('配送業者ID', 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

                $objFormParam->convParam();

                $arrErr = $objFormParam->checkError();
                break;
            default:
                break;
        }
        return $arrErr;
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
