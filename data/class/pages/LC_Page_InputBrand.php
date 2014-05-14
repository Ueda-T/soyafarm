<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * ブランドコード入力 のページクラス.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_InputBrand.php 1472 2014-04-18 08:07:28Z moriuchi $
 */
class LC_Page_InputBrand extends LC_Page_Ex
{
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_message = "ブランドを検索しています。";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $results = array();

        $objView = new SC_SiteView_Ex(false);

        // 入力エラーチェック
        $arrErr = $this->fnErrorCheck($_GET);

        // 入力エラーの場合は終了
        if (count($arrErr) > 0) {
            $results["brand_name"] = "";
            foreach ($arrErr as $key => $val) {
                $results["brand_name"] .= preg_replace("/<br \/>/", "\n", $val);
            }
            echo json_encode($results);
            exit;
        }

        // ブランドデータを取得する
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$brandCode = addslashes($_GET['brand_code']);
        $sql =<<< __EOS
SELECT brand_id
     , brand_name
  FROM dtb_brand
 WHERE del_flg = 0
   AND brand_code = '{$brandCode}'
__EOS;
        $r = $objQuery->getAll($sql);
        if (count($r) == 0) {
            $results["brand_name"] = "該当するブランドが見つかりません。";
            echo json_encode($results);
            exit;
        }

        $results = $r[0];
        echo json_encode($results);
        exit;
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
     * 入力エラーのチェック.
     *
     * @param array $arrRequest リクエスト値($_GET)
     * @return array $arrErr エラーメッセージ配列
     */
    function fnErrorCheck($arrRequest) {
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();

        // パラメーター情報の初期化
        $objFormParam->addParam
            ('ブランドコード', 'brand_code', BRAND_CODE_LEN, 'a',
             array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));

        // リクエスト値をセット
        $objFormParam->setParam($arrRequest);

        // エラーチェック
        $arrErr = $objFormParam->checkError();

        return $arrErr;
    }
}
?>
