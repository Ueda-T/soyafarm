<!-- -*- coding: utf-8 -*- -->
<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * エラー表示のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Error_DispError.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Error_DispError extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     * LC_Page_Adminクラス内でエラーページを表示しようとした際に無限ループに陥るのを防ぐため,
     * ここでは, parent::init() を行わない.(フロントのエラー画面出力と同様の仕様)
     *
     * @return void
     */
    function init() {
        $this->template = LOGIN_FRAME;
        $this->tpl_mainpage = 'login_error.tpl';
        $this->tpl_title = 'ログインエラー';
        // ディスプレイクラス生成
        $this->objDisplay = new SC_Display_Ex();
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    function action(){
        switch ($this->type) {
            case LOGIN_ERROR:
                $this->tpl_error="ＩＤまたはパスワードが正しくありません。<br />もう一度ご確認のうえ、再度入力してください。";
                break;
            case ACCESS_ERROR:
                $this->tpl_error="ログイン認証の有効期限切れの可能性があります。<br />もう一度ご確認のうえ、再度ログインしてください。";
                break;
            case AUTH_ERROR:
                $this->tpl_error="このページにはアクセスできません";
                SC_Response_Ex::sendHttpStatus(403);
                break;
            case INVALID_MOVE_ERRORR:
                $this->tpl_error="不正なページ移動です。<br />もう一度ご確認のうえ、再度入力してください。";
                break;
            default:
                $this->tpl_error="エラーが発生しました。<br />もう一度ご確認のうえ、再度ログインしてください。";
                break;
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
     * エラーページではトランザクショントークンの自動検証は行わない
     */
    function doValidToken() {
        // queit.
    }
}
?>
