#!/bin/sh

### DB情報 
SERVER="localhost"
DATABASE=rohto
USER=rohto
PASSWD=rohto

### ログファイル
LOG_FILE="./regular_import.log"

###
### 各種定数
###
## WEB配送業者ID
DELIV_ID_YAMATO=1
DELIV_ID_SAGAWA=2
DELIV_ID_YAMATO_MAIL=3
## INOS配送業者ID
INOS_DELIV_ID_YAMATO=0
INOS_DELIV_ID_SAGAWA=1
## 箱サイズ
DELIV_BOX_ID_MAIL=1

put_log() {
    DATE=`date '+%Y/%m/%d %H:%M:%S'`
    echo "$DATE : $1" >> $LOG_FILE
}

# 引数が存在する場合は整合性チェック
if [ $# -eq 0 ]; then
    put_log "引き数にファイル名を指定して下さい。"
    exit 1
fi

if [ $# -gt 0 ]; then
    FILE=$1
    if  [ ! -e $FILE ]; then
        put_log "$FILEが存在しません。"
        exit 1
    fi
fi

#
#データを一旦初期化
#
mysql -h $SERVER -u $USER -p$PASSWD $DATABASE  <<__EOS
TRUNCATE TABLE dtb_regular_inos_import;

__EOS
put_log "定期インポートテーブルを初期化しました"

#
#CSVファイルからデータを登録
#
mysql -h $SERVER -u $USER -p$PASSWD $DATABASE  <<__EOS
SET CHARACTER_SET_DATABASE=cp932;

LOAD DATA LOCAL INFILE '$FILE'
    INTO TABLE $DATABASE.dtb_regular_inos_import 
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n'

SET
    regular_base_no        = NULLIF(regular_base_no, ''),
    customer_cd            = NULLIF(customer_cd, ''),
    order_date             = NULLIF(order_date, '0000-00-00'),
    order_kana             = NULLIF(order_kana, ''),
    order_tel              = NULLIF(order_tel, ''),
    order_zip              = NULLIF(order_zip, ''),
    order_addr_kana        = NULLIF(order_addr_kana, ''),
    order_addr01           = NULLIF(order_addr01, ''),
    order_addr02           = NULLIF(order_addr02, ''),
    status                 = IFNULL(status, ''),
    next_ship_date         = NULLIF(next_ship_date, '0000-00-00'),
    after_next_ship_date   = NULLIF(after_next_ship_date, '0000-00-00'),
    shipment_cd            = NULLIF(shipment_cd, ''),
    deliv_id               = IFNULL(deliv_id, ''),
    box_size               = IFNULL(box_size, ''),
    time_id                = IFNULL(time_id, 0),
    remarks                = IFNULL(remarks, ''),
    payment_id             = NULLIF(payment_id, ''),
    deliv_fee              = IFNULL(deliv_fee, ''),
    buy_num                = IFNULL(buy_num, '0'),
    order_id               = NULLIF(order_id, ''),
    del_flg                = NULLIF(del_flg, ''),
    update_date            = NULLIF(update_date, '0000-00-00'),
    dtl_line_no            = NULLIF(dtl_line_no, ''),
    dtl_product_code       = NULLIF(dtl_product_code, ''),
    dtl_product_name       = NULLIF(dtl_product_name, ''),
    dtl_quantity           = NULLIF(dtl_quantity, ''),
    dtl_price              = NULLIF(dtl_price, ''),
    dtl_course_cd          = NULLIF(dtl_course_cd, ''),
    dtl_status             = IFNULL(dtl_status, ''),
    dtl_todoke_kbn         = NULLIF(dtl_todoke_kbn, ''),
    dtl_todoke_day         = NULLIF(dtl_todoke_day, ''),
    dtl_todoke_week        = NULLIF(dtl_todoke_week, ''),
    dtl_todoke_week2       = NULLIF(dtl_todoke_week2, ''),
    dtl_next_arrival_date  = NULLIF(dtl_next_arrival_date, '0000-00-00'),
    dtl_after_next_arrival_date
                           = NULLIF(dtl_after_next_arrival_date, '0000-00-00'),
    dtl_cancel_date        = NULLIF(dtl_cancel_date, '0000-00-00'),
    dtl_cancel_reason_cd   = NULLIF(dtl_cancel_reason_cd, '')
;
__EOS
put_log "定期インポートテーブルへ登録が完了しました"

####
#### 郵便番号データ存在チェック
####
##ZIP=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
##     SELECT IM.order_zip
##       FROM dtb_regular_inos_import IM
##      WHERE NOT EXISTS 
##    (SELECT *
##       FROM mtb_zip ZP
## INNER JOIN mtb_pref PR
##         ON PR.name = ZP.state
##      WHERE ZP.zipcode = replace(IM.order_zip, '-', '')
##    )
##__EOS`
##
##if [ ${#ZIP} -gt 0 ]; then
##    put_log "郵便番号から紐づく都道府県コードが存在しません。"
##    put_log "$ZIP"
##    exit 1
##fi
##
## put_log "郵便番号チェック終了"

##
## レコードの重複チェック
## (コース受注NOと行NOが重複しているレコードはエラーとする)
##
##BASE_CD=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
##SELECT
##    CONCAT("コース受注CD：",IM.regular_base_no) AS regular_base_no
##   ,CONCAT("行NO：", dtl_line_no) AS dtl_line_no 
##FROM
##    dtb_regular_inos_import IM
##GROUP BY IM.regular_base_no, IM.dtl_line_no
##HAVING COUNT(IM.regular_base_no) <> 1
##__EOS`
##
##if [ ${#BASE_CD} -gt 0 ]; then
##    put_log "コース受注CD、行NOが重複するデータが存在します。"
##    put_log "$BASE_CD"
##    exit 1
##fi
## put_log "レコード重複チェック終了"

##
## 顧客データ存在チェック
## (顧客CDに紐付く顧客データが顧客マスタに存在しない場合はエラー)
##
##CUSTOMER_CD=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
##SELECT
##    IM.customer_cd
##FROM
##    dtb_regular_inos_import IM
##WHERE NOT EXISTS 
##    (SELECT * FROM dtb_customer C 
##        WHERE C.customer_cd = IM.customer_cd AND C.del_flg = 0) 
##__EOS`
##
##if [ ${#CUSTOMER_CD} -gt 0 ]; then
##    put_log "顧客マスタに顧客データが存在しません。"
##    put_log "$CUSTOMER_CD"
##    exit 1
##fi
## put_log "顧客データ存在チェック終了"

##
## コース受注NOに紐付く受注データが存在するか？
##
##REGULAR_BASE_NO=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
##SELECT
##    IM.regular_base_no
##FROM
##    dtb_regular_inos_import IM
##WHERE NOT EXISTS 
##    (SELECT * FROM dtb_order O
##        WHERE O.regular_base_no = IM.regular_base_no) 
##__EOS`
##
##if [ ${#REGULAR_BASE_NO} -gt 0 ]; then
##    put_log "コース受注NOに紐付く受注データが存在しません。"
##    put_log "$REGULAR_BASE_NO"
##    exit 1
##fi
## put_log "受注データ存在チェック終了"

#
#シーケンスを取得
#
SEQ=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
SELECT sequence 
    FROM dtb_regular_order_regular_id_seq;
__EOS`
put_log "シーケンス番号取得"
put_log "$SEQ"

##
## 定期受注情報の登録
##
mysql -h $SERVER -u $USER -p$PASSWD $DATABASE  <<__EOS

SET @regular_id_seq := $SEQ;
INSERT INTO dtb_regular_order (
    regular_id
   ,customer_id
   ,regular_base_no
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
   ,next_ship_date
   ,after_next_ship_date
   ,shipment_cd
   ,deliv_id
   ,box_size
   ,time_id
   ,remarks
   ,include_kbn
   ,payment_id
   ,deliv_fee
   ,buy_num
   ,order_id
   ,send_flg
   ,recv_date
   ,del_flg
   ,create_date
   ,update_date
)
SELECT
    @regular_id_seq := @regular_id_seq + 1 AS regular_id
   ,CS.customer_id
   ,IM.regular_base_no
   ,IM.order_date
   ,IM.order_name
   ,IM.order_kana
   ,IM.order_tel
   ,IM.order_zip
   ,PR.id AS order_pref
   ,IM.order_addr01
   ,IM.order_addr02
   ,IM.order_addr_kana
   ,IM.status
   ,IM.next_ship_date
   ,IM.after_next_ship_date
   ,IM.shipment_cd
   ,CASE
        WHEN IM.box_size = $DELIV_BOX_ID_MAIL
            THEN $DELIV_ID_YAMATO_MAIL
        WHEN IM.deliv_id = $INOS_DELIV_ID_YAMATO
            THEN $DELIV_ID_YAMATO
        WHEN IM.deliv_id = $INOS_DELIV_ID_SAGAWA
            THEN $DELIV_ID_SAGAWA
        ELSE "" END AS deliv_id
   ,IM.box_size
   ,IM.time_id
   ,IM.remarks
   ,IM.include_kbn
   ,IM.payment_id
   ,IM.deliv_fee
   ,IM.buy_num
   ,IM.order_id
   ,1
   ,now()
   ,IM.del_flg
   ,IM.order_date
   ,IM.update_date
FROM
    dtb_regular_inos_import IM
    LEFT JOIN dtb_customer CS
        ON CS.customer_cd = IM.customer_cd
    LEFT JOIN mtb_zip ZP
        ON ZP.zipcode = replace(IM.order_zip, '-', '')
    LEFT JOIN mtb_pref PR
        ON PR.name = ZP.state
GROUP BY IM.regular_base_no
;

INSERT INTO dtb_regular_order_detail (
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
   ,cancel_date
   ,cancel_reason_cd
   ,del_flg
   ,create_date
   ,update_date
)
SELECT
    RH.regular_id
   ,IM.dtl_line_no
   ,PC.product_id
   ,PC.product_class_id
   ,IM.dtl_product_name
   ,IM.dtl_price
   ,IM.dtl_quantity
   ,IM.dtl_todoke_kbn
   ,IM.dtl_todoke_day
   ,IM.dtl_todoke_week
   ,IM.dtl_todoke_week2
   ,IM.dtl_course_cd
   ,IM.dtl_status
   ,IM.dtl_next_arrival_date
   ,IM.dtl_after_next_arrival_date
   ,IM.dtl_cancel_date
   ,IM.dtl_cancel_reason_cd
   ,IM.del_flg
   ,IM.order_date
   ,IM.update_date
FROM
    dtb_regular_inos_import IM
    INNER JOIN dtb_regular_order RH
        ON RH.regular_base_no = IM.regular_base_no
    LEFT JOIN dtb_products_class PC
        ON PC.product_code = IM.dtl_product_code
            AND PC.del_flg = 0
;

UPDATE dtb_regular_order_regular_id_seq
   SET sequence = (SELECT MAX(regular_id)
                    FROM dtb_regular_order)
;
__EOS

put_log "定期受注情報テーブルへデータ移行が完了しました"

#
# fin.
#

