<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * 受注管理メール確認 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Mypage_MailView.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Mypage_MailView extends LC_Page_AbstractMypage_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        $objCustomer = new SC_Customer_Ex();
        if(!SC_Utils_Ex::sfIsInt($_GET['send_id'])) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $arrMailView = $this->lfGetMailView(
            $_GET['send_id'], $objCustomer->getValue('customer_id'));

        if (empty($arrMailView)) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->tpl_subject  = $arrMailView[0]['subject'];
        $this->tpl_body     = $arrMailView[0]['mail_body'];

        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_PC){
            $this->setTemplate('mypage/mail_view.tpl');
        } else {
            $this->tpl_title    = 'メール履歴詳細';
            $this->tpl_mainpage = 'mypage/mail_view.tpl';
        }

        switch ($this->getMode()) {
        case 'getDetail':
            echo SC_Utils_Ex::jsonEncode($arrMailView);
            exit;
            break;
        default:
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

    /**
     * GETで指定された受注idのメール送信内容を返す
     *
     * @param mixed $send_id
     * @param mixed $customer_id
     * @access private
     * @return array
     */
    function lfGetMailView($send_id, $customer_id) {

        $objQuery   = SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    subject,
    mail_body
FROM
    dtb_mail_history
    LEFT JOIN dtb_order USING(order_id)
WHERE
    send_id = $send_id
    AND customer_id = $customer_id
EOF;
        return $objQuery->getAll($sql);
    }

}
