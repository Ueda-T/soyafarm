<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_RegularCSV.php');
/**
 * 定期受注CSVのページクラス
 *
 * LC_Page_Admin_Products_UploadCSV をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mdl_SMBC_Admin_Order_UploadCSVRegular extends LC_Page_Admin_Ex
{
    /** エラー情報 **/
    public $arrErr;

    /** 表示用項目 **/
    public $arrTitle;

    /** 結果行情報 **/
    public $arrRowResult;

    /** エラー行情報 **/
    public $arrRowErr;

    /** TAGエラーチェックフィールド情報 */
    public $arrTagCheckItem;

    /** テーブルカラム情報 (登録処理用) **/
    public $arrRegistColumn;

    /** 登録フォームカラム情報 **/
    public $arrFormKeyList;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_mainpage = MDL_SMBC_TEMPLATE_PATH . 'admin/order/upload_csv_regular.tpl';
        $this->tpl_subnavi = TEMPLATE_ADMIN_REALDIR . 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'upload_csv_regular';
        $this->tpl_pager = TEMPLATE_ADMIN_REALDIR . 'pager.tpl';
        $this->tpl_subtitle = '定期受注CSVアップロード';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);

        $this->max_upload_csv_size = $this->getUnitDataSize(CSV_SIZE);

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch ($this->getMode()) {
            case 'csv_upload':
                $this->doUploadCsv($objFormParam, $objUpFile);
                break;
            default:
                break;
        }

    }

    /**
     * 登録/編集結果のメッセージをプロパティへ追加する
     *
     * @param  integer $line_count 行数
     * @param  stirng  $message    メッセージ
     * @return void
     */
    public function addRowResult($line_count, $message)
    {
        $this->arrRowResult[] = $line_count . '行目：' . $message;
    }

    /**
     * 登録/編集結果のエラーメッセージをプロパティへ追加する
     *
     * @param  integer $line_count 行数
     * @param  stirng  $message    メッセージ
     * @return void
     */
    public function addRowErr($line_count, $message)
    {
        $this->arrRowErr[] = $line_count . '行目：' . $message;
    }

    /**
     * CSVアップロードを実行する
     *
     * @param  SC_FormParam  $objFormParam
     * @param  SC_UploadFile $objUpFile
     * @return void
     */
    public function doUploadCsv(&$objFormParam, &$objUpFile)
    {
        if (ob_get_level() > 0 && ob_get_length() > 0) {
            while (ob_end_clean());
        }
        if (($fp = $this->doFileUpload($objFormParam)) === false) {
            return false;
        }
        // 実行結果画面を表示
        $this->tpl_mainpage = MDL_SMBC_TEMPLATE_PATH . 'admin/order/upload_csv_regular_complete.tpl';

        // 登録先テーブル カラム情報の初期化
        $this->lfInitTableInfo();

        // 登録フォーム カラム情報
        $this->arrFormKeyList = $objFormParam->getKeyList();

        // 登録対象の列数
        $col_max_count = $objFormParam->getCount();
        // 行数
        $line_count = 0;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $errFlag = false;

        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);
            // 行カウント
            $line_count++;
            // ヘッダ行はスキップ
            if ($line_count == 1) {
                continue;
            }
            // 空行はスキップ
            if (empty($arrCSV)) {
                continue;
            }
            // 列数が異なる場合はエラー
            $col_count = count($arrCSV);
            if ($col_max_count != $col_count) {
                $this->addRowErr($line_count, '※ 項目数が' . $col_count . '個検出されました。項目数は' . $col_max_count . '個になります。');
                $errFlag = true;
                break;
            }

            // シーケンス配列を格納する。
            $objFormParam->setParam($arrCSV, true);
            // 入力値の変換
            $objFormParam->convParam();
            // <br>なしでエラー取得する。
            $arrCSVErr = $this->lfCheckError($objFormParam);


            // 入力エラーチェック
            if (count($arrCSVErr) > 0) {
                foreach ($arrCSVErr as $err) {
                    $this->addRowErr($line_count, $err);
                }
                $errFlag = true;
                break;
            }
            SC_Utils_Ex::extendTimeOut();
        }

        if ($errFlag) {
            return;
        }

        $line_count = 0;
        rewind($fp);
        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);

            $line_count++;
            // ヘッダ行はスキップ
            if ($line_count == 1) {
                continue;
            }
            // 空行はスキップ
            if (empty($arrCSV)) {
                continue;
            }

            // シーケンス配列を格納する。
            $objFormParam->setParam($arrCSV, true);
            // 入力値の変換
            $objFormParam->convParam();

            $objQuery->begin();
            $arrOrder = $this->createOrderTemp($objQuery, $line_count, $objFormParam);
            $arrParam = array(); // 送信パラメータのリファレンス
            $arrResponse = $this->doSendRegularSubscription($objQuery, $line_count, $arrOrder, $arrParam);
            if ($arrResponse['rescd'] != MDL_SMBC_RES_OK) {
                // エラーが発生
                $res = mb_convert_encoding($arrResponse['res'], "UTF-8", "auto");
                // 結果コード
                $rescd = $arrResponse['rescd'];
                $this->addRowErr($line_count, $rescd . ': ' . $res);
            } else {
                $this->completeOrder($objQuery, $line, $objFormParam, $arrOrder['order_temp_id'], $arrResponse);
                $this->addRowResult($line_count, '顧客番号(bill_no)：'.$arrParam['bill_no'] . ' / お名前：'
                                . $arrParam['bill_name']);
            }

            $objQuery->commit();
            SC_Utils_Ex::extendTimeOut();
        }
        fclose($fp);
        return;
    }

    /**
     * ファイル情報の初期化を行う.
     *
     * @return void
     */
    public function lfInitFile(&$objUpFile)
    {
        $objUpFile->addFile('CSVファイル', 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
    }

    /**
     * 入力情報の初期化を行う.
     *
     * @param array CSV構造設定配列
     * @return void
     */
    public function lfInitParam($objFormParam)
    {
        $objFormParam->addParam('会員番号', 'customer_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('要望等', 'message', LTEXT_LEN, 'aKV', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お名前(姓)', 'name01', STEXT_LEN, 'aKV', array('NO_SPTAB', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お名前(名)', 'name02', STEXT_LEN, 'aKV', array('NO_SPTAB', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お名前(フリガナ・姓)', 'kana01', STEXT_LEN, 'CKV', array('NO_SPTAB', 'SPTAB_CHECK' ,'MAX_LENGTH_CHECK', 'KANA_CHECK'));
        $objFormParam->addParam('お名前(フリガナ・名)', 'kana02', STEXT_LEN, 'CKV', array('NO_SPTAB', 'SPTAB_CHECK' ,'MAX_LENGTH_CHECK', 'KANA_CHECK'));
        $objFormParam->addParam('メールアドレス', 'email', null, 'a', array('NO_SPTAB', 'EMAIL_CHECK', 'SPTAB_CHECK' ,'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('お電話番号1', 'tel01', TEL_ITEM_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お電話番号2', 'tel02', TEL_ITEM_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お電話番号3', 'tel03', TEL_ITEM_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('郵便番号1', 'zip01', ZIP01_LEN, 'n', array('SPTAB_CHECK' ,'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('郵便番号2', 'zip02', ZIP02_LEN, 'n', array('SPTAB_CHECK' ,'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('都道府県', 'pref', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('住所1', 'addr01', MTEXT_LEN, 'aKV', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('住所2', 'addr02', MTEXT_LEN, 'aKV', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('性別', 'sex', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('小計', 'subtotal', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('値引き', 'discount', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('送料', 'deliv_fee', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('手数料', 'charge', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('加算ポイント', 'add_point', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('税金', 'tax', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('税率(%)', 'tax_rule', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('課税規則', 'tax_rate', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('支払い合計', 'payment_total', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('配送業者', 'deliv_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お支払い方法', 'payment_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('SHOPメモ', 'note', LTEXT_LEN, 'aKV', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('対応状況', 'status', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('注文日時', 'create_date', STEXT_LEN, 'aKV', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('発送完了日時', 'commit_date', STEXT_LEN, 'aKV', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('カード番号', 'card_no', 16, 'n', array('MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('カード有効期限', 'card_yukokigen', 4, 'n', array('MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('請求開始年月', 'seikyuu_kaishi_ym', 6, 'n', array('MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('請求終了年月', 'seikyuu_shuryo_ym', 6, 'n', array('MAX_LENGTH_CHECK', 'EXIST_CHECK'));
    }

    /**
     * ファイルアップロードを行う.
     *
     * 以下のチェックを行い, ファイルを一時領域へアップロードする.
     * 1. ファイルサイズチェック
     * 2. 拡張子チェック
     *
     * ファイルアップロード後, 一時ファイルのファイルポインタを返す.
     * アップロードに失敗した場合は, エラーメッセージを $this->arrErr に出力し, false を返す.
     *
     * SC_CheckError クラスや, SC_UploadFile クラスはファイルが残ってしまうため,
     * 独自のロジックを使用している.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return resouces|boolean
     */
    public function doFileUpload($objFormParam)
    {
        if ($_FILES['csv_file']['size'] <= 0) {
            $this->arrErr['csv_file'] = '※ ファイルがアップロードされていません';
        } elseif ($_FILES['csv_file']['size'] > CSV_SIZE *  1024) {
            $this->arrErr['csv_file'] = '※ CSVファイルのファイルサイズは' . $this->max_upload_csv_size . '以下のものを使用してください。<br />';
        } else {
            // SC_CheckError::FILE_EXT_CHECK とのソース互換を強めるための配列
            $value = array(
                0 => 'CSVファイル',
                1 => 'csv_file',
                2 => array('csv'),
            );
            // ▼SC_CheckError::FILE_EXT_CHECK から移植
            $match = false;
            if (strlen($_FILES[$value[1]]['name']) >= 1) {
                $filename = $_FILES[$value[1]]['name'];

                foreach ($value[2] as $check_ext) {
                    $match = preg_match('/' . preg_quote('.' . $check_ext) . '$/i', $filename) >= 1;
                    if ($match === true) {
                        break 1;
                    }
                }
            }

            if ($match === false) {
                $str_ext = implode('・', $value[2]);
                $this->arrErr[$value[1]] = '※ ' . $value[0] . 'で許可されている形式は、' . $str_ext . 'です。<br />';
            // ▲SC_CheckError::FILE_EXT_CHECK から移植
            } else {
                if (is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
                    $buf = mb_convert_encoding(file_get_contents($_FILES['csv_file']['tmp_name']), CHAR_CODE, 'SJIS-win');
                    $fp = tmpfile();
                    if ($fp !== false) {
                        fwrite($fp, $buf);
                        rewind($fp);
                        return $fp;
                    }
                }
            }
        }
        $this->arrErr['csv_file'] = '※ ファイルのアップロードに失敗しました。<br />';
        GC_Utils_Ex::gfPrintLog('File Upload Error!: ' . $_FILES['csv_file']['name'] . ' -> ' . $_FILES['csv_file']['tmp_name']);
        return false;
    }

    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    public function lfCheckError(&$objFormParam)
    {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError(false);

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrCustomer = $objQuery->getRow('*', 'dtb_customer', 'customer_id = ?',
                                         array($arrRet['customer_id']));
        if ($arrCustomer['del_flg'] == '1') {
            $objErr->arrErr['customer_id'] = '※この会員は既に退会しています。';
        }
        return $objErr->arrErr;
    }

    /**
     * 保存先テーブル情報の初期化を行う.
     *
     * @return void
     */
    public function lfInitTableInfo()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->arrRegistColumn = $objQuery->listTableFields('dtb_category');
    }

    /**
     * 受注一時データを作成する.
     */
    public function createOrderTemp($objQuery, $line, $objFormParam) {

        $arrList = $objFormParam->getHashArray();
        // customer_id が存在していれば dtb_customer から取得
        if (!SC_Utils_Ex::isBlank($arrList['customer_id'])
            || $arrList['customer_id'] !== '0' || $arrList['customer_id'] !== 0) {
            $arrCustomer = $objQuery->getRow('*', 'dtb_customer', 'customer_id = ?',
                                             array($arrList['customer_id']));
            unset($arrCustomer['create_date']); // dtb_order_temp.create_date と重複するため unset.
            if (!SC_Utils_Ex::isBlank($arrCustomer)) {
                $arrList = array_merge($arrList, $arrCustomer);
            }
        }

        $arrOrder = array();
        $arrDetail = array();
        // 会員情報をコピー
        SC_Helper_Purchase::copyFromOrder($arrOrder, $arrList, 'order', '',
                                          array('name01', 'name02', 'kana01', 'kana02',
                                                'sex', 'zip01', 'zip02', 'pref', 'addr01', 'addr02',
                                                'tel01', 'tel02', 'tel03', 'email'));
        // 受注情報をコピー
        SC_Helper_Purchase::copyFromOrder($arrOrder, $arrList, '', '',
                                          array('message', 'subtotal', 'discount', 'deliv_fee',
                                                'charge', 'add_point', 'tax', 'payment_total',
                                                'deliv_id', 'payment_id', 'note', 'create_date',
                                                'commit_date', 'device_type_id', 'card_no', 'card_yukokigen',
                                                'seikyuu_kaishi_ym', 'seikyuu_shuryo_ym'));

        // 受注詳細情報をコピー
        SC_Helper_Purchase::copyFromOrder($arrDetail, $arrList, '', '',
                                          array('tax_rate', 'tax_rule'));

        $arrDetail['product_id'] = '0';
        $arrDetail['product_class_id'] = '0';
        $arrDetail['product_name'] = '定期受注CSV登録商品';
        $arrDetail['price'] = $arrOrder['subtotal'];
        $arrDetail['quantity'] = '1';
        $arrOrder['session'] = serialize($arrDetail);
        $arrOrder['total'] = $arrOrder['payment_total']; // ポイント利用は無いので
        $arrOrder['order_temp_id'] = SC_Utils_Ex::sfGetUniqRandomId();
        $arrOrder['customer_id'] = $arrList['customer_id'];
        $arrOrder['device_type_id'] = '10'; //端末種別はPC固定
        if(SC_Utils_Ex::isBlank($arrOrder['discount'])){
            $arrOrder['discount'] = '0';
        }
        if(SC_Utils_Ex::isBlank($arrOrder['add_point'])){
            $arrOrder['add_point'] = '0';
        }
        if (SC_Utils_Ex::isBlank($arrOrder['customer_id'])) {
            $arrOrder['customer_id'] = '0';
        }
        if (SC_Utils_Ex::isBlank($arrOrder['create_date'])) {
            $arrOrder['create_date'] = $this->lfGetDbFormatTimeWithLine($line);
        }
        $arrOrder['update_date'] = $this->lfGetDbFormatTimeWithLine($line);

        $sqlval = $objQuery->extractOnlyColsOf('dtb_order_temp', $arrOrder);
        $objQuery->insert('dtb_order_temp', $sqlval);
        return $arrOrder;
    }

    /**
     * 継続課金の申込を行う
     */
    public function doSendRegularSubscription($objQuery, $line, $arrOrder, &$arrParam) {
        $objSmbcData = new SC_SMBC_RegularCSV();
        $objSmbcData->initArrParam();

        $arrOrder['shoporder_no'] = $objSmbcData->createRegularOrderId();
        $arrParam = $objSmbcData->makeParam($arrOrder);
        $objSmbcData->setParam($arrParam);

        $objSMBC =& SC_Mdl_SMBC::getInstance();
        $arrModule = $objSMBC->getSubData();

        if($arrModule['connect_url'] == "real"){
            // 本番用
            $connect_url = MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_REAL;
        }else{
            // テスト用
            $connect_url = MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_TEST;
        }
        $arrResponse = $objSmbcData->sendParam($connect_url);
        return $arrResponse;
    }

    /**
     * dtb_order 及び関連のレコードを登録する.
     */
    public function completeOrder($objQuery, $line, $objFormParam, $order_temp_id, $arrResponse) {
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrderTemp = $objPurchase->getOrderTemp($order_temp_id);

        $objSMBC =& SC_Mdl_SMBC::getInstance();
        $arrModule = $objSMBC->getSubData();

        $arrList = $objFormParam->getHashArray();

        // dtb_order を作成
        $arrOrder = $objQuery->extractOnlyColsOf('dtb_order', $arrOrderTemp);
        $arrOrder['order_id'] = $objQuery->nextVal('dtb_order_order_id');
        $arrOrder['status'] = ORDER_PAY_WAIT;
        $arrOrder['commit_date'] = '';
        $arrOrder['payment_date'] = '';
        $arrOrder['memo05'] = $arrModule['regular_product_type_id'];
        $arrOrder['note'] = '管理番号: ' . $arrResponse['shoporder_no'] . PHP_EOL;
        $arrOrder['note'] .= '請求年月: ' . substr($arrList['seikyuu_kaishi_ym'], 0, 4) . '/' . substr($arrList['seikyuu_kaishi_ym'], 4, 2);

            /* 使用しない
            $arrOrder['memo01'] = '';
            $arrOrder['memo02'] = '';
            $arrOrder['memo03'] = '';
            $arrOrder['memo04'] = '';
            $arrOrder['memo05'] = '';
            $arrOrder['memo06'] = '';
            $arrOrder['memo07'] = '';
            $arrOrder['memo08'] = '';
            $arrOrder['memo09'] = '';
            $arrOrder['memo10'] = '';
            */
        if (SC_Utils_Ex::isBlank($arrOrder['create_date'])) {
            $arrOrder['create_date'] = $this->lfGetDbFormatTimeWithLine($line);
        }
        $arrOrder['update_date'] = $this->lfGetDbFormatTimeWithLine($line);
        $arrOrder['del_flg'] = '0';
        $objQuery->insert('dtb_order', $arrOrder);

        // dtb_order_detail を作成
        $arrDetail = unserialize($arrOrderTemp['session']);
        $arrDetail['order_detail_id'] = $objQuery->nextVal('dtb_order_detail_order_detail_id');
        $arrDetail['order_id'] = $arrOrder['order_id'];
        $arrDetail = $objQuery->extractOnlyColsOf('dtb_order_detail', $arrDetail);
        $objQuery->insert('dtb_order_detail', $arrDetail);

        // dtb_shipping を作成
        $arrShipping = array();
        SC_Helper_Purchase::copyFromOrder($arrShipping, $arrOrderTemp, 'shipping', 'order',
                                          array('name01', 'name02', 'kana01', 'kana02',
                                                'zip01', 'zip02', 'pref', 'addr01', 'addr02',
                                                'tel01', 'tel02', 'tel03'));

        $arrShipping['shipping_id'] = '0';
        $arrShipping['order_id'] = $arrOrder['order_id'];
        $arrShipping['shipping_commit_date'] = '';
        $arrShipping['create_date'] = $arrOrder['create_date'];
        $arrShipping['update_date'] = $arrOrder['update_date'];
        $objQuery->insert('dtb_shipping', $arrShipping);

        // dtb_shipment_item を作成
        $arrShipItem = array();
        $arrShipItem['shipping_id'] = $arrShipping['shipping_id'];
        $arrShipItem['order_id'] = $arrOrder['order_id'];
        $arrShipItem['product_class_id'] = $arrDetail['product_class_id'];
        $arrShipItem['product_name'] = $arrDetail['product_class_id'];
        $arrShipItem['price'] = $arrOrder['subtotal'];
        $arrShipItem['quantity'] = '1';
        $objQuery->insert('dtb_shipment_item', $arrShipItem);

        // dtb_mdl_smbc_regular_customer を作成
        $arrRegularCustomer = array();
        SC_Helper_Purchase::copyFromOrder($arrRegularCustomer, $arrOrderTemp, '', 'order',
                                          array('name01', 'name02', 'kana01', 'kana02',
                                                'zip01', 'zip02', 'pref', 'addr01', 'addr02',
                                                'tel01', 'tel02', 'tel03',
                                                'email', 'sex'));
        $arrRegularCustomer['customer_id'] = $arrOrderTemp['customer_id'];
        $arrRegularCustomer['bill_no'] = $arrResponse['bill_no'];
        $arrRegularCustomer['create_date'] = $arrOrder['create_date'];
        $arrRegularCustomer['update_date'] = $arrOrder['update_date'];
        $arrRegularCustomer['del_flg'] = '0';
        // bill_no が重複している場合はスキップ
        if (!$objQuery->exists('dtb_mdl_smbc_regular_customer', 'bill_no = ?',
                               array($arrRegularCustomer['bill_no']))) {
                $objQuery->insert('dtb_mdl_smbc_regular_customer', $arrRegularCustomer);
        }

        // dtb_mdl_smbc_regular_order を作成
        $arrRegularOrder['bill_no'] = $arrResponse['bill_no'];
        $arrRegularOrder['shoporder_no'] = $arrResponse['shoporder_no'];
        $arrRegularOrder['order_id'] = $arrOrder['order_id'];
        $arrRegularOrder['regular_status'] = MDL_SMBC_REGULAR_STATUS_NONE;
        $arrRegularOrder['rescd'] = $arrResponse['rescd'];
        $arrRegularOrder['res'] = $arrResponse['res'];
        $arrRegularOrder['create_date'] = $arrOrder['create_date'];
        $arrRegularOrder['update_date'] = $arrOrder['update_date'];
        $objQuery->insert('dtb_mdl_smbc_regular_order', $arrRegularOrder);
    }


    /**
     * 指定された行番号をmicrotimeに付与してDB保存用の時間を生成する。
     * トランザクション内のCURRENT_TIMESTAMPは全てcommit()時の時間に統一されてしまう為。
     * TODO util メソッドに移動
     *
     * @param  string $line_no 行番号
     * @return string $time DB保存用の時間文字列
     */
    public function lfGetDbFormatTimeWithLine($line_no = '')
    {
        $time = date('Y-m-d H:i:s');
        // 秒以下を生成
        if ($line_no != '') {
            $microtime = sprintf('%06d', $line_no);
            $time .= ".$microtime";
        }

        return $time;
    }

    /**
     * データ量の単位を付与する
     *
     * 2.13.x より SC_Utils::getUnitDataSize() が存在するが前方互換のため.
     *
     * @param  int    $data
     * @return string
     * @see SC_Utils::getUnitDataSize()
     */
    protected function getUnitDataSize($data)
    {
        if ($data < 1000) {
            $return = $data . "KB";
        } elseif ($data < 1000000) {
            $return = $data/1000 . "MB";
        } else {
            $return = $data/1000000 . "GB";
        }

        return $return;
    }
}
