<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * SEO管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Seo.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Basis_Seo extends LC_Page_Admin_Ex {

    // {{{ properties

    /** エラー情報 */
    var $arrErr;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/seo.tpl';
        $this->tpl_subno = 'seo';
        $this->tpl_mainno = 'basis';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = 'SEO管理';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData("mtb_taxrule");
        $this->arrDeviceTypeName[DEVICE_TYPE_PC] = 'PCサイト';
        $this->arrDeviceTypeName[DEVICE_TYPE_MOBILE] = 'モバイルサイト';
        $this->arrDeviceTypeName[DEVICE_TYPE_SMARTPHONE] = 'スマートフォン';
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
        // データの取得
        $this->arrPageData = $this->lfGetSeoPageData();

        $mode = $this->getMode();

        if (!empty($_POST)) {
            $objFormParam = new SC_FormParam_Ex();
            $this->lfInitParam($mode, $objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            $this->arrErr = $objFormParam->checkError();
            $post = $objFormParam->getHashArray();
        }
        $device_type_id = (isset($post['device_type_id'])) ? $post['device_type_id'] : '';
        $page_id = (isset($post['page_id'])) ? $post['page_id'] : '';

        switch ($mode) {
        case 'confirm':
            $objFormParam->setParam($_POST['meta'][$device_type_id][$page_id]);
            $this->arrErr[$device_type_id][$page_id] = $objFormParam->checkError();

            // エラーがなければデータを更新
            if(count($this->arrErr[$device_type_id][$page_id]) == 0) {
                $arrMETA = $objFormParam->getHashArray();

                // 更新データ配列生成
                $arrUpdData = array($arrMETA['author'], $arrMETA['description'], $arrMETA['keyword'], $device_type_id, $page_id);
                // データ更新
                $this->lfUpdPageData($arrUpdData);
            }else{
                // POSTのデータを再表示
                $arrPageData = $this->lfSetData($this->arrPageData, $_POST['meta']);
                $this->arrPageData = $arrPageData;
            }
            break;
        default:
            break;
        }

        // エラーがなければデータの取得
        if(count($this->arrErr[$device_type_id][$page_id]) == 0) {
            // データの取得
            $this->arrPageData = $this->lfGetSeoPageData();
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
     * ページレイアウトテーブルにデータ更新を行う.
     *
     * @param array $arrUpdData 更新データ
     * @return integer 更新結果
     */
    function lfUpdPageData($arrUpdData = array()){
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
UPDATE
    dtb_pagelayout
SET
    author = ?,
    description = ?,
    keyword = ?
WHERE
    device_type_id = ?
    AND page_id = ?
EOF;

        // SQL実行
        $ret = $objQuery->query($sql, $arrUpdData);

        return $ret;
    }

    function lfInitParam($mode, &$objFormParam) {
        $objFormParam->addParam('デバイスID', 'device_type_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ページID', 'page_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メタタグ:Author', 'author', STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        // 2011.05.10 文字数制限変更
        $objFormParam->addParam('メタタグ:Description', 'description', MTEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam('メタタグ:Keywords', 'keyword', STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
    }

    /**
     * テンプレート表示データに値をセットする.
     *
     * @param array 表示元データ
     * @param array 表示データ
     * @return array 表示データ
     */
    function lfSetData($arrPageData, $arrDispData){

        foreach($arrPageData as $device_key => $arrVal){
            foreach($arrVal as $key => $val) {
                $device_type_id = $val['device_type_id'];
                $page_id = $val['page_id'];
                $arrPageData[$device_key][$key]['author'] = $arrDispData[$device_type_id][$page_id]['author'];
                $arrPageData[$device_key][$key]['description'] = $arrDispData[$device_type_id][$page_id]['description'];
                $arrPageData[$device_key][$key]['keyword'] = $arrDispData[$device_type_id][$page_id]['keyword'];
            }
        }

        return $arrPageData;
    }

    /**
     * SEO管理で設定するページのデータを取得する
     *
     * @param void
     * @return array $arrRet ページデータ($arrRet[デバイスタイプID])
     */
    function lfGetSeoPageData() {
        $objLayout = new SC_Helper_PageLayout_Ex();

        return array(
            DEVICE_TYPE_PC          => $objLayout->getPageProperties(DEVICE_TYPE_PC, null, 'edit_flg = ?', array('2')),
            DEVICE_TYPE_MOBILE      => $objLayout->getPageProperties(DEVICE_TYPE_MOBILE, null, 'edit_flg = ?', array('2')),
            DEVICE_TYPE_SMARTPHONE  => $objLayout->getPageProperties(DEVICE_TYPE_SMARTPHONE, null, 'edit_flg = ?', array('2')),
        );
    }
}
?>
