<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 支払方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Payment.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Basis_Payment extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/payment.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'payment';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '支払方法設定';
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
            $objFormParam->addParam('配送業者ID', 'payment_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            $arrErr = $objFormParam->checkError();
            if (!empty($this->arrErr['payment_id'])) {
                SC_Utils_Ex::sfDispException();
                return;
            }
            $post = $objFormParam->getHashArray();
        }

        switch($this->getMode()) {
            case 'delete':
                // ランク付きレコードの削除
                $objDb->sfDeleteRankRecord(
                    "dtb_payment", "payment_id", $post['payment_id']);
                // 再表示
                $this->objDisplay->reload();
                break;
            case 'up':
                $objDb->sfRankUp(
                    "dtb_payment", "payment_id", $post['payment_id']);
                // 再表示
                $this->objDisplay->reload();
                break;
            case 'down':
                $objDb->sfRankDown(
                    "dtb_payment", "payment_id", $post['payment_id']);
                // 再表示
                $this->objDisplay->reload();
                break;
        }
        $this->arrDelivList =
            $objDb->sfGetIDValueList("dtb_deliv", "deliv_id", "service_name");
        $this->arrPaymentListFree = $this->lfGetPaymentList();
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
     * 支払方法一覧の取得.
     */
    function lfGetPaymentList() {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    payment_id,
    payment_method,
    charge,
    rule,
    upper_rule,
    note,
    fix,
    charge_flg
FROM
    dtb_payment
WHERE
    del_flg = 0
ORDER BY rank DESC
EOF;
        return $objQuery->getAll($sql);
    }
}
?>
