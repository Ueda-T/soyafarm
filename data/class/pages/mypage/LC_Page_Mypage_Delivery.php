<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * お届け先編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Mypage_Delivery.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Mypage_Delivery extends LC_Page_AbstractMypage_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = '配送先の登録・修正';
        $this->tpl_mypageno = 'delivery';
        $masterData         = new SC_DB_MasterData_Ex();
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
		$_SESSION["MYPAGENO"] = $this->tpl_mypageno;

        $objCustomer    = new SC_Customer_Ex();
        $customer_id    = $objCustomer->getValue('customer_id');
        $objFormParam   = new SC_FormParam_Ex();

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch($this->getMode()) {
        // お届け先の削除確認
        case 'confirm':

            // モバイル用
            if(SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {

                foreach($_POST as $key => $data) {
                    if (preg_match("/^delete_/", $key)) {
                        $other_deliv_id =
                            str_replace("delete_", "", $key);
                    }
                }
                $objFormParam->setValue("other_deliv_id", $other_deliv_id);

                if ($objFormParam->checkError()) {
                    SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                    exit;
                }

                // 届け先情報をDBから取得
                //$other_deliv_id = $objFormParam->getValue('other_deliv_id');
                $this->arrDeliv = $this->getArrayOtherDeliv($other_deliv_id);

                // 確認画面を表示
                $this->tpl_subtitle = 'お届け先削除(確認)';
                $this->tpl_mainpage = 'mypage/delivery_delete_confirm.tpl';
            }
            
            break;
        // お届け先の削除実行
        case 'delete':
            if ($objFormParam->checkError()) {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                exit;
            }

            // 削除実行
            $this->deleteOtherDeliv($customer_id, $objFormParam->getValue('other_deliv_id'));

            // モバイル用
            if(SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
                // 完了画面を表示
                $this->tpl_subtitle = 'お届け先削除(完了)';
                $this->tpl_mainpage = 'mypage/delivery_delete_complete.tpl';
            }
            break;
            
        // スマートフォン版のもっと見るボタン用
        case 'getList':
                $arrData = $objFormParam->getHashArray();
                //別のお届け先情報
                $arrOtherDeliv = $this->getOtherDeliv($customer_id, (($arrData['pageno'] - 1) * SEARCH_PMAX));
                //県名をセット
                $arrOtherDeliv = $this->setPref($arrOtherDeliv, $this->arrPref);
                $arrOtherDeliv['delivCount'] = count($arrOtherDeliv);
                $this->arrOtherDeliv = $arrOtherDeliv;
                echo SC_Utils_Ex::jsonEncode($this->arrOtherDeliv);
                exit;
                break;

        // お届け先の表示
        default:
            break;
        }

        //別のお届け先情報
        $this->arrOtherDeliv = $this->getOtherDeliv($customer_id);

        //お届け先登録数
        $this->tpl_linemax = count($this->arrOtherDeliv);
        
        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;
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
     * フォームパラメータの初期化
     *
     * @return SC_FormParam
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('お届け先ID', 'other_deliv_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam("現在ページ", "pageno", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
    }

    /**
     * お届け先一覧の取得
     *
     * @param integer $customerId
     * @param integer $startno
     * @return array
     */
    function getOtherDeliv($customer_id, $startno = '') {

        $objQuery   =& SC_Query_Ex::getSingletonInstance();

        //スマートフォン用の処理
        if ($startno != '') {
            $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        }

        $sql =<<<EOF
SELECT
    *
FROM
    dtb_other_deliv
WHERE
    customer_id = $customer_id
ORDER BY other_deliv_id DESC
EOF;
        return $objQuery->getAll($sql);
    }

    /**
     * 別届け先IDを指定してお届け先情報を取得
     *
     * @param integer $other_deliv_id
     * @return array
     */
    function getArrayOtherDeliv($other_deliv_id) {

        $objQuery   =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    *
FROM
    dtb_other_deliv
WHERE
    other_deliv_id = '{$other_deliv_id}'
EOF;
        $arrRes = $objQuery->getAll($sql);
        return $arrRes[0];
    }

    /**
     * お届け先の削除
     *
     * @param integer $customerId
     * @param integer $delivId
     */
    function deleteOtherDeliv($customer_id, $deliv_id) {

        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<EOF
DELETE
FROM
    dtb_other_deliv
WHERE
    customer_id = $customer_id
    AND other_deliv_id = $deliv_id
EOF;
        $objQuery->query($sql);
    }
    
    /**
     * 県名をセット
     *
     * @param array $arrOtherDeliv
     * @param array $arrPref
     * return array
     */
    function setPref($arrOtherDeliv, $arrPref) {
        if (is_array($arrOtherDeliv)) {
            foreach($arrOtherDeliv as $key => $arrDeliv) {
                $arrOtherDeliv[$key]['prefname'] = $arrPref[$arrDeliv['pref']];
            }
        }
        return $arrOtherDeliv;
    }
}
