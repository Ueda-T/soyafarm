<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * ブランドマスタ照会 のページクラス.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_BrandSearch.php 561 2014-01-27 08:28:56Z nishi $
 */
class LC_Page_Admin_Products_BrandSearch extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/brand_search.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'brand_search';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = 'ブランド管理';
        $this->tpl_subtitle = 'ブランドマスター';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
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
        $objDb = new SC_Helper_DB_Ex();
        $objFormParam = new SC_FormParam();
        $objQuery =& SC_Query::getSingletonInstance();

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $this->arrHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getFormParamList();

        switch ($this->getMode()) {
        case 'delete':
            // ブランドデータの削除
            $this->doDelete("brand_id = ?",
                            array($objFormParam->getValue('brand_id')));
            // 削除後に検索結果を表示するためブレークはしない

        case 'search':
            $objFormParam->convParam();
            $objFormParam->trimParam();
            $this->arrErr = $this->lfCheckError($objFormParam);
            $arrParam = $objFormParam->getHashArray();

            if (count($this->arrErr) > 0) {
                break;
            }

            $where = "del_flg = 0";
            foreach ($arrParam as $key => $val) {
                if ($val == "") {
                    continue;
                }
                $this->buildQuery($key, $where, $arrval, $objFormParam);
            }

            // 行数の取得
            $this->tpl_linemax = $this->getNumberOfLines($where, $arrval);
            // ページ送りの処理
            $page_max = SC_Utils_Ex::sfGetSearchPageMax
                ($objFormParam->getValue('search_page_max'));
            // ページ送りの取得
            $objNavi = new SC_PageNavi_Ex($this->arrHidden['search_pageno'],
                                          $this->tpl_linemax, $page_max,
                                          'fnNaviSearchPage', NAVI_PMAX);
            $this->arrPagenavi = $objNavi->arrPagenavi;

            // 検索結果の取得
            $this->arrBrands = $this->findBrands
                ($where, $arrval, $page_max, $objNavi->start_row);

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
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // POSTされる値
        $objFormParam->addParam
            ("ブランドID", "brand_id", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        // 検索条件
        $objFormParam->addParam
            ("ブランドコード", "search_brand_code", STEXT_LEN, 'a',
             array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ブランド名", 'search_brand_name', STEXT_LEN, 'KVa',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ページ送り番号", "search_pageno", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam
            ("表示件数", "search_page_max", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfCheckError(&$objFormParam) {
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        return $objErr->arrErr;
    }

    /**
     * ブランドの削除
     *
     * @param string $where 削除対象の WHERE 句
     * @param array $arrParam 削除対象の値
     * @return void
     */
    function doDelete($where, $arrParam = array()) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sqlval['del_flg']     = 1;
        $sqlval['updator_id']  = $_SESSION['member_id'];
        $sqlval['update_date'] = 'now()';

        $objQuery->begin();
        $objQuery->update('dtb_brand', $sqlval, $where, $arrParam);
        $objQuery->commit();
    }

    /**
     * クエリを構築する.
     *
     * 検索条件のキーに応じた WHERE 句と, クエリパラメーターを構築する.
     * クエリパラメーターは, SC_FormParam の入力値から取得する.
     *
     * 構築内容は, 引数の $where 及び $arrValues にそれぞれ追加される.
     *
     * @param string $key 検索条件のキー
     * @param string $where 構築する WHERE 句
     * @param array $arrValues 構築するクエリパラメーター
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function buildQuery($key, &$where, &$arrValues, &$objFormParam) {
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();

        switch ($key) {
        // ブランドコード
        case 'search_brand_code':
            $where .= " AND brand_code LIKE ?";
            $arrValues[] = '%' . $objFormParam->getValue($key) . '%';
            break;

        // ブランド名
        case 'search_brand_name':
            $where .= " AND brand_name LIKE ?";
            $arrValues[] = '%' . $objFormParam->getValue($key) . '%';
            break;

        default:
            break;
        }
    }

    /**
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where, $arrValues) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->count('dtb_brand', $where, $arrValues);
    }

    /**
     * ブランドマスタを検索する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @param integer $limit 表示件数
     * @param integer $offset 開始件数
     * @return array 商品の検索結果
     */
    function findBrands($where, $arrValues, $limit, $offset) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = <<< EOS
SELECT brand_id
     , brand_name
     , brand_code
     , rank
     , DATE_FORMAT(disp_start_date, '%Y/%m/%d') AS disp_start_date
     , DATE_FORMAT(disp_end_date, '%Y/%m/%d') AS disp_end_date
     , category_id
     , product_disp_num
     , img_disp_num
     , parent_id
     , pc_comment
     , pc_free_space1
     , pc_free_space2
     , pc_free_space3
     , pc_free_space4
     , pc_free_space5
     , sp_comment
     , sp_free_space1
     , sp_free_space2
     , sp_free_space3
     , sp_free_space4
     , sp_free_space5
     , mb_comment
     , mb_free_space1
     , mb_free_space2
     , mb_free_space3
     , mb_free_space4
     , mb_free_space5
  FROM dtb_brand
 WHERE {$where}
 ORDER BY rank
 LIMIT {$offset}, {$limit}
EOS;

        return $objQuery->getAll($sql, $arrValues);
    }
}
?>
