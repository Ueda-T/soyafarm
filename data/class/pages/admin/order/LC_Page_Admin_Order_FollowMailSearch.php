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
 * フォローメール照会 のページクラス.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Order_FollowMailSearch.php 440 2014-01-16 02:49:43Z taizo $
 */
class LC_Page_Admin_Order_FollowMailSearch extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_mainpage = 'order/follow_mail_search.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'follow_mail_search';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = 'フォローメール管理';
        $this->tpl_subtitle = 'フォローメール情報';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
        $this->arrFollowMailStatus = array(1 => '有効', 0 => '停止');
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
            // フォローメールデータの削除
            $this->doDelete("follow_id = ?",
                            array($objFormParam->getValue('follow_id')));
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
            $this->arrFollowMails = $this->findFollowMails
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
        $objFormParam->addParam("フォローメールID", "follow_id",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        // 検索条件
        $objFormParam->addParam("フォローメール名", "search_follow_name",
                                40, 'n',
                                array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("送信日設定", "search_send_term",
                                4, 'n',
                                array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ページ送り番号", "search_pageno",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("表示件数", "search_page_max",
                                INT_LEN, 'n',
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
     * フォローメールの削除
     *
     * @param string $where 削除対象の WHERE 句
     * @param array $arrParam 削除対象の値
     * @return void
     */
    function doDelete($where, $arrParam = array()) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sqlval['del_flg']     = 1;
        $sqlval['updator_id']   = $_SESSION['member_id'];
        $sqlval['update_date'] = 'now()';

        $objQuery->begin();
        $objQuery->update('dtb_follow_mail', $sqlval, $where, $arrParam);
        $objQuery->commit();

        // フォローメール商品データの削除
        $objQuery->begin();
        $objQuery->update('dtb_follow_mail_products', $sqlval, $where, $arrParam);
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
        // フォローメール名
        case 'search_follow_name':
            if ($objFormParam->getValue($key) != "") {
                $where .= " AND follow_name LIKE ?";
                $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
            }
            break;
        // 送信日設定
        case 'search_send_term':
            if ($objFormParam->getValue($key) != "") {
                $where .= " AND send_term =  ?";
                $arrValues[] = $objFormParam->getValue($key);
            }
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
        return $objQuery->count('dtb_follow_mail', $where, $arrValues);
    }

    /**
     * フォローメールマスタを検索する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @param integer $limit 表示件数
     * @param integer $offset 開始件数
     * @return array 商品の検索結果
     */
    function findFollowMails($where, $arrValues, $limit, $offset) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = <<< EOS
SELECT *
  FROM dtb_follow_mail
 WHERE {$where}
 ORDER BY follow_code
 LIMIT {$offset}, {$limit}
EOS;

        return $objQuery->getAll($sql, $arrValues);
    }
}
?>
