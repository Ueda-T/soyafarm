<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/*
 * 受注管理メール確認 のページクラス.
 */
class LC_Page_Admin_Order_MailView extends LC_Page_Admin_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/mail_view.tpl';
        $this->httpCacheControl('nocache');
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
        $send_id = $_GET['send_id'];
        if (SC_Utils_Ex::sfIsInt($send_id)) {
	    if ($_GET['type'] == 1) {
		$mailHistory = $this->getMailHistory($send_id);
	    } else {
		$mailHistory = $this->getFollowMailHistory($send_id);
	    }
            $this->tpl_subject = $mailHistory[0]['subject'];
            $this->tpl_body = $mailHistory[0]['mail_body'];
        }
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * 
     * メールの履歴を取り出す。
     * @param int $send_id
     */
    function getMailHistory($send_id){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
select
    subject
   ,mail_body
from
    dtb_mail_history
where
    send_id = $send_id
__EOS;

        return $objQuery->getAll($sql);
    }

    /**
     * 
     * メールの履歴を取り出す。
     * @param int $send_id
     */
    function getFollowMailHistory($send_id){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
select
    subject
   ,body as mail_body
from
    dtb_follow_mail_history
where
    send_id = $send_id
__EOS;

        return $objQuery->getAll($sql);
    }
}
?>
