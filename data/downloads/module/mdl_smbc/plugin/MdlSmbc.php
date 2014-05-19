<?php
/*
 * CategoryContents
 * Copyright (C) 2012 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC_Send.php');

/*
 * SMBC決済モジュール用
 */
class MdlSmbc extends SC_Plugin_Base {

    /**
     * コンストラクタ
     * プラグイン情報(dtb_plugin)をメンバ変数をセットします.
     * @param array $arrSelfInfo dtb_pluginの情報配列
     * @return void
     */
    public function __construct(array $arrSelfInfo) {

        parent::__construct($arrSelfInfo);
        $this->plugin_path = PLUGIN_UPLOAD_REALDIR . $this->arrSelfInfo['plugin_code'];

    }

    /**
     * インストール時に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function install($arrPlugin) {
        // nop
    }

    /**
     * 削除時に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function uninstall($arrPlugin) {
        // nop
    }

    /**
     * 有効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function enable($arrPlugin) {
        // nop
    }

    /**
     * 無効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function disable($arrPlugin) {
        // nop
    }

    /**
     * 支払方法選択依頼メールを送る.
     * @param LC_Page_Admin_Order_Edit $objPage <管理画面>受注管理.
     * @return void
     */
    function send_data_smbc($objPage) {

        $objPurchase  = new SC_Helper_Purchase_Ex();
        $objFormParam = new SC_FormParam_Ex();

        $arrValuesBefore = array();
        $arrValuesBefore['payment_id'] = NULL;
        $arrValuesBefore['payment_method'] = NULL;

        $post = $_POST;
        switch ($post['mode']) {
            // 送信押下時
            case 'send_data_smbc':
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $objPage->lfInitParam($objFormParam);
                    $objFormParam->setParam($_POST);
                    $objFormParam->convParam();

                    $objPage->arrErr = $objPage->lfCheckError($objFormParam);
                    // 追加チェック
                    if (SC_Utils_Ex::isBlank($objPage->arrErr)) {
                        $objPage->arrErr = MdlSmbc::checkErrorSendData($objFormParam);
                    }

                    if (SC_Utils_Ex::isBlank($objPage->arrErr)) {
                        // 登録処理
                        $message = '受注を登録し、依頼メールを送信しました。';
                        $order_id = $objPage->doRegister(null, $objPurchase, $objFormParam, $message, $arrValuesBefore);
                        if ($order_id >= 0) {
                            $objPage->tpl_mode = 'edit';
                            $objFormParam->setValue('order_id', $order_id);
                            $objPage->setOrderToFormParam($objFormParam, $order_id);
                            // 送信処理
                            $res = MdlSmbc::sendOrderData($objFormParam);
                        }
                        $objPage->tpl_onload = "window.alert('" . $message . "');";
                    } else if (!SC_Utils_Ex::isBlank($objPage->arrErr['js_alert'])) {
                        // エラーアラート表示
                        $objPage->tpl_onload = "window.alert('" . $objPage->arrErr['js_alert'] . "');";
                    }
                }
                $objPage->arrForm = $objFormParam->getFormParamList();
                break;
            default:
                break;
        }

        // 新規、かつ支払方法がSMBCであれば送信ボタン表示
        if (SC_Utils_Ex::isBlank($_POST['order_id']) && SC_Utils_Ex::isBlank($order_id)) {
            if ($this->checkPaymentModule($_POST['payment_id'])) {
                $objPage->tpl_smbc_sendmail = true;
            }
        }
    }

    /**
     * 固定割当.
     * @param LC_Page_Admin_Customer_Edit $objPage <管理画面>会員管理.
     * @return void
     */
    function fixBankAccountSmbc($objPage) {
        $post = $_POST;
        if(!empty($post['mode'])){
            $objQuery =& SC_Query_Ex::getSingletonInstance();

            if(!empty($post['edit_customer_id'])){
                $post['customer_id'] = $post['edit_customer_id'];
            }

            $table = 'dtb_mdl_smbc_bankaccount';
            $where = 'customer_id = ?';
            $whereVal = array((int)$post['customer_id']);
            $smbcCustomer = $objQuery->getRow('*', 'dtb_mdl_smbc_customer', $where, $whereVal);

            switch ($post['mode']) {
                case 'send_data_smbc_bank_assign':
                    if(!empty($smbcCustomer['account_number'])){
                        $objPage->bankaccount = "店番号：".$smbcCustomer['branch_code']." 口座番号：".$smbcCustomer['account_number'];
                        break;
                    }
                    $count = $objQuery->count('dtb_mdl_smbc_bankaccount', 'change_flg = ? AND bill_name IS NULL', array(0));
                    if($count == 0){
                        $objPage->arrError['bankaccount'] = "※ 割当できる空き口座がありません。<br />";
                        break;
                    }
                    $where = "change_flg = ? AND bill_name IS NULL";
                    $whereVal = array(0);
                    $order  = "update_date ASC,";
                    $order .= "CASE branch_code WHEN '".$smbcCustomer['branch_code']."' THEN '9999' ELSE branch_code END ASC,";
                    $order .= "account_number ASC";
                    $objQuery->setOrder($order);
                    $objQuery->setLimit(1);
                    $smbcBankaccount = $objQuery->select('*', $table, $where, $whereVal);

                    $arrVal = array();
                    $arrVal['bill_no'] = str_pad((int)$post['customer_id'], 14, '0', STR_PAD_LEFT);
                    $arrVal['update_date'] = "CURRENT_TIMESTAMP";
                    $where = "bank_code = ? AND branch_code = ? AND account_number = ?";
                    $whereVal = array($smbcBankaccount[0]['bank_code'], $smbcBankaccount[0]['branch_code'], $smbcBankaccount[0]['account_number']);
                    $objQuery->update($table, $arrVal, $where, $whereVal);

                    $objPage->bankaccount = "店番号：".$smbcBankaccount[0]['branch_code']." 口座番号：".$smbcBankaccount[0]['account_number'];
                    $_SESSION['smbc_bank_action'] = 'add';
                    break;
                case 'send_data_smbc_bank_remove':
                    $objPage->bankaccount = '';
                    $_SESSION['smbc_bank_action'] = 'del';
                    break;
                case 'confirm':
                case 'return':
                    if($_SESSION['smbc_bank_action'] == 'add' && empty($smbcCustomer['account_number'])){
                        $where = "change_flg = ? AND bill_no = ? AND bill_name IS NULL";
                        $whereVal = array(0, str_pad((int)$post['customer_id'], 14, '0', STR_PAD_LEFT));
                        $objQuery->setOrder("update_date DESC");
                        $objQuery->setLimit(1);
                        $smbcBankaccount = $objQuery->select('*', $table, $where, $whereVal);

                        $objPage->bankaccount = "店番号：".$smbcBankaccount[0]['branch_code']." 口座番号：".$smbcBankaccount[0]['account_number'];
                    }elseif($_SESSION['smbc_bank_action'] != 'del' && !empty($smbcCustomer['account_number'])){
                        $objPage->bankaccount = "店番号：".$smbcCustomer['branch_code']." 口座番号：".$smbcCustomer['account_number'];
                    }
                    break;
                case 'complete':
                    if($_SESSION['smbc_bank_action'] == 'add' && empty($smbcCustomer['account_number'])){
                        $objQuery->begin();
                        // 割当
                        $where = "change_flg = ? AND bill_no = ? AND bill_name IS NULL";
                        $whereVal = array(0, str_pad((int)$post['customer_id'], 14, '0', STR_PAD_LEFT));
                        $objQuery->setOrder("update_date DESC");
                        $objQuery->setLimit(1);
                        $smbcBankaccount = $objQuery->select('*', $table, $where, $whereVal);

                        $arrVal = array();
                        $arrVal['bill_name'] = mb_convert_kana($post['name01'].$post['name02'], "KVAN");
                        $arrVal['bill_name'] = mb_strcut($arrVal['bill_name'], 0, MDL_SMBC_CUSTOMER_NAME_MAX_LEN);

                        $arrVal['change_flg'] = "1";
                        $where = "bank_code = ? AND branch_code = ? AND account_number = ? AND bill_no = ?";
                        $whereVal = array($smbcBankaccount[0]['bank_code'], $smbcBankaccount[0]['branch_code'], $smbcBankaccount[0]['account_number'], str_pad((int)$post['customer_id'], 14, '0', STR_PAD_LEFT));
                        $objQuery->update($table, $arrVal, $where, $whereVal);
                        if ($objQuery->isError()) {
                            $objQuery->rollback();
                        }else{
                            $table = 'dtb_mdl_smbc_customer';
                            $arrVal = array();
                            $arrVal['branch_code'] = $smbcBankaccount[0]['branch_code'];
                            $arrVal['account_number'] = $smbcBankaccount[0]['account_number'];

                            if(empty($smbcCustomer['customer_id'])){
                                $arrVal['customer_id'] = $post['customer_id'];
                                $objQuery->insert($table, $arrVal);
                            }else{
                                $where = "customer_id = ?";
                                $objQuery->update($table, $arrVal, $where, array($post['customer_id']));
                            }
                            if ($objQuery->isError()) {
                                $objQuery->rollback();
                            }else{
                                $objQuery->commit();
                            }
                        }
                    }elseif($_SESSION['smbc_bank_action'] == 'del' && !empty($smbcCustomer['account_number'])){
                        $objQuery->begin();
                        // 解除
                        $where = "branch_code = ? AND account_number = ? AND bill_no = ?";
                        $whereVal = array($smbcCustomer['branch_code'], $smbcCustomer['account_number'], str_pad((int)$post['customer_id'], 14, '0', STR_PAD_LEFT));
                        $objQuery->setLimit(1);
                        $smbcBankaccount = $objQuery->select('*', $table, $where, $whereVal);

                        $arrVal = array();
                        if($smbcBankaccount[0]['change_flg'] == '1'){
                            $arrVal['bill_no'] = '';
                            $arrVal['change_flg'] = '0';
                        }else{
                            $arrVal['change_flg'] = '2';
                        }
                        $arrVal['bill_name'] = '';
                        $arrVal['update_date'] = "CURRENT_TIMESTAMP";
                        $where  = "branch_code = ? AND account_number = ? AND bill_no = ?";
                        $whereVal = array($smbcCustomer['branch_code'], $smbcCustomer['account_number'], str_pad((int)$post['customer_id'], 14, '0', STR_PAD_LEFT));
                        $objQuery->update($table, $arrVal, $where, $whereVal);
                        if ($objQuery->isError()) {
                            $objQuery->rollback();
                        }else{
                            $table = 'dtb_mdl_smbc_customer';
                            $arrVal = array();
                            $arrVal['account_number'] = "";
                            $objQuery->update($table, $arrVal, "customer_id = ?", array($post['customer_id']));
                            if ($objQuery->isError()) {
                                $objQuery->rollback();
                            }else{
                                $objQuery->commit();
                            }
                        }
                    }
                    unset($_SESSION['smbc_bank_action']);
                    break;
                default:
                    if(!empty($smbcCustomer['account_number'])){
                        $objPage->bankaccount = "店番号：".$smbcCustomer['branch_code']." 口座番号：".$smbcCustomer['account_number'];
                    }
                    unset($_SESSION['smbc_bank_action']);
                    break;
            }
            if($post['mode'] == 'send_data_smbc_bank_assign' || $post['mode'] == 'send_data_smbc_bank_remove'){
                $_POST['mode'] = 'return';
            }
        }
    }

    /**
     * prefilterコールバック関数
     * テンプレートの変更処理を行います.
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {

        // SC_Helper_Transformのインスタンスを生成.
        $objTransform = new SC_Helper_Transform($source);

        $template_dir = PLUGIN_UPLOAD_REALDIR . $this->arrSelfInfo['plugin_code'] . '/';
        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_MOBILE:
            case DEVICE_TYPE_SMARTPHONE:
            case DEVICE_TYPE_PC:
                // nop
                // 処理を追加する場合は合わせて LC_Page_Mdl_SMBC_Config.php でキャッシュクリアすること
                break;
            case DEVICE_TYPE_ADMIN:
            default:
                // 受注管理受注登録画面(新規受付時のみ) 送信ボタン追加
                if (strpos($filename, 'order/edit.tpl') !== false) {
                    $objTransform->select('div.btn-area ul')->appendChild(file_get_contents($template_dir . 'smbc_admin_order_edit.tpl'));
                }
                // 顧客登録に銀行口座固定割当フィールド追加
                if (strpos($filename, 'customer/edit.tpl') !== false) {
                    $objTransform->select('table.form')->appendChild(file_get_contents($template_dir . 'smbc_admin_customer_edit.tpl'));
                }
                if (strpos($filename, 'customer/edit_confirm.tpl') !== false) {
                    $objTransform->select('table.form')->appendChild(file_get_contents($template_dir . 'smbc_admin_customer_edit_confirm.tpl'));
                }
                // サビナビに口座情報管理を追加
                if (strpos($filename, 'system/subnavi.tpl') !== false) {
                    $objTransform->select('ul.level1')->appendChild(file_get_contents($template_dir . 'systemSubnavi.tpl'));
                }
                break;
        }

        // 変更を実行します
        $source = $objTransform->getHTML();
    }


    /**
     * 支払方法選択依頼メールを送る.
     *
     * @param array $objFormParam
     * @return void
     */
    function sendOrderData(&$objFormParam) {

        $objSMBC = new SC_SMBC_Send();

        $order_id = $objFormParam->getValue('order_id');
        $objSMBC->initArrParam();
        $arrParam = $objSMBC->makeParam($order_id);
        $res = $objSMBC->setSendData($arrParam);

    }

    /**
     * 入力内容のチェックを行う(依頼メール送信時の追加チェック).
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function checkErrorSendData(&$objFormParam) {
        if ($this->checkPaymentModule($objFormParam->getValue('payment_id'))) {
            if (SC_Utils_Ex::isBlank($objFormParam->getValue('order_email'))) {
                $arrErr['order_email'] = '※ メールアドレスを入力してください。<br>';
            }
            if (SC_Utils_Ex::isBlank($objFormParam->getValue('order_tel01'))
                || SC_Utils_Ex::isBlank($objFormParam->getValue('order_tel02'))
                || SC_Utils_Ex::isBlank($objFormParam->getValue('order_tel03'))) {
                $arrErr['order_tel01'] = '※ 電話番号を入力してください。<br>';
            }
            // 商品数
            $arrValues = $objFormParam->getHashArray();
            $quantity = 0;
            foreach($arrValues['quantity'] as $key => $value) {
                $quantity += 1;
            }
            if ($quantity <= 0) {
                $arrErr['js_alert'] = '※ 商品を追加してください。';
            }
            if ($arrValues['payment_total'] == 0) {
                $arrErr['js_alert'] = '※ お支払い合計が0円のため支払いはありません。';
            }

        } else {
                $arrErr['js_alert'] = '※ 指定のお支払方法ではメール送信できません。';
        }
        return $arrErr;
    }

    // SMBCの決済方法かどうか確認
    function checkPaymentModule($payment_id = '') {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        if (!empty($payment_id)) {
            $res = $objQuery->getOne('SELECT module_code FROM dtb_payment WHERE payment_id=?', array($payment_id));
            if ($res == 'mdl_smbc') {
                return true;
            }
        }
        return false;

    }

    /**
     * ログファイルの一覧に決済モジュールのログを追加する
     *
     */
    function loadLogListWithSmbcLog($objPage) {
        $objPage->arrLogList['SMBC'] = 'SMBCファイナンスサービス決済ログファイル';
    }

}

?>
