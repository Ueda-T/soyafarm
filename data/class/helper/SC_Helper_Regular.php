<?php

/**
 * 定期情報関連のヘルパークラス.
 *
 *
 * @package Helper
 * @author
 * @version $Id:$
 */
class SC_Helper_Regular {

    /**
     * 定期受注情報、定期明細情報の登録を行う
     *
     * @param  array $orderParams 登録する受注情報の配列
     * @param  array $orderParams 登録する配送先情報の配列
     * @param  SC_CartSession $objCartSession カート情報のインスタンス
     * @param  integer $cartKey 登録を行うカート情報のキー
     * @param array $arrAddShip 配送情報の追加項目連想配列
     * @return void
     */
    function registerRegularOrderComplete(
        $arrOrder, $arrShipping, &$objCartSession, $cartKey, $arrAddShip) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 定期購入が含まれない場合は処理終了
        if ($this->checkRegularPurchase(
            $objCartSession->getAllCartList(), $cartKey) === false) {
            return;
        }

        if (!SC_Utils_Ex::isBlank($arrShipping['shipping_date'])) {
            $d = mb_strcut($arrShipping["shipping_date"], 0, 10);
            $arrDate = explode("/", $d);
            $todokeDay = $arrDate[2];
        }

        // 受注明細情報を取得
        $cartItems = $objCartSession->getCartList($cartKey);

        // 定期受注IDを採番
        $regular_id = $objQuery->nextVal('dtb_regular_order_regular_id');

        $line_no = 1;
        foreach($cartItems as $detail) {
            if ($detail['regular_flg'] == REGULAR_PURCHASE_FLG_OFF) {
                continue;
            }
            // 届け日をセット
            $detail['todoke_day'] = $todokeDay;

            // 定期受注明細情報の登録
            $this->registerRegularOrderDetail
                ($regular_id, $line_no, $detail);
            $line_no++;
        }

        // 定期受注情報の登録
        $arrShipping["shipment_cd"] = $arrAddShip["shipping_area_code"];
        $this->registerRegularOrder
            ($regular_id, $arrOrder, $arrShipping);
    }

    /**
     * 定期受注ID、行NOに紐付く定期受注明細情報を取得
     *
     * @param integer $regular_id 定期受注ID 
     * @param integer $line_no    行NO
     * @return array 定期受注明細情報の配列
     */
    function getRegularOrderDetail($regular_id, $line_no) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOS
SELECT
    T3.product_id,
    T3.product_class_id as product_class_id,
    T3.product_type_id AS product_type_id,
    T3.sale_limit,
    T3.sale_minimum_number,
    T4.brand_id,
    T1.customer_id,
    T1.order_name,
    T1.order_kana,
    T1.order_tel,
    T1.order_zip,
    T1.order_pref,
    T1.order_addr01,
    T1.order_addr02,
    T1.payment_id,
    T2.regular_id,
    T2.line_no,
    T2.product_name,
    T2.price,
    T2.quantity,
    T2.todoke_kbn,
    T2.todoke_day,
    T2.todoke_week,
    T2.todoke_week2,
    T2.course_cd,
    T2.cancel_date,
    T2.cancel_reason_cd,
    T2.status as status,
    DATE_FORMAT(T2.next_arrival_date,'%Y/%m/%d') next_arrival_date,
    DATE_FORMAT(T2.after_next_arrival_date,'%Y/%m/%d') after_next_arrival_date,
    CASE
        WHEN EXISTS(
            SELECT * FROM dtb_products
                WHERE
                    product_id = T3.product_id
                    AND del_flg = 0
                    AND status = 1
            )
        THEN '1' ELSE '0'
        END AS enable,
    NULL AS effective,
    T4.deliv_date_id,
    O.campaign_cd,
    O.device_type_id
FROM
    dtb_regular_order T1
        JOIN dtb_regular_order_detail T2
            ON T1.regular_id = T2.regular_id
        JOIN dtb_products_class T3
            ON T2.product_class_id = T3.product_class_id
        JOIN dtb_products T4
            ON T3.product_id = T4.product_id
        LEFT JOIN dtb_order O
            ON T1.order_id = O.order_id
WHERE
    T1.regular_id = $regular_id
    AND T2.line_no = $line_no
EOS;

        $arrRes = $objQuery->getAll($sql);
        return $arrRes[0];
    }

    /**
     * 定期受注ID、ブランドID、コースに紐付く定期受注明細情報を取得
     *
     * @param integer $regular_id 定期受注ID
     * @param array   $arrRegular 定期情報
     * @return array 定期受注明細情報の配列
     */
    function getRegularOrderDetailGroup($regular_id, $arrRegular) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 購入中データのみ抽出
        $status = REGULAR_ORDER_STATUS_PURCHASE;

        $sql =<<<EOS
SELECT
    T3.product_id,
    T3.product_class_id as product_class_id,
    T3.product_type_id AS product_type_id,
    T3.sale_limit,
    T3.sale_minimum_number,
    T3.product_code,
    T4.brand_id,
    T1.customer_id,
    T1.order_name,
    T1.order_kana,
    T1.order_tel,
    T1.order_zip,
    T1.order_pref,
    T1.order_addr01,
    T1.order_addr02,
    T1.payment_id,
    T2.regular_id,
    T2.line_no,
    T2.product_name,
    T2.price,
    T2.quantity,
    T2.todoke_kbn,
    T2.todoke_day,
    T2.todoke_week,
    T2.todoke_week2,
    T2.course_cd,
    T2.cancel_date,
    T2.cancel_reason_cd,
    T2.status as status,
    DATE_FORMAT(T2.next_arrival_date,'%Y/%m/%d') next_arrival_date,
    DATE_FORMAT(T2.after_next_arrival_date,'%Y/%m/%d') after_next_arrival_date,
    CASE
        WHEN EXISTS(
            SELECT * FROM dtb_products
                WHERE
                    product_id = T3.product_id
                    AND del_flg = 0
                    AND status = 1
            )
        THEN '1' ELSE '0'
        END AS enable,
    NULL AS effective,
    T4.deliv_date_id
FROM
    dtb_regular_order T1
        JOIN dtb_regular_order_detail T2
            ON T1.regular_id = T2.regular_id
        JOIN dtb_products_class T3
            ON T2.product_class_id = T3.product_class_id
        JOIN dtb_products T4
            ON T3.product_id = T4.product_id
WHERE
    T1.regular_id = $regular_id
    AND T4.brand_id = {$arrRegular["brand_id"]}
    AND T2.status = {$status}
    AND T2.course_cd = {$arrRegular["course_cd"]}
    AND DATE_FORMAT(T2.next_arrival_date, '%Y/%m/%d') = '{$arrRegular["next_arrival_date"]}'
EOS;

        // お届け日
        if (strlen($arrRegular["todoke_day"])) {
            $sql .= " AND T2.todoke_day = {$arrRegular["todoke_day"]} ";
        } else {
            $sql .= " AND T2.todoke_day IS NULL ";
        }
        // 曜日指定１
        if (strlen($arrRegular["todoke_week"])) {
            $sql .= " AND T2.todoke_week = {$arrRegular["todoke_week"]} ";
        } else {
            $sql .= " AND T2.todoke_week IS NULL ";
        }
        // 曜日指定２
        if (strlen($arrRegular["todoke_week2"])) {
            $sql .= " AND T2.todoke_week2 = {$arrRegular["todoke_week2"]} ";
        } else {
            $sql .= " AND T2.todoke_week2 IS NULL ";
        }

        GC_Utils_Ex::gfFrontLog($sql);
        $arrRes = $objQuery->getAll($sql);
        return $arrRes;
    }

    /**
     * 顧客番号に紐付く定期受注明細の件数を取得する.
     *
     * @param integer $customer_id 顧客ID
     * @return array 定期受注明細の配列
     */
    function getRegularOrderDetailCount($customer_id) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOS
SELECT
    COUNT(*)
FROM
    dtb_regular_order T1
        JOIN dtb_regular_order_detail T2
            ON T1.regular_id = T2.regular_id
        JOIN dtb_products_class T3
            ON T2.product_class_id = T3.product_class_id
WHERE
    T1.customer_id = $customer_id
ORDER BY T2.regular_id, line_no
EOS;

        return $objQuery->getOne($sql);
    }


    /**
     * 顧客番号に紐付く定期受注明細一覧を取得する.
     *
     * @param integer $customer_id 顧客ID
     * @return array 定期受注明細の配列
     */
    function getRegularOrderDetailList($customer_id, $start_no) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $limit  = SEARCH_PMAX;
        $offset = $start_no;

        // ステータス(9：解約)
        $cancel_status = REGULAR_ORDER_STATUS_CANCEL;

        $sql =<<<EOS
SELECT
    T3.product_id,
    T3.product_class_id,
    T3.product_type_id,
    T2.regular_id,
    T2.line_no,
    T2.product_name,
    T2.price,
    T2.quantity,
    T2.course_cd,
    IF (T2.next_arrival_date - INTERVAL 1 WEEK <= NOW() , false, true) AS disp_flg,
    DATE_FORMAT(T2.next_arrival_date,'%Y/%m/%d') AS next_arrival_date,
    DATE_FORMAT(T2.after_next_arrival_date,'%Y/%m/%d') AS after_next_arrival_date,
    T2.status,
    T1.status AS product_status
FROM
    dtb_regular_order T1
        INNER JOIN dtb_regular_order_detail T2
            ON T1.regular_id = T2.regular_id
                AND T2.del_flg = 0
        INNER JOIN dtb_products_class T3
            ON T2.product_class_id = T3.product_class_id
WHERE
    T1.customer_id = {$customer_id}
    AND T2.status != '{$cancel_status}'
    AND T1.del_flg = 0
ORDER BY T2.regular_id, line_no
LIMIT {$limit} OFFSET {$start_no}
EOS;

        return $objQuery->getAll($sql);
    }

    /**
     * 定期受注明細情報のお届け日、商品情報を更新する
     *
     * @param object $objFormParam SC_FormParamのインスタンス
     * @param int $regular_id 定期受注ID
     * @param int $line_no 行NO
     */
    function updateRegularProduct(&$objFormParam, $regular_id, $line_no) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objProduct = new SC_Product_Ex();

        $product_class_id =
            $objFormParam->getValue('after_product_class_id');

        // 商品ID
        $product_id =
            $objFormParam->getValue('after_product_id');
        if (empty($product_id)) {
            $product_id = $objFormParam->getValue('before_product_id');
        }
        $product_class_id =
            $objFormParam->getValue('after_product_class_id');
        // 商品規格ID
        if (empty($product_class_id)) {
            $product_class_id =
                $objFormParam->getValue('before_product_class_id');
        }

        // 変更後商品情報を取得
        $arrProductClass =
            $objProduct->getDetailAndProductsClass($product_class_id);
        // 商品名
        $product_name = $arrProductClass['name'];
        // 価格
        $price = $arrProductClass['price01'];
        // 数量
        $quantity = $objFormParam->getValue('regular_quantity');
        // 届け日指定区分
        $todoke_kbn = $objFormParam->getValue('todoke_kbn');
        // コースCD 
        $course_cd = $objFormParam->getValue('course_cd');
        // 曜日指定1
        $todoke_week = $objFormParam->getValue('todoke_week');
        if (empty($todoke_week)) {
            $todoke_week = 'NULL';
        }

        // 曜日指定2
        $todoke_week2 = $objFormParam->getValue('todoke_week2');
        if (empty($todoke_week2)) {
            $todoke_week2 = 'NULL';
        }

        // 次回お届け日
        $next_arrival_date =
            $objFormParam->getValue('next_arrival_date');

        // 次々回お届け日
        $after_next_arrival_date =
            $objFormParam->getValue('after_next_arrival_date');

        // お届け日
        if ($todoke_kbn == TODOKE_KBN_DAY) {
            $todoke_day = substr($next_arrival_date, 8);
        } else {
            $todoke_day = 'NULL';
        }

        $sql = <<<EOS
UPDATE
    dtb_regular_order_detail
SET
    product_id              =  {$product_id},
    product_class_id        =  {$product_class_id},
    product_name            = '{$product_name}',
    price                   =  {$price},
    quantity                =  {$quantity},
    todoke_kbn              = '{$todoke_kbn}',
    todoke_day              =  {$todoke_day},
    todoke_week             =  {$todoke_week},
    todoke_week2            =  {$todoke_week2},
    course_cd               = '{$course_cd}',
    next_arrival_date       = '{$next_arrival_date}',
    after_next_arrival_date = '{$after_next_arrival_date}',
    update_date             = now()
WHERE
    regular_id = $regular_id
    AND line_no = $line_no
EOS;

        $objQuery->query($sql);
    }

    /**
     * 定期受注明細情報の行NOの最大値を取得する
     *
     * @param integer $regular_id 定期受注ID
     * @return integer 行NOの最大値
     */
    function getRegularOrderDetailMaxLineNo($regular_id) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOS
SELECT
    MAX(line_no)
FROM
    dtb_regular_order_detail
WHERE
    regular_id = $regular_id
EOS;

        return $objQuery->getOne($sql);
    }

    /**
     * 定期受注情報を登録する
     *
     * @param  array $orderParams 登録する受注情報の配列
     * @param  array $orderParams 登録する配送先情報の配列
     * @return integer 定期受注ID
     */
    function registerRegularOrder($regular_id, $arrOrder, $arrShipping) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        // 郵便番号
        if (preg_match("/^\d{3}\-\d{4}$/", $arrShipping['shipping_zip'])) {
            $zip  = substr(trim($arrShipping['shipping_zip']), 0, 3);
            $zip .= '-';
            $zip .= substr(trim($arrShipping['shipping_zip']), 4, 4);
        } else {
            $zip = $arrShipping['shipping_zip'];
        }
        // 状況(0:受注中)
        $status = REGULAR_ORDER_STATUS_ORDER;

        /*
        // TODO 産直品の場合は産直をセットする
        // 出荷場所(1:岡山)
            $shipment_cd = DROP_SHIPMENT_FLG_OFF;
         */

        // エスケープ
        $addr01 = addslashes($arrShipping['shipping_addr01']);
        $addr02 = addslashes($arrShipping['shipping_addr02']);

        // 送信フラグ(0:未送信をセット)
        $send_flg = SEND_FLG_UNSENT;

        $sql =<<<EOS
INSERT INTO
    dtb_regular_order (
        regular_id
        ,customer_id
        ,order_date
        ,order_name
        ,order_kana
        ,order_tel
        ,order_zip
        ,order_pref
        ,order_addr01
        ,order_addr02
        ,order_addr_kana
        ,status
        ,shipment_cd
        ,deliv_id
        ,box_size
        ,time_id
        ,remarks
        ,include_kbn
        ,payment_id
        ,deliv_fee
        ,order_id
        ,send_flg
        ,del_flg
        ,create_date
        ,update_date
    )
    VALUES (
        {$regular_id}
        ,{$arrOrder['customer_id']}
        ,now()
        ,'{$arrShipping['shipping_name']}'
        ,'{$arrShipping['shipping_kana']}'
        ,'{$arrShipping['shipping_tel']}'
        ,'{$zip}'
        ,'{$arrShipping['shipping_pref']}'
        ,'{$addr01}'
        ,'{$addr02}'
        ,'{$arrShipping['shipping_addr_kana']}'
        ,'{$status}'
        ,'{$arrShipping['shipment_cd']}'
        ,'{$arrShipping['deliv_id']}'
        ,'{$arrOrder['deliv_box_id']}'
        ,'{$arrShipping['time_id']}'
        ,'{$arrOrder['note']}'
        ,'{$arrOrder['include_kbn']}'
        ,'{$arrOrder['payment_id']}'
        ,'{$arrOrder['deliv_fee']}'
        ,'{$arrOrder['order_id']}'
        ,'{$send_flg}'
        ,0
        ,now()
        ,now()
);
EOS;
        $objQuery->query($sql);

        return $regular_id;
    }

    /**
     * 定期受注明細情報を新規登録する
     *
     * @param integer $regular_id 定期受注ID
     * @param integer $line_no    行NO
     * @param array $arrDetail 定期商品情報の連想配列
     * @return void
     */
    function registerRegularOrderDetail(
        $regular_id, $line_no, $arrDetail) {

            $objQuery =& SC_Query_Ex::getSingletonInstance();

            if (empty($arrDetail['todoke_week_no'])) {
                $arrDetail['todoke_week_no'] = 'NULL';
                $arrDetail['todoke_week'] = 'NULL';
            }

            $productsClass = $arrDetail['productsClass'];

        $sql =<<<EOS
INSERT INTO
    dtb_regular_order_detail(
        regular_id
        ,line_no
        ,product_id
        ,product_class_id
        ,product_name
        ,price
        ,quantity
        ,todoke_kbn
        ,todoke_day
        ,todoke_week
        ,todoke_week2
        ,course_cd
        ,status
        ,cut_rate
        ,del_flg
        ,create_date
        ,update_date
    )
    VALUES (
        {$regular_id},
        {$line_no},
        {$productsClass['product_id']},
        {$productsClass['product_class_id']},
        '{$productsClass['name']}',
        {$arrDetail['price']},
        {$arrDetail['quantity']},
        '{$arrDetail['todoke_kbn']}',
        IF({$arrDetail['todoke_kbn']} <> 1, NULL, '{$arrDetail['todoke_day']}'),
        {$arrDetail['todoke_week_no']},
        {$arrDetail['todoke_week']},
        '{$arrDetail['course_cd']}',
        1,
        '{$arrDetail['cut_rate']}',
        0,
        now(),
        now()
);
EOS;

        GC_Utils_Ex::gfFrontLog($sql);

        $objQuery->query($sql);
    }

    /**
     * Myページで追加された商品を
     * 定期受注明細情報に登録する
     *
     * @param integer $regular_id 定期受注ID
     * @param integer $line_no    行NO
     * @param array $arrRegular 定期受注情報の連想配列
     * @param array $arrDetail 追加商品情報の連想配列
     * @return void
     */
    function registerAddRegularOrderDetail(
        $regular_id, $line_no, $arrRegular, $arrDetail) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrProduct = $arrDetail["productsClass"];
        $sql =<<<EOS
INSERT INTO
    dtb_regular_order_detail(
        regular_id
        ,line_no
        ,product_id
        ,product_class_id
        ,product_name
        ,price
        ,quantity
        ,todoke_kbn
        ,todoke_day
        ,todoke_week
        ,todoke_week2
        ,course_cd
        ,status
        ,next_arrival_date
        ,after_next_arrival_date
        ,del_flg
        ,create_date
        ,update_date
    )
    VALUES (
        {$regular_id},
        {$line_no},
        {$arrProduct['product_id']},
        {$arrProduct['product_class_id']},
        '{$arrProduct['name']}',
        {$arrDetail['price']},
        {$arrDetail['quantity']},
        '{$arrRegular['todoke_kbn']}',
        {$arrRegular['todoke_day']},
        {$arrRegular['todoke_week']},
        {$arrRegular['todoke_week2']},
        '{$arrRegular['course_cd']}',
        '{$arrRegular['status']}',
        '{$arrRegular['next_arrival_date']}',
        '{$arrRegular['after_next_arrival_date']}',
        0,
        now(),
        now()
);
EOS;

        $objQuery->query($sql);
    }


    /**
     * 定期受注情報の送信フラグを未送信へ更新
     *
     * @param integer $regular_id 定期受注ID
     * @return void
     */
    function updateRegularOrderSendFlg($regular_id) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $send_flg = INOS_SEND_FLG_OFF;

        $sql = <<<EOS
UPDATE
    dtb_regular_order
SET
    send_flg     = '{$send_flg}',
    update_date  = now()
WHERE
    regular_id = '{$regular_id}'
EOS;

        $objQuery->query($sql);
    }


    /**
     * カート内に定期商品が含まれるか判定
     *
     * @param $arrCartItems カートセッション情報の連想配列
     * @param  integer $cartKey 登録を行うカート情報のキー
     * @param boolean true:定期商品あり false:定期商品なし
     */
    function checkRegularPurchase($arrCartItems, $cartKey=1) {

        foreach ($arrCartItems[$cartKey] as $item) {
            if ($item['regular_flg'] == REGULAR_PURCHASE_FLG_ON) {
                return true;
            }
        }
        return false;
    }

    /**
     * コースCDからお届け間隔(日ごと、月ごと)を判定して返却する
     *
     * @param  int $course_cd コースCD
     * @return int 日ごと:1 月ごと:2
     */
    function getTodokeCycle($course_cd) {

        // 20～90
        if ($course_cd >= COURSE_CD_DAY_MIN &&
            $course_cd <= COURSE_CD_DAY_MAX) {

            // 日ごと
            return TODOKE_CYCLE_DAY;

        } else {
            // 月ごと
            return TODOKE_CYCLE_MONTH;
        }
    }

    /**
     * コースCDからお届け間隔(日ごと、月ごと)を判定して文字列を返却する
     * 
     * @param  int $course_cd    コースCD
     * @return string お届け間隔の文字列
     */
    function getTodokeKankakuString($course_cd, $todoke_week, $todoke_week2) {

        $todoke_kankaku = $course_cd;

        $todoke_cycle = $this->getTodokeCycle($course_cd);

        if ($todoke_cycle == TODOKE_CYCLE_DAY) {
            $todoke_kankaku .= '日ごと　';
        } else {
            $todoke_kankaku .= 'ヶ月ごと　';
        }

        if (!empty($todoke_week) && !empty($todoke_week2)) {

            $masterData = new SC_DB_MasterData_Ex();
            // お届け曜日
            $arrTodokeWeekNo =
                $masterData->getMasterData('mtb_todoke_week');

            // XXX 既存のマスタとIDが一致しないため、独自で設定
            //$this->arrTodokeWeek = $masterData->getMasterData('mtb_wday');
            $arrTodokeWeek = array(1 => '日',
                                   2 => '月',
                                   3 => '火',
                                   4 => '水',
                                   5 => '木',
                                   6 => '金',
                                   7 => '土');

            $todoke_kankaku .= $arrTodokeWeekNo[$todoke_week];
            $todoke_kankaku .= $arrTodokeWeek[$todoke_week2];
            $todoke_kankaku .= '曜日';
        }
        return $todoke_kankaku;
    }

    /**
     * お届け日指定区分を返却する
     *
     * @param  int $todoke_week  曜日指定
     * @param  int $todoke_week2 曜日指定2
     * @return int 届け日指定:1 曜日指定:2
     */
    function getTodokeKbn($todoke_week = "", $todoke_week2 = "") {

        if (!empty($todoke_week) && !empty($todoke_week2)) {
            // 曜日指定
            return TODOKE_KBN_WEEK;
        } else {
            // 届け日指定
            return TODOKE_KBN_DAY;
        }
    }

    /**
     * Nヶ月後またはN日後の日付を取得する
     *
     * @param string $base_date     基準日(YYYY/MM/DD形式)
     * @param string $interval_str  インターバル('day'または'month')
     * @param int    $interval_num  インターバル(n)
     * @return Nヶ月後の日付またはN日後の日付
     */
    function getAfterNextArrivalDate($base_date, $interval_str, $interval_num) {

        $arrBaseDate = explode("/", $base_date);
        $year  = $arrBaseDate[0];
        $month = $arrBaseDate[1];
        $day   = $arrBaseDate[2];

        if ($interval_str == 'month') {
            // 基準日の末日を取得
            $base_date_lastday = date("Y/m/t", strtotime($base_date));
            $next_month = $month + $interval_num;
            if ($next_month == '13') {
                $next_month = 1;
                $year++;
            }
            // 届け日
            $base_date_day = substr($base_date, 8);

            // 末日対策
            if (($base_date_lastday == $base_date && $next_month != 2) ||
                ($next_month == 2 && $base_date_day >= 29)) {
                $after_date =
                    date("Y/m/t", strtotime($year.'-'.$next_month.'-'.'1'));

            } else {
                $after_date =
                    date('Y/m/d', strtotime($year.'-'.$next_month.'-'.$day));
            }
        } else {
            $start_date = mktime(0, 0, 0, $month, $day, $year);
            $interval = '+' . $interval_num . ' '. $interval_str;
            $after_date = date('Y/m/d', strtotime($interval, $start_date));
        }
        return $after_date;
    }


    /**
     * 指定した年月の第n d曜日の日付を取得
     *
     * @param string $base_date 基準日
     * @papam int    $week 第n週か(1:第一、2:第二、3:第三、4:第四)
     * @param int    $wday 曜日(0:日～6:土)
     * @param int    $interval nヶ月後
     */
    function getWeekDate($base_date, $week, $wday, $interval) {

        // 基準日から年月を取得
        $arrBaseDate = explode("/", $base_date);
        $year  = $arrBaseDate[0];
        $month = $arrBaseDate[1];
        $month = $month + $interval;
        if ($month == 13) {
            $month = 1;
            $year++;
        }

        // XXX INOSインターフェースの曜日IDと一致しないためマイナスする
        $wday--;
        // 指定した年月の最初の曜日を取得
        $first_day = date("w", mktime(0, 0, 0, $month, 1, $year));

        // 求めたい曜日の第1週の日付けを計算する
        $wday = $wday - $first_day + 1;
        if($wday <= 0) {
            $wday += 7;
        }
        $wday += 7 * ($week - 1);

        return date("Y-m-d", mktime(0, 0, 0, $month, $wday, $year));
    }
}
