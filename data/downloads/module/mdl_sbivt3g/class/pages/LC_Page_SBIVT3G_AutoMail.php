<?php
/**
 * LC_Page_SBIVT3G_AutoMail.php - LC_Page_SBIVT3G_AutoMail クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_AutoMail.php 181 2012-07-27 05:45:17Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール 期限前・期限切れメール自動配信処理クラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version    Release: @package_version@
 * @link        http://www.veritrans.co.jp/3gps
 * @access  public
 * @author  K.Hiranuma
 */
class LC_Page_SBIVT3G_AutoMail extends LC_Page_SBIVT3G {

    // {{{ properties

    /** 設定オブジェクト */
    var $objSetting;

    /** 対象決済IDの配列 */
    var $arrTargetPayment;

    /** 対象日付 */
    var $execDate;

    /** エラー格納配列 */
    var $arrErrors;

    /** 期限前メールテンプレートID */
    var $notice_id;

    /** 期限切れメールテンプレートID */
    var $expire_id;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @access public
     * @return void
     */
    function init() {
        // エラー処理初期化
        restore_error_handler();

        // 出力を抑制する
        ob_start();

        // 親処理実行
        parent::init();

        $this->arrErrors = array();

        // 設定
        $this->objSetting =& SC_Helper_SBIVT3G_Setting::getSingletonInstance();

        // 日付の取得
        if (isset($_SERVER['argv'][1]) == true
        && preg_match('|^\d{4}/\d{2}/\d{2}$|', $_SERVER['argv'][1]) == true) {
            $this->execDate = $_SERVER['argv'][1];
        } else {
            $this->execDate = date('Y/m/d');
        }

        // 対象決済
        $this->arrTargetPayment = array(
            MDL_SBIVT3G_INNER_ID_CVS,
            MDL_SBIVT3G_INNER_ID_PAYEASY_ATM,
            MDL_SBIVT3G_INNER_ID_PAYEASY_NET,
            MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL,
            MDL_SBIVT3G_INNER_ID_EDY_PC_APP,
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL,
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP,
            MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL,
            MDL_SBIVT3G_INNER_ID_SUICA_PC_APP,
            MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP,
            MDL_SBIVT3G_INNER_ID_WAON_PC_APP,
        );
    }

    /**
     * Page のプロセス.
     *
     * @access public
     * @return void
     */
    function process() {
        // sendResponse()は実行しない
        $this->action();
    }

    /**
     * デストラクタ.
     *
     * @access public
     * @return void
     */
    function destroy() {

        // エラーがあればメール通知を行う 
        if (count($this->arrErrors) > 0) {
            $this->sendErrorMail($this->arrErrors);
        }

        // 親処理実行
        parent::destroy();

        // 出力があればログへ逃す
        $buffer = ob_get_contents();
        if (strcmp($buffer, '') != 0) {
            $logger =& TGMDK_Logger::getInstance();
            $logger->info('入金通知 - 標準出力:' .LF. $buffer);
        }

        // バッファを開放
        ob_end_clean();
    }

    /**
     * Page のアクション.
     *
     * @access public
     * @return void
     */
    function action() {

        $logger =& TGMDK_Logger::getInstance();

        // 定数チェック
        if (MDL_SBIVT3G_AUTOMAIL_ENABLED == false) {
            $this->arrErrors[] = 'この機能はご利用いただけません。' . LF;
            return;
        }

        $logger->info('メール自動配信処理 開始');

        // テンプレートIDを取得
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objMasterData = new SC_DB_MasterData_Ex();
        $objMasterData->objQuery =& $objQuery;
        $arrMtbMailTemplate =
            $objMasterData->getDbMasterData('mtb_mail_template');
        $arrNoticeKey = array_keys($arrMtbMailTemplate,
            MDL_SBIVT3G_MAIL_TPL_TITLE_PAY_NOTICE);
        $this->notice_id = @$arrNoticeKey[0];
        $arrExpireKey = array_keys($arrMtbMailTemplate,
            MDL_SBIVT3G_MAIL_TPL_TITLE_PAY_EXPIRE);
        $this->expire_id = @$arrExpireKey[0];
        $arrMtbMailTplPath =
            $objMasterData->getDbMasterData('mtb_mail_tpl_path');

        // 期限前メール送信処理
        if ($this->objSetting->get('noticeMailFlg') == true) {
            if ($this->notice_id == 0
            || isset($arrMtbMailTplPath[$this->notice_id]) == false) {
                $this->arrErrors[] =
                    '期限前メールのテンプレートが設定されていません。' . LF
                    . '『ベリトランス3G MDK決済モジュール』の設定を再度実施'
                    . 'して下さい。' . LF;

            } else {
                $this->doSendNoticeMail();
            }
        }

        // 期限切れメール送信処理
        if ($this->objSetting->get('expireMailFlg') == true) {
            if ($this->expire_id == 0
            || isset($arrMtbMailTplPath[$this->expire_id]) == false) {
                $this->arrErrors[] =
                    '期限切れメールのテンプレートが設定されていません。' . LF
                    . '『ベリトランス3G MDK決済モジュール』の設定を再度実施'
                    . 'して下さい。' . LF;
            } else {
                $this->doSendExpireMail();
            }
        }

        $logger->info('メール自動配信処理 終了');
    }

    /**
     * エラー報知メールを店舗管理者へ送信
     *
     * @access protected
     * @param array $arrErrors エラーレコード
     * @param string $pushTime 3Gからの送信時刻
     * @param string $pushId 識別ID
     * @return void
     */
    function sendErrorMail($arrErrors) {
        $logger =& TGMDK_Logger::getInstance();

        // 基本情報取得
        $objDB = new SC_Helper_DB_Ex();
        $arrBasis = $objDB->sfGetBasisData();

        // エラーコードを展開
        $errors = implode(LF, $arrErrors);

        // 本文
        $myName = MDL_SBIVT3G_MODULE_NAME;
        $content = <<<EOD
「$myName - 自動メール配信」で以下のエラーが発生しました。

$errors

EOD;
        $logger->debug('エラー報知メール本文 >' .LF. $content);

        // メール送信クラス生成
        $objMail = new SC_SendMail_Ex();
        $objMail->setItem(
            '', // to 別途指定
            $myName . '- 自動配信エラー報知メール', // subject
            $content, // body
            $arrBasis['email03'], // fromaddress
            $myName . '- 自動送信', // from_name
            '', // reply_to
            $arrBasis['email04'], // return_path
            $arrBasis['email04']  // errors_to
        );
        $objMail->setTo($arrBasis['email01'], $arrBasis['shop_name']);
        $objMail->sendMail();

        $logger->info('自動配信エラー報知メール送信');
    }

    /**
     * 期限前メール送信処理
     *
     * @access protected
     * @return void
     */
    function doSendNoticeMail() {
        $logger =& TGMDK_Logger::getInstance();

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 告知日を設定
        $noticeDays = $this->objSetting->get('noticeDays');
        $expression = sprintf('%s + %d day', $this->execDate, $noticeDays);
        $targetDate = date('Y/m/d', strtotime($expression));
        $logger->info(sprintf('告知対象日付: %s', $targetDate));

        // 受注情報取得
        $question = implode(',',
            array_fill(0, count($this->arrTargetPayment), '?'));
        $sql = <<<EOD
SELECT
    O.*,
    P.payment_method
FROM dtb_order O
LEFT OUTER JOIN dtb_payment P
ON P.payment_id = O.payment_id
WHERE O.del_flg <> 1
AND O.status = ?
AND O.memo01 = ?
AND O.payment_id IN (
    SELECT payment_id FROM dtb_payment
    WHERE memo01 IN ($question)
)
ORDER BY O.order_id ASC
EOD;
        $arrRows = $objQuery->getAll($sql,
            array_merge(
                array(ORDER_PAY_WAIT, MDL_SBIVT3G_STATUS_REQUEST),
                $this->arrTargetPayment
            )
        );

        $logger->info(sprintf('対象件数: [%d]件', count($arrRows)));

        foreach ($arrRows as $row) {
            $arrMail = unserialize($row['memo02']);
            $arrRes = unserialize($row['memo05']);

            // 告知日であること
            if ($arrRes['limitDate'] != $targetDate) {
                continue;
            }

            // 既に送っていれば送信しない
            if (isset($arrRes['noticeSent']) == true) {
                continue;
            }

            $objPage->tpl_header = 'ご購入いただいた注文のお支払い期限が近づいています。';
            $objPage->tpl_footer = '';
            $objPage->tpl_subject = 'お支払い期限が近づいています';
            $objPage->arrOrder = $row;
            $objPage->arrOther = $arrMail;
            $this->sfSendTemplateMail(
                $row['order_email'],
                $row['order_name01'] . ' '. $row['order_name02'] .' 様',
                $this->notice_id,
                $objPage
            );
            $logger->info(sprintf('期限前メール送信 受注番号:%d',
                $row['order_id']));
            $objPage = null;

            // 送信済みに設定
            $arrRes['noticeSent'] = true;

            // 更新
            $arrModifies = array(
                'memo05' => serialize($arrRes),
                'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
            );
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $objQuery->update('dtb_order',
                $arrModifies,
                ' order_id = ? ', array($row['order_id'])
            );
            $logger->info(sprintf('受注データ更新 受注番号:%d',
                $row['order_id']));
            continue;
        }
    }

    /**
     * 期限切れメール送信処理
     *
     * @access protected
     * @return void
     */
    function doSendExpireMail() {
        $logger =& TGMDK_Logger::getInstance();

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objMasterData = new SC_DB_MasterData_Ex();
        $objMasterData->objQuery =& $objQuery;

        // 期限日
        $logger->info(sprintf('期限対象日付: %s', $this->execDate));

        // 受注情報取得
        $question = implode(',',
            array_fill(0, count($this->arrTargetPayment), '?'));
        $sql = <<<EOD
SELECT
    O.*,
    P.payment_method
FROM dtb_order O
LEFT OUTER JOIN dtb_payment P
ON P.payment_id = O.payment_id
WHERE O.del_flg <> 1
AND O.status = ?
AND O.memo01 = ?
AND O.payment_id IN (
    SELECT payment_id FROM dtb_payment
    WHERE memo01 IN ($question)
)
ORDER BY O.order_id ASC
EOD;
        $arrRows = $objQuery->getAll($sql,
            array_merge(
                array(ORDER_PAY_WAIT, MDL_SBIVT3G_STATUS_REQUEST),
                $this->arrTargetPayment
            )
        );

        $logger->info(sprintf('対象件数: [%d]件', count($arrRows)));

        foreach ($arrRows as $row) {
            $arrMail = unserialize($row['memo02']);
            $arrRes = unserialize($row['memo05']);

            // 期限を経過していること
            if ($arrRes['limitDate'] >= $this->execDate) {
                continue;
            }

            $objPage->tpl_header = 'ご購入いただいた注文のお支払い期限が終了しました。';
            $objPage->tpl_footer = '';
            $objPage->tpl_subject = 'お支払い期限が終了しました';
            $objPage->arrOrder = $row;
            $objPage->arrOther = $arrMail;
            $this->sfSendTemplateMail(
                $row['order_email'],
                $row['order_name01'] . ' '. $row['order_name02'] .' 様',
                $this->expire_id,
                $objPage
            );
            $logger->info(sprintf('期限切れメール送信 受注番号:%d',
                $row['order_id']));
            $objPage = null;

            // 更新
            $arrModifies = array(
                'memo01' => MDL_SBIVT3G_STATUS_EXPIRED,
                'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
            );
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $objQuery->update('dtb_order',
                $arrModifies,
                ' order_id = ? ', array($row['order_id'])
            );
            $logger->info(sprintf('受注データ更新 受注番号:%d',
                $row['order_id']));
            continue;
        }
    }

    /**
     * DBに登録されたテンプレートメールの送信(カスタマイズ版)
     *
     * @access protected
     * @param string $to To:
     * @param string $to_name To:文字列
     * @param integer $template_id dtb_mailtemplate.template_id
     * @param object $objPage アサインされるオブジェクト
     * @param string $from_address From:
     * @param string $from_name From:文字列
     * @param string $reply_to ReplyTo:
     * @return void
     */
    function sfSendTemplateMail($to, $to_name, $template_id, &$objPage, $from_address = "", $from_name = "", $reply_to = "") {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objMailHelper = new SC_Helper_Mail_Ex();

        // メールテンプレート情報の取得
        $where = "template_id = ?";
        $arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($template_id));
        if (strcmp($arrRet[0]['header'], '') != 0) {
            $objPage->tpl_header = $arrRet[0]['header'];
        }
        if (strcmp($arrRet[0]['footer'], '') != 0) {
            $objPage->tpl_footer = $arrRet[0]['footer'];
        }
        if (strcmp($arrRet[0]['subject'], '') != 0) {
            $tmp_subject = $arrRet[0]['subject'];
        } else {
            $tmp_subject = $objPage->tpl_subject;
        }

        $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();

        $objMailView = new SC_SiteView_Ex();
        // メール本文の取得
        $objMailView->assignobj($objPage);
        $body = $objMailView->fetch($objMailHelper->arrMAILTPLPATH[$template_id]);

        // メール送信処理
        $objSendMail = new SC_SendMail_Ex();
        if ($from_address == "") $from_address = $arrInfo['email03'];
        if ($from_name == "") $from_name = $arrInfo['shop_name'];
        if ($reply_to == "") $reply_to = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $tosubject = $objMailHelper->sfMakeSubject($tmp_subject, $objMailView);

        $objSendMail->setItem('', $tosubject, $body, $from_address, $from_name, $reply_to, $error, $error);
        $objSendMail->setTo($to, $to_name);
        $objSendMail->setBCc($arrInfo['email03']);
        $objSendMail->sendMail();    // メール送信

        // 保存
        $objMailHelper->sfSaveMailHistory($objPage->arrOrder['order_id'],
            $template_id, $tosubject, $body);
    }
}

?>
