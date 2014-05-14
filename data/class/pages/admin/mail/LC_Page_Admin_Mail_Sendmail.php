<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * メールマガジンの予約配信用ページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id: LC_Page_Admin_Mail_Sendmail.php 122 2012-06-11 07:04:36Z hira $
 */
class LC_Page_Admin_Mail_Sendmail extends LC_Page_Admin_Ex {
    
	var $objMail;
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
    	 // SC_SendMailの拡張
	    if(file_exists(MODULE_PATH . "mdl_speedmail/SC_SpeedMail.php")) {
	        require_once(MODULE_PATH . "mdl_speedmail/SC_SpeedMail.php");
	        // SpeedMail対応
	        $this->objMail = new SC_SpeedMail();
	    } else {
	        $this->objMail = new SC_SendMail_Ex();
	    }
    	
        // SSL強制時のリダイレクトを防止する
        //parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
		$objQuery =& SC_Query_Ex::getSingletonInstance();

        //----　未送信データを取得する
        $sql =<<<EOF
SELECT
    send_id
FROM
    dtb_send_history
WHERE
    complete_count = 0
    AND del_flg = 0
    AND end_date IS NULL
    AND start_date <= now()
ORDER BY
    send_id ASC,
    start_date ASC
EOF;
        $time_data = $objQuery->getAll($sql);

        //未送信メルマガの数
        $count = count($time_data);

        //未送信メルマガがあれば送信処理を続ける。なければ中断する。
        if( $count > 0 ){
            $msg = __CLASS__."::".__FUNCTION__."() start sending.";
            GC_Utils_Ex::gfPrintLog($msg);
        } else {
            $msg = __CLASS__."::".__FUNCTION__."() not fount.";
            GC_Utils_Ex::gfPrintLog($msg);
            exit;
        }

        //---- メール送信準備
        for( $i = 0; $i < $count; $i++ ) {
			// DB登録・送信
			SC_Helper_Mail_Ex::sfSendMailmagazine($time_data[$i]["send_id"]);
        }

        $msg = __CLASS__."::".__FUNCTION__."() complete.";
        GC_Utils_Ex::gfPrintLog($msg);
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
