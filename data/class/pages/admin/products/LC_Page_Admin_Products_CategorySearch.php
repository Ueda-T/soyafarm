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
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * カテゴリ管理 のページクラス.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_CategorySearch.php 167 2013-12-19 01:07:03Z kaji $
 */
class LC_Page_Admin_Products_CategorySearch extends LC_Page_Admin_Ex {

    // {{{ properties

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'カテゴリー登録';
        $this->tpl_mainpage = 'products/category_search.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno  = 'category_search';
        $this->tpl_onload = " fnSetFocus('category_name'); ";
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
        $objDb      = new SC_Helper_DB_Ex();
        $objFormParam = new SC_FormParam_Ex();

        // 入力パラメーター初期化
        $this->initParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch($this->getMode()) {
        // カテゴリ削除
        case 'delete':
            $this->doDelete($objFormParam, $objDb);
            break;

        // 表示順を上へ
        case 'up':
            $this->doUp($objFormParam);
            break;

        // 表示順を下へ
        case 'down':
            $this->doDown($objFormParam);
            break;

        // カテゴリツリークリック時
        case 'tree':
            break;

         // CSVダウンロード
        case 'csv':
            // CSVを送信する
            $objCSV = new SC_Helper_CSV_Ex();
            $objCSV->sfDownloadCsv("5", "", array(), "", true);
            exit;
            break;

        default:
            break;
        }

        $parent_category_id = $objFormParam->getValue('parent_category_id');
        // 空の場合は親カテゴリを0にする
        if (empty($parent_category_id)) {
            $parent_category_id = 0;
        }
        // 親カテゴリIDの保持
        $this->arrForm['parent_category_id'] = $parent_category_id;
        // カテゴリ一覧を取得
        $this->arrList =
            $this->findCategoiesByParentCategoryId($parent_category_id);
        // カテゴリツリーを取得
        $this->arrTree = $objDb->sfGetCatTree($parent_category_id);
        // ぱんくずの生成
        $arrBread = array();
        $objDb->findTree($this->arrTree, $parent_category_id, $arrBread);
        $this->tpl_bread_crumbs = SC_Utils_Ex::jsonEncode($arrBread);
    }

    /**
     * カテゴリの削除を実行する.
     *
     * 下記の場合は削除を実施せず、エラーメッセージを表示する.
     *
     * - 削除対象のカテゴリに、子カテゴリが1つ以上ある場合
     * - 削除対象のカテゴリを、登録商品が使用している場合
     *
     * カテゴリの削除は、物理削除で行う.
     *
     * @param SC_FormParam $objFormParam
     * @param SC_Helper_Db $objDb
     * @return void
     */
    function doDelete(&$objFormParam, &$objDb) {
        $category_id = $objFormParam->getValue('category_id');
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 子カテゴリのチェック
        $where = "parent_category_id = ? AND del_flg = 0";
        $count = $objQuery->count("dtb_category", $where, array($category_id));
        if ($count > 0) {
             $this->arrErr['category_name'] = "※ 子カテゴリが存在するため削除できません。<br/>";
             return;
        }
        // 登録商品のチェック
        $table = "dtb_product_categories AS T1 LEFT JOIN dtb_products AS T2 ON T1.product_id = T2.product_id";
        $where = "T1.category_id = ? AND T2.del_flg = 0";
        $count = $objQuery->count($table, $where, array($category_id));
        if ($count > 0) {
            $this->arrErr['category_name'] = "※ カテゴリ内に商品が存在するため削除できません。<br/>";
            return;
        }

        // ランク付きレコードの削除(※処理負荷を考慮してレコードごと削除する。)
        $objDb->sfDeleteRankRecord("dtb_category", "category_id", $category_id, "", true);
    }

    /**
     * カテゴリの表示順序を上へ移動する.
     *
     * @param SC_FormParam $objFormParam
     * @return void
     */
    function doUp(&$objFormParam) {
        $category_id = $objFormParam->getValue('category_id');

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $up_id = $this->lfGetUpRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
        if ($up_id != "") {
            // 上のグループのrankから減算する数
            $my_count = $this->lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
                // 自分のグループのrankに加算する数
                $up_count = $this->lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id);
                if ($my_count > 0 && $up_count > 0) {
                    // 自分のグループに加算
                    $this->lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id, $up_count);
                    // 上のグループから減算
                    $this->lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id, $my_count);
                }
        }
        $objQuery->commit();
    }

    /**
     * カテゴリの表示順序を下へ移動する.
     *
     * @param SC_FormParam $objFormParam
     * @return void
     */
    function doDown(&$objFormParam) {
        $category_id = $objFormParam->getValue('category_id');

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $down_id = $this->lfGetDownRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
        if ($down_id != "") {
            // 下のグループのrankに加算する数
            $my_count = $this->lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
            // 自分のグループのrankから減算する数
            $down_count = $this->lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id);
            if ($my_count > 0 && $down_count > 0) {
                // 自分のグループから減算
                $this->lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id, $my_count);
                // 下のグループに加算
                $this->lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id, $down_count);
            }
        }
        $objQuery->commit();
    }

    /**
     * パラメーターの初期化を行う
     *
     * @param SC_FormParam $objFormParam
     * @return void
     */
    function initParam(&$objFormParam) {
        $objFormParam->addParam("親カテゴリID", "parent_category_id", null, null, array());
        $objFormParam->addParam("カテゴリID", "category_id", null, null, array());
        $objFormParam->addParam("カテゴリ名", "category_name", STEXT_LEN, 'KVa', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カテゴリコード", "category_code", STEXT_LEN, 'a', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * 親カテゴリIDでカテゴリを検索する.
     *
     * - 表示順の降順でソートする
     * - 有効なカテゴリを返す(del_flag = 0)
     *
     * @param SC_Query $objQuery
     * @param int $parent_category_id 親カテゴリID
     * @return array カテゴリの配列
     */
    function findCategoiesByParentCategoryId($parent_category_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        if (!$parent_category_id) {
            $parent_category_id = 0;
        }

        $sql =<<< __EOS
SELECT category_id
     , category_code
     , category_name
     , level
     , rank
  FROM dtb_category
 WHERE del_flg = 0
   AND parent_category_id = {$parent_category_id}
 ORDER BY rank DESC
__EOS;

        return $objQuery->getAll($sql);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // 並びが1つ下のIDを取得する。
    function lfGetDownRankID($objQuery, $table, $pid_name, $id_name, $id) {
        // 親IDを取得する。
        $col = "$pid_name";
        $where = "$id_name = ?";
        $pid = $objQuery->get($col, $table, $where, $id);
        // すべての子を取得する。
        $col = "$id_name";
        $where = "del_flg = 0 AND $pid_name = ? ORDER BY rank DESC";
        $arrRet = $objQuery->select($col, $table, $where, array($pid));
        $max = count($arrRet);
        $down_id = "";
        for($cnt = 0; $cnt < $max; $cnt++) {
            if($arrRet[$cnt][$id_name] == $id) {
                $down_id = $arrRet[($cnt + 1)][$id_name];
                break;
            }
        }
        return $down_id;
    }

    // 並びが1つ上のIDを取得する。
    function lfGetUpRankID($objQuery, $table, $pid_name, $id_name, $id) {
        // 親IDを取得する。
        $col = "$pid_name";
        $where = "$id_name = ?";
        $pid = $objQuery->get($col, $table, $where, $id);
        // すべての子を取得する。
        $col = "$id_name";
        $where = "del_flg = 0 AND $pid_name = ? ORDER BY rank DESC";
        $arrRet = $objQuery->select($col, $table, $where, array($pid));
        $max = count($arrRet);
        $up_id = "";
        for($cnt = 0; $cnt < $max; $cnt++) {
            if($arrRet[$cnt][$id_name] == $id) {
                $up_id = $arrRet[($cnt - 1)][$id_name];
                break;
            }
        }
        return $up_id;
    }

    function lfCountChilds($objQuery, $table, $pid_name, $id_name, $id) {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        return count($arrRet);
    }

    function lfUpRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count) {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        $line = SC_Utils_Ex::sfGetCommaList($arrRet);
        $sql = "UPDATE $table SET rank = (rank + $count) WHERE $id_name IN ($line) ";
        $sql.= "AND del_flg = 0";
        $ret = $objQuery->exec($sql);
        return $ret;
    }

    function lfDownRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count) {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        $line = SC_Utils_Ex::sfGetCommaList($arrRet);
        $sql = "UPDATE $table SET rank = (rank - $count) WHERE $id_name IN ($line) ";
        $sql.= "AND del_flg = 0";
        $ret = $objQuery->exec($sql);
        return $ret;
    }
}
