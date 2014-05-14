<?php
/**
 * 会員検索用クラス
 *
 * @author IQUEVE CO.,LTD.
 * @version $Id: SC_CustomerList.php 108 2012-04-24 03:33:44Z hira $
 */
class SC_CustomerList extends SC_SelectSql_Ex {

    var $arrColumnCSV;

    function SC_CustomerList($array, $mode = '') {
        parent::SC_SelectSql($array);

        $masterData = new SC_DB_MasterData_Ex();
        $objDb = new SC_Helper_DB_Ex();
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();

	//print_r($array);
        if ($mode == "") {
            // 会員本登録会員で削除していない会員
            $this->setWhere("status = 2 AND del_flg = 0 ");
            // 登録日を示すカラム
            $regdate_col = 'dtb_customer.update_date';
        } else if ($mode == 'customer') {
            // 登録日を示すカラム
            $regdate_col = 'dtb_customer.update_date';
        }

	// 退会者を含める
        if (!strlen($this->arrSql['search_withdrawal'])) {
	    $this->arrSql['search_withdrawal'] = "";
	    $this->setWhere("del_flg = 0");
	}

        // 顧客ID
        if (!isset($this->arrSql['search_customer_id']))
	    $this->arrSql['search_customer_id'] = "";

        if ( strlen($this->arrSql['search_customer_id']) > 0) {
            $this->setWhere("customer_id = ?");
            $this->arrVal[] = $this->arrSql['search_customer_id'];
        }

        // 顧客コード(基幹)
        if (!isset($this->arrSql['search_customer_cd']))
	    $this->arrSql['search_customer_cd'] = "";

        if (strlen($this->arrSql['search_customer_cd']) > 0) {
            $this->setWhere("customer_cd = ?");
            $this->arrVal[] = $this->arrSql['search_customer_cd'];
        }

        // 名前
        if (!isset($this->arrSql['search_name']))
	    $this->arrSql['search_name'] = "";

        if ( strlen($this->arrSql['search_name']) > 0 ) {
            $this->setWhere("replace(replace(name, ' ', ''), '　', '') LIKE ?");
            $searchName = $this->addSearchStr($this->arrSql['search_name']);
            $this->arrVal[] = mb_ereg_replace("[ 　]+","",$searchName);
        }

        // 名前(フリガナ)
        if (!isset($this->arrSql['search_kana']))
	    $this->arrSql['search_kana'] = "";

        if ( strlen($this->arrSql['search_kana']) > 0 ) {
            $this->setWhere("replace(replace(kana, ' ', ''), '　', '') LIKE ?");
            $searchKana = $this->addSearchStr($this->arrSql['search_kana']);
            $this->arrVal[] = mb_ereg_replace("[ 　]+","",$searchKana);
        }

        // 都道府県
        if (!isset($this->arrSql['search_pref']))
	    $this->arrSql['search_pref'] = "";

        if (strlen($this->arrSql['search_pref']) > 0) {
            $this->setWhere( "pref = ?" );
            $this->arrVal[] = $this->arrSql['search_pref'];
        }

        // 電話番号
        if (!isset($this->arrSql['search_tel']))
	    $this->arrSql['search_tel'] = "";

        if (is_numeric( $this->arrSql['search_tel'])) {
            $this->setWhere("tel LIKE ?");
            $searchTel = $this->addSearchStr($this->arrSql['search_tel']);
            $this->arrVal[] = ereg_replace("-", "", $searchTel);
        }

        // 性別
        if (!isset($this->arrSql['search_sex']))
	    $this->arrSql['search_sex'] = "";

        if (is_array( $this->arrSql['search_sex'])) {
            $arrSexVal = $this->setItemTerm( $this->arrSql['search_sex'] ,'sex' );
            foreach ($arrSexVal as $data) {
                $this->arrVal[] = $data;
            }
        }

        // E-MAIL
        if (!isset($this->arrSql['search_email']))
	    $this->arrSql['search_email'] = "";

        if (strlen($this->arrSql['search_email']) > 0) {
            //カンマ区切りで複数の条件指定可能に
            $this->arrSql['search_email'] = explode(",", $this->arrSql['search_email']);
            $sql_where = "";
            foreach($this->arrSql['search_email'] as $val) {
                $val = trim($val);
                //検索条件を含まない
                if($this->arrSql['not_emailinc'] == '1') {
                    if($sql_where == "") {
                        $sql_where .= "dtb_customer.email NOT ILIKE ? ";
                    } else {
                        $sql_where .= "AND dtb_customer.email NOT ILIKE ? ";
                    }
                } else {
                    if($sql_where == "") {
                        $sql_where .= "dtb_customer.email ILIKE ? ";
                    } else {
                        $sql_where .= "OR dtb_customer.email ILIKE ? ";
                    }
                }
                $searchEmail = $this->addSearchStr($val);
                $this->arrVal[] = $searchEmail;
            }
            $this->setWhere($sql_where);
        }

        // E-MAIL(mobile)
        if (!isset($this->arrSql['search_email_mobile'])) {
	    $this->arrSql['search_email_mobile'] = "";
	}

        if (strlen($this->arrSql['search_email_mobile']) > 0) {
            //カンマ区切りで複数の条件指定可能に
            $this->arrSql['search_email_mobile'] = explode(",", $this->arrSql['search_email_mobile']);
            $sql_where = "";
            foreach($this->arrSql['search_email_mobile'] as $val) {
                $val = trim($val);
                //検索条件を含まない
                if($this->arrSql['not_email_mobileinc'] == '1') {
                    if($sql_where == "") {
                        $sql_where .= "dtb_customer.email_mobile NOT ILIKE ? ";
                    } else {
                        $sql_where .= "AND dtb_customer.email_mobile NOT ILIKE ? ";
                    }
                } else {
                    if($sql_where == "") {
                        $sql_where .= "dtb_customer.email_mobile ILIKE ? ";
                    } else {
                        $sql_where .= "OR dtb_customer.email_mobile ILIKE ? ";
                    }
                }
                $searchemail_mobile = $this->addSearchStr($val);
                $this->arrVal[] = $searchemail_mobile;
            }
            $this->setWhere($sql_where);
        }

        // メールマガジンの場合
        if($mode == 'customer') {
            // メルマガ受け取りの選択項目がフォームに存在する場合
            if ( isset($this->arrSql['search_htmlmail'])){
                if (SC_Utils_Ex::sfIsInt($this->arrSql['search_htmlmail'])){
                    $this->setWhere("mailmaga_flg = ?");
                    $this->arrVal[] = $this->arrSql['search_htmlmail'];
                } else {
                    //　メルマガ購読拒否は省く
                    $this->setWhere("mailmaga_flg <> 3");
                }
            }
        }

        // 配信メールアドレス種別
        if ( $mode == 'customer' ){
            if (isset($this->arrSql['search_mail_type'])){
                $sqlEmailMobileIsEmpty = "(dtb_customer.email_mobile IS NULL OR dtb_customer.email_mobile = '')";
                switch ($this->arrSql['search_mail_type']) {
                    // PCメールアドレス
                    case 1:
                        $this->setWhere("(dtb_customer.email <> dtb_customer.email_mobile OR $sqlEmailMobileIsEmpty)");
                        break;
                    // 携帯メールアドレス
                    case 2:
                        $this->setWhere("NOT $sqlEmailMobileIsEmpty");
                        break;
                    // PCメールアドレス (携帯メールアドレスを登録している会員は除外)
                    case 3:
                        $this->setWhere($sqlEmailMobileIsEmpty);
                        break;
                    // 携帯メールアドレス (PCメールアドレスを登録している会員は除外)
                    case 4:
                        $this->setWhere('dtb_customer.email = dtb_customer.email_mobile');
                        break;
                }
            }
        }

        // 購入金額指定
        if (!isset($this->arrSql['search_buy_total_from'])) $this->arrSql['search_buy_total_from'] = "";
        if (!isset($this->arrSql['search_buy_total_to'])) $this->arrSql['search_buy_total_to'] = "";
        if( is_numeric( $this->arrSql["search_buy_total_from"] ) || is_numeric( $this->arrSql["search_buy_total_to"] ) ) {
            $arrBuyTotal = $this->selectRange($this->arrSql["search_buy_total_from"], $this->arrSql["search_buy_total_to"], "buy_total");
            foreach ($arrBuyTotal as $data1) {
                $this->arrVal[] = $data1;
            }
        }

        // 購入回数指定
        if (!isset($this->arrSql['search_buy_times_from'])) $this->arrSql['search_buy_times_from'] = "";
        if (!isset($this->arrSql['search_buy_times_to'])) $this->arrSql['search_buy_times_to'] = "";
        if( is_numeric( $this->arrSql["search_buy_times_from"] ) || is_numeric( $this->arrSql["search_buy_times_to"] ) ) {
            $arrBuyTimes = $this->selectRange($this->arrSql["search_buy_times_from"], $this->arrSql["search_buy_times_to"], "buy_times");
            foreach ($arrBuyTimes as $data2) {
                $this->arrVal[] = $data2;
            }
        }

        // 誕生日期間指定
        if (!isset($this->arrSql['search_b_start_year'])) $this->arrSql['search_b_start_year'] = "";
        if (!isset($this->arrSql['search_b_start_month'])) $this->arrSql['search_b_start_month'] = "";
        if (!isset($this->arrSql['search_b_start_day'])) $this->arrSql['search_b_start_day'] = "";
        if (!isset($this->arrSql['search_b_end_year'])) $this->arrSql['search_b_end_year'] = "";
        if (!isset($this->arrSql['search_b_end_month'])) $this->arrSql['search_b_end_month'] = "";
        if (!isset($this->arrSql['search_b_end_day'])) $this->arrSql['search_b_end_day'] = "";
        if ( (strlen($this->arrSql['search_b_start_year']) > 0 && strlen($this->arrSql['search_b_start_month']) > 0 && strlen($this->arrSql['search_b_start_day']) > 0) ||
              strlen($this->arrSql['search_b_end_year']) > 0 && strlen($this->arrSql['search_b_end_month']) > 0 && strlen($this->arrSql['search_b_end_day']) > 0) {

            $arrBirth = $this->selectTermRange($this->arrSql['search_b_start_year'], $this->arrSql['search_b_start_month'], $this->arrSql['search_b_start_day']
                      , $this->arrSql['search_b_end_year'], $this->arrSql['search_b_end_month'], $this->arrSql['search_b_end_day'], 'birth');
            if (is_array($arrBirth)) {
                foreach ($arrBirth as $data3) {
                    $this->arrVal[] = $data3;
                }
            }
        }

        // 誕生月の検索
        if (!isset($this->arrSql['search_birth_month'])) $this->arrSql['search_birth_month'] = "";
        if (is_numeric($this->arrSql["search_birth_month"])) {
            $this->setWhere(" EXTRACT(month from birth) = ?");
            $this->arrVal[] = $this->arrSql["search_birth_month"];
        }

        // 登録期間指定
        if (!isset($this->arrSql['search_start_year'])) $this->arrSql['search_start_year'] = "";
        if (!isset($this->arrSql['search_start_month'])) $this->arrSql['search_start_month'] = "";
        if (!isset($this->arrSql['search_start_day'])) $this->arrSql['search_start_day'] = "";
        if (!isset($this->arrSql['search_end_year'])) $this->arrSql['search_end_year'] = "";
        if (!isset($this->arrSql['search_end_month'])) $this->arrSql['search_end_month'] = "";
        if (!isset($this->arrSql['search_end_day'])) $this->arrSql['search_end_day'] = "";
        if ( (strlen($this->arrSql['search_start_year']) > 0 && strlen($this->arrSql['search_start_month']) > 0 && strlen($this->arrSql['search_start_day']) > 0 ) ||
                (strlen($this->arrSql['search_end_year']) > 0 && strlen($this->arrSql['search_end_month']) >0 && strlen($this->arrSql['search_end_day']) > 0) ) {

            $arrRegistTime = $this->selectTermRange($this->arrSql['search_start_year'], $this->arrSql['search_start_month'], $this->arrSql['search_start_day']
                            , $this->arrSql['search_end_year'], $this->arrSql['search_end_month'], $this->arrSql['search_end_day'], $regdate_col);
            if (is_array($arrRegistTime)) {
                foreach ($arrRegistTime as $data4) {
                    $this->arrVal[] = $data4;
                }
            }
        }

        // 最終購入日指定
        if (!isset($this->arrSql['search_buy_start_year'])) $this->arrSql['search_buy_start_year'] = "";
        if (!isset($this->arrSql['search_buy_start_month'])) $this->arrSql['search_buy_start_month'] = "";
        if (!isset($this->arrSql['search_buy_start_day'])) $this->arrSql['search_buy_start_day'] = "";
        if (!isset($this->arrSql['search_buy_end_year'])) $this->arrSql['search_buy_end_year'] = "";
        if (!isset($this->arrSql['search_buy_end_month'])) $this->arrSql['search_buy_end_month'] = "";
        if (!isset($this->arrSql['search_buy_end_day'])) $this->arrSql['search_buy_end_day'] = "";

        if ( (strlen($this->arrSql['search_buy_start_year']) > 0 && strlen($this->arrSql['search_buy_start_month']) > 0 && strlen($this->arrSql['search_buy_start_day']) > 0 ) ||
                (strlen($this->arrSql['search_buy_end_year']) > 0 && strlen($this->arrSql['search_buy_end_month']) >0 && strlen($this->arrSql['search_buy_end_day']) > 0) ) {
            $arrRegistTime = $this->selectTermRange($this->arrSql['search_buy_start_year'], $this->arrSql['search_buy_start_month'], $this->arrSql['search_buy_start_day']
                            , $this->arrSql['search_buy_end_year'], $this->arrSql['search_buy_end_month'], $this->arrSql['search_buy_end_day'], "last_buy_date");
            if (is_array($arrRegistTime)) {
                foreach ($arrRegistTime as $data4) {
                    $this->arrVal[] = $data4;
                }
            }
        }

        // 購入商品コード
        if (!isset($this->arrSql['search_buy_product_code']))
	    $this->arrSql['search_buy_product_code'] = "";
        if (strlen($this->arrSql['search_buy_product_code']) > 0) {
            $this->setWhere( "customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_code LIKE ? ))");
            $search_buyproduct_code = $this->addSearchStr($this->arrSql['search_buy_product_code']);
            $this->arrVal[] = $search_buyproduct_code;
        }

        // 購入商品名称
        if (!isset($this->arrSql['search_buy_product_name']))
	    $this->arrSql['search_buy_product_name'] = "";
        if (strlen($this->arrSql['search_buy_product_name']) > 0) {
            $this->setWhere( "customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_name LIKE ? ))");
            $search_buyproduct_name = $this->addSearchStr($this->arrSql['search_buy_product_name']);
            $this->arrVal[] = $search_buyproduct_name;
        }

        // カテゴリーを選択している場合のみ絞込検索を行う
        if (!isset($this->arrSql['search_category_id']))
	    $this->arrSql['search_category_id'] = "";
        if (strlen($this->arrSql['search_category_id']) > 0) {
            // カテゴリーで絞込検索を行うSQL文生成
            list($tmp_where, $tmp_arrval) = $objDb->sfGetCatWhere($this->arrSql['search_category_id']);

            // カテゴリーで絞込みが可能の場合
            if($tmp_where != "") {
                $this->setWhere( " customer_id IN (SELECT distinct customer_id FROM dtb_order WHERE order_id IN (SELECT distinct order_id FROM dtb_order_detail WHERE product_id IN (SELECT product_id FROM dtb_product_categories WHERE ".$tmp_where." ))) ");
                $this->arrVal = array_merge((array)$this->arrVal, (array)$tmp_arrval);
            }
        }

        // 携帯電話番号
        if (!isset($this->arrSql['search_cell']))
	    $this->arrSql['search_cell'] = "";
        if (is_numeric( $this->arrSql['search_cell'])) {
            $this->setWhere("cell LIKE ?");
            $searchTel = $this->addSearchStr($this->arrSql['search_cell']);
            $this->arrVal[] = ereg_replace("-", "", $searchTel);
        }

        // 会員状態
        if (!isset($this->arrSql['search_status'])) $this->arrSql['search_status'] = "";
        if ( is_array( $this->arrSql['search_status'] ) ){
            $arrStatusVal = $this->setItemTerm( $this->arrSql['search_status'] ,'status' );
            foreach ($arrStatusVal as $data) {
                $this->arrVal[] = $data;
            }
        }

        // 顧客区分
        if (!isset($this->arrSql['search_customer_kbn'])) {
            $this->arrSql['search_customer_kbn'] = "";
        }
        if (is_array($this->arrSql['search_customer_kbn'])) {
            $arrCustomerKbnVal = $this->setItemTerm
                ($this->arrSql['search_customer_kbn'], 'customer_kbn');
            foreach ($arrCustomerKbnVal as $data) {
                $this->arrVal[] = $data;
            }
        }

        // 貸倒区分
        if (!isset($this->arrSql['search_kashidaore_kbn'])) {
            $this->arrSql['search_kashidaore_kbn'] = "";
        }
        if (is_array($this->arrSql['search_kashidaore_kbn'])) {
            $arrKashidaoreKbnVal = $this->setItemTerm
                ($this->arrSql['search_kashidaore_kbn'], 'kashidaore_kbn');
            foreach ($arrKashidaoreKbnVal as $data) {
                $this->arrVal[] = $data;
            }
        }

        $this->setOrder( "customer_id DESC" );
    }

    // 検索用SQL
    function getList() {
        $this->select =<<<EOF
SELECT customer_id
     , name
     , kana
     , sex
     , email
     , email_mobile
     , tel
     , pref
     , status
     , customer_kbn
     , kashidaore_kbn
     , create_date
     , update_date
     , mailmaga_flg
     , del_flg
  FROM dtb_customer 
EOF;
        return $this->getSql(0);
    }

    function getListMailMagazine($is_mobile = false) {
        $colomn = $this->getMailMagazineColumn($is_mobile);
        $this->select =<<<EOF
SELECT $colomn
  FROM dtb_customer
EOF;
        return $this->getSql(0);
    }

    // 検索総数カウント用SQL
    function getListCount() {
        $this->select =<<<EOF
SELECT COUNT(customer_id)
  FROM dtb_customer
EOF;
        return $this->getSql(1);
    }

    // CSVダウンロード用SQL
    function getListCSV($arrColumnCSV) {
        $this->arrColumnCSV = $arrColumnCSV;
        $i = 0;
        foreach ($this->arrColumnCSV as $val) {
            if ($i != 0) $state .= ", ";
            $state .= $val['sql'];
            $i ++;
        }

        $this->select =<<<EOF
SELECT
    $state
FROM
    dtb_customer
EOF;
        return $this->getSql(2);
    }

    function getWhere() {
        return array($this->where, $this->arrVal);
    }
}
?>
