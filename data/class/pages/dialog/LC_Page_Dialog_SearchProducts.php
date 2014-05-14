<?php

// {{{ requires

/**
 * 商品検索ダイアログのページクラス.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Dialog_SearchProducts.php 408 2014-01-14 09:51:22Z kaji $
 */
class LC_Page_Dialog_SearchProducts
{
    var $template;
    var $arrForm;
    var $isSingleSelect;

    var $resultCount;
    var $arrResultList;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->isSingleSelect = "0";
        $this->resultCount = 0;
        $this->arrResultList = array();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        ;
    }

    /**
     * テンプレート名を返す
     */
    function getTemplate() {
        return $this->template;
    }

    /**
     * Page のプロセス.
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $this->arrForm = $objFormParam->getHashArray();

        // 結果選択モードを設定する
        $ss = $objFormParam->getValue('isSingleSelect');
        if (!empty($ss)) {
            $this->isSingleSelect = $ss;
        }

        switch ($objFormParam->getValue('mode')) {
        case 'search':
            $this->template = "dialog/search_products_result.tpl";
            $this->arrResultList = $this->search($this->arrForm);
            $this->resultCount = count($this->arrResultList);
            break;

        default:
            $this->template = "dialog/search_products.tpl";
            break;
        }
    }

    /**
     * Page のレスポンス送信.
     *
     * @return void
     */
    function sendResponse() {
        $display = new SC_Display_Ex();
        $display->prepare($this);
        echo $display->response->body;
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // POSTされる値
        $objFormParam->addParam
            ("モード", "mode", STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ダイアログID", "dialogId", STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("選択モード", "isSingleSelect", STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("商品コード(条件)", "productCode", STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("商品名(条件)", "productName", STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * 検索処理
     *
     * @param arrForm フォーム配列
     * @return 検索結果の配列
     */
    function search($arrForm) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $fmt =<<< __EOS
SELECT pc.product_code
     , pd.name AS product_name
     , cg.name AS product_class_name
  FROM dtb_products_class pc
 INNER JOIN dtb_products pd
    ON pc.product_id = pd.product_id
   AND pd.del_flg = 0
  LEFT JOIN dtb_class_combination cc
    ON pc.class_combination_id = cc.class_combination_id
  LEFT JOIN dtb_classcategory cg
    ON cc.classcategory_id = cg.classcategory_id
   AND cg.del_flg = 0
 WHERE pc.del_flg = 0
%s
%s
 ORDER BY pc.product_code
        , pd.product_id
__EOS;

        $cond1 = '';
        if (!empty($arrForm["productCode"])) {
            $cond1 =<<< __EOS
   AND pc.product_code LIKE '%{$arrForm["productCode"]}%'
__EOS;
        }
        $cond2 = '';
        if (!empty($arrForm["productName"])) {
            $cond2 =<<< __EOS
   AND pd.name LIKE '%{$arrForm["productName"]}%'
__EOS;
        }
        $sql = sprintf($fmt, $cond1, $cond2);

        $results = $objQuery->getAll($sql);

        return $results;
    }
}
?>
