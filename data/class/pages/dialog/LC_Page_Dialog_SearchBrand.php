<?php

// {{{ requires

/**
 * ブランド検索ダイアログのページクラス.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Dialog_SearchBrand.php 369 2014-01-10 06:27:02Z kaji $
 */
class LC_Page_Dialog_SearchBrand
{
    var $template;
    var $arrForm;

    var $resultCount;
    var $arrResultList;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $resultCount = 0;
        $arrResultList = array();
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

        switch ($objFormParam->getValue('mode')) {
        case 'search':
            $this->template = "dialog/search_brand_result.tpl";
            $this->arrResultList = $this->search($this->arrForm);
            $this->resultCount = count($this->arrResultList);
            break;

        default:
            $this->template = "dialog/search_brand.tpl";
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
            ("ブランドコード(条件)", "brandCode", STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ブランド名(条件)", "brandName", STEXT_LEN, '',
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
SELECT bd.brand_code
     , bd.brand_name
  FROM dtb_brand bd
 WHERE bd.del_flg = 0
%s
%s
 ORDER BY bd.rank
__EOS;

        $cond1 = '';
        if (!empty($arrForm["brandCode"])) {
            $cond1 =<<< __EOS
   AND bd.brand_code LIKE '%{$arrForm["brandCode"]}%'
__EOS;
        }
        $cond2 = '';
        if (!empty($arrForm["brandName"])) {
            $cond2 =<<< __EOS
   AND bd.brand_name LIKE '%{$arrForm["brandName"]}%'
__EOS;
        }
        $sql = sprintf($fmt, $cond1, $cond2);

        $results = $objQuery->getAll($sql);

        return $results;
    }
}
?>
