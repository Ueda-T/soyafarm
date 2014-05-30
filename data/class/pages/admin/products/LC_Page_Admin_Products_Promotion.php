<?php
// {{{ requires
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');

/**
 * 企画マスタ登録 のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_Promotion.php 1000 2014-02-24 10:27:05Z moriuchi $
 */
class LC_Page_Admin_Products_Promotion extends LC_Page_Admin_Ex
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
        $this->tpl_mainpage = 'products/promotion.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'promotion_search';
        $this->tpl_maintitle = 'プロモーション管理';
        $this->tpl_subtitle = 'プロモーション詳細';

        $this->arrPromotionKbn = array(1 => '値引', 2 => '送料', 3 => '同梱品');
        $this->arrValidKbn = array(0 => '無効', 1 => '有効');
        $this->arrQuantityKbn = array(1 => '明細ずつ', 2 => '全明細');
        $this->arrCourseKbn = array(0 => '単品', 1 => '定期', 9 => '全体');
        $this->arrDelivFeeKbn = array(0 => '有料', 1 => '無料');

        //$this->arrOrderKbnMst = array(1 => '電話', 2 => 'FAX', 3 => 'WEB');
        $this->arrOrderKbnMst = $this->lfMakeDeviceType();

        $this->arrMedia = array();
        $this->arrOrderKbn = array();
        $this->arrOrderProduct = array();
        $this->arrDiscountProduct = array();
        $this->arrIncludeProduct = array();
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
        $objFormParam = new SC_FormParam_Ex();

        // パラメーター初期化, 取得
        $this->lfInitFormParam($objFormParam, $_POST);
        $this->arrForm = $objFormParam->getHashArray();

        // 検索パラメーター引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();

        if ($this->arrForm['promotion_cd'] != "") {
            $this->arrForm =
                $this->lfGetPromotion($this->arrForm['promotion_cd']);
            $this->arrMedia =
                $this->lfGetMedia($this->arrForm['promotion_cd']);
            $this->arrOrderKbn =
                $this->lfGetOrderKbn($this->arrForm['promotion_cd']);
            $this->arrOrderProduct =
                $this->lfGetOrderProduct($this->arrForm['promotion_cd']);
            $this->arrDiscountProduct =
                $this->lfGetDiscountProduct($this->arrForm['promotion_cd']);
            $this->arrIncludeProduct =
                $this->lfGetIncludeProduct($this->arrForm['promotion_cd']);
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
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return void
     */
    function lfInitFormParam(&$objFormParam, $arrPost) {
        // POSTされる値
        $objFormParam->addParam
            ("プロモーションコード", "promotion_cd", STEXT_LEN, 'a',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("プロモーション名", "promotion_name", STEXT_LEN, 'a',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("プロモーション区分", 'promotion_kbn', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("有効区分", 'valid_kbn', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("有効期間From", 'valid_from', STEXT_LEN, 'a',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("有効期間To", 'valid_to', STEXT_LEN, 'a',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("数量From", "quantity_from", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("数量To", "quantity_to", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("数量集計区分", "quantity_kbn", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("金額From", "amount_from", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("金額To", "amount_to", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("コース区分", "course_kbn", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("回数From", "count_from", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("回数To", "count_to", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("送料区分", "deliv_fee_kbn", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("適用回数", "use_count", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));

        // 検索条件
        $objFormParam->addParam
            ("プロモーション区分", "search_promotion_kbn", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("有効期間(FROM)", "search_valid_from", STEXT_LEN, 'a',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("有効期間(TO)", "search_valid_to", STEXT_LEN, 'a',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ページ送り番号", "search_pageno", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam
            ("表示件数", "search_page_max", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * DBからプロモーションマスタを取得する
     * 
     * @param string $promotion_cd プロモーションコード
     * @return array プロモーションマスタ
     */
    function lfGetPromotion($promotion_cd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT promotion_cd
     , promotion_name
     , promotion_kbn
     , valid_kbn
     , DATE_FORMAT(valid_from, '%Y/%m/%d') AS valid_from
     , DATE_FORMAT(valid_to,   '%Y/%m/%d') AS valid_to
     , quantity_from
     , quantity_to
     , quantity_kbn
     , amount_from
     , amount_to
     , course_kbn
     , count_from
     , count_to
     , deliv_fee_kbn
     , use_count
  FROM dtb_promotion
 WHERE del_flg = 0
   AND promotion_cd = '{$promotion_cd}'
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0];
    }

    /**
     * DBからプロモーションイベントマスタを取得する
     * 
     * @param string $promotion_cd プロモーションコード
     * @return array プロモーションイベントマスタ
     */
    function lfGetMedia($promotion_cd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT pm.media_code
     , CONCAT(pm.media_code, '　', IFNULL(m.media_name, '')) AS value
  FROM dtb_promotion_media pm
  LEFT JOIN dtb_media m
    ON pm.media_code = m.media_code
   AND m.del_flg = 0
 WHERE pm.del_flg = 0
   AND pm.promotion_cd = '{$promotion_cd}'
 ORDER BY pm.media_code
__EOS;

        $results = $objQuery->getAll($sql);

        $values = array();
        for ($i = 0; $i < count($results); ++$i) {
            $values[$results[$i]['media_code']] = $results[$i]['value'];
        }

        return $values;
    }

    /**
     * DBからプロモーション受注区分マスタを取得する
     * 
     * @param string $promotion_cd プロモーションコード
     * @return array プロモーション受注区分マスタ
     */
    function lfGetOrderKbn($promotion_cd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT order_kbn
  FROM dtb_promotion_order_kbn
 WHERE del_flg = 0
   AND promotion_cd = '{$promotion_cd}'
 ORDER BY order_kbn
__EOS;

        $results = $objQuery->getAll($sql);

        $values = array();
        for ($i = 0; $i < count($results); ++$i) {
            $key = $results[$i]['order_kbn'];
            $value = '';

            if (isset($this->arrOrderKbnMst[$key])) {
                $value = sprintf("%02d　%s", $key, $this->arrOrderKbnMst[$key]);
            } else {
                $value = sprintf("%02d　", $key);
            }

            $values[$key] = $value;
        }

        return $values;
    }

    /**
     * DBからプロモーション購入商品マスタを取得する
     * 
     * @param string $promotion_cd プロモーションコード
     * @return array プロモーション購入商品マスタ
     */
    function lfGetOrderProduct($promotion_cd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT op.product_cd
     , CONCAT(op.product_cd, '　', IFNULL(p.name, '')) AS value
  FROM dtb_promotion_order_product op
  LEFT JOIN dtb_products_class pc
    ON op.product_cd = pc.product_code
   AND pc.del_flg = 0
  LEFT JOIN dtb_products p
    ON pc.product_id = p.product_id
   AND p.del_flg = 0
 WHERE op.del_flg = 0
   AND op.promotion_cd = '{$promotion_cd}'
 ORDER BY op.product_cd
__EOS;

        $results = $objQuery->getAll($sql);

        $values = array();
        for ($i = 0; $i < count($results); ++$i) {
            $values[$results[$i]['product_cd']] = $results[$i]['value'];
        }

        return $values;
    }

    /**
     * DBからプロモーション値引商品マスタを取得する
     * 
     * @param string $promotion_cd プロモーションコード
     * @return array プロモーション値引商品マスタ
     */
    function lfGetDiscountProduct($promotion_cd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT dp.product_cd
     , CONCAT(dp.product_cd, '　',
              IFNULL(p.name, ''), '　',
              FORMAT(dp.sales_price, 0), '円', '　値引率',
              FORMAT(dp.cut_rate, 0), '%') AS value
  FROM dtb_promotion_discount_product dp
  LEFT JOIN dtb_products_class pc
    ON dp.product_cd = pc.product_code
   AND pc.del_flg = 0
  LEFT JOIN dtb_products p
    ON pc.product_id = p.product_id
   AND p.del_flg = 0
 WHERE dp.del_flg = 0
   AND dp.promotion_cd = '{$promotion_cd}'
 ORDER BY dp.product_cd
__EOS;

        $results = $objQuery->getAll($sql);

        $values = array();
        for ($i = 0; $i < count($results); ++$i) {
            $values[$results[$i]['product_cd']] = $results[$i]['value'];
        }

        return $values;
    }

    /**
     * DBからプロモーション同梱商品マスタを取得する
     * 
     * @param string $promotion_cd プロモーションコード
     * @return array プロモーション同梱商品マスタ
     */
    function lfGetIncludeProduct($promotion_cd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT ip.product_cd
     , CONCAT(ip.product_cd, '　',
              IFNULL(p.name, ''), '　',
              FORMAT(ip.quantity, 0), '個') AS value
  FROM dtb_promotion_include_product ip
  LEFT JOIN dtb_products_class pc
    ON ip.product_cd = pc.product_code
   AND pc.del_flg = 0
  LEFT JOIN dtb_products p
    ON pc.product_id = p.product_id
   AND p.del_flg = 0
 WHERE ip.del_flg = 0
   AND ip.promotion_cd = '{$promotion_cd}'
 ORDER BY ip.product_cd
__EOS;

        $results = $objQuery->getAll($sql);

        $values = array();
        for ($i = 0; $i < count($results); ++$i) {
            $values[$results[$i]['product_cd']] = $results[$i]['value'];
        }

        return $values;
    }

    /**
     * DBからマスタ用配列を生成する
     * 
     * @param none
     * @return array デバイスタイプマスタ
     */
    function lfMakeDeviceType() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT kikan_id
     , name
FROM mtb_device_type
ORDER BY rank
__EOS;

        $results = $objQuery->getAll($sql);

        $arrMst = array();
        for ($i = 0; $i < count($results); $i++) {
            $arrMst[$results[$i]["kikan_id"]] = $results[$i]["name"];
        }

        return $arrMst;
    }

}
?>
