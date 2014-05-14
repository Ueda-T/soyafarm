<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 管理者ログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->template = MAIN_FRAME;

        //IP制限チェック
        $allow_hosts = unserialize(ADMIN_ALLOW_HOSTS);
        if (is_array($allow_hosts) && count($allow_hosts) > 0) {
            if (array_search($_SERVER["REMOTE_ADDR"],$allow_hosts) === FALSE) {
                SC_Utils_Ex::sfDispError(AUTH_ERROR);
            }
        }

        //SSL制限チェック
        if(ADMIN_FORCE_SSL == TRUE){
            if(empty($_SERVER['HTTPS']) AND $_SERVER['SERVER_PORT'] != 443){
                SC_Response_Ex::sendRedirect($SERVER["REQUEST_URI"], $_GET,FALSE, TRUE);
            }
        }

        $this->tpl_authority = $_SESSION['authority'];

        // 更新、ダウンロード、基幹連携の権限をセット
        $this->tpl_update_auth = $_SESSION['update_auth'];
        $this->tpl_csv_download_auth = $_SESSION['csv_download_auth'];
        $this->tpl_inos_auth = $_SESSION['inos_auth'];
        $this->tpl_critical_menu = $_SESSION['critical_menu'];

        // ディスプレイクラス生成
        $this->objDisplay = new SC_Display_Ex();

        // プラグインクラス生成
        $this->objPlagin = new SC_Helper_Plugin_Ex();
        $this->objPlagin->preProcess($this);

        // トランザクショントークンの検証と生成
        $this->doValidToken(true);
        $this->setTokenTo();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
    }

    /**
     * Page のレスポンス送信.
     *
     * @return void
     */
    function sendResponse() {
        if (isset($this->objPlagin)) { // FIXME モバイルエラー応急対応
            // post-prosess処理(暫定的)
            $this->objPlagin->process($this);
        }
        $this->objDisplay->prepare($this, true);
        $this->objDisplay->response->write();
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
     * ログ出力を行う.
     *
     * ログイン中の管理者IDを含めてログ出力します.
     *
     * @access protected
     * @param string $mess ログメッセージ
     * @param string $log_level ログレベル("Info" or "Debug")
     * @return void
     */
    function log($mess, $log_level) {
        $mess = $mess . " id=" . $_SESSION['login_id'] . "(" . $_SESSION['authority'] . ")" . "[" . session_id() . "]";

        GC_Utils_Ex::gfAdminLog($mess, $log_level);
    }

    /**
     * 商品コードが存在するかを取得する
     *
     * @param integer $product_id ブランドID
     * @param integer $product_code ブランドコード
     * @return integer 0:なし、1以上:あり
     */
    function lfExistsProductCode($product_id, $product_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $addWhere = '';
        if ($product_id > 0) {
            $addWhere = '   AND product_id <> ' . $product_id;
        }

        $sql =<<< __EOS
SELECT COUNT(*) AS count
  FROM dtb_products_class
 WHERE del_flg = 0
   AND product_code = '{$product_code}'
{$addWhere}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0]['count'];
    }
}
?>
