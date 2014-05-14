#!/bin/sh

SERVER="localhost"
## XXX
#DATABASE=rohto
DATABASE=rohto
USER=rohto
PASSWD=rohto

LOG_FILE="./order_import.log"

put_log() {
    DATE=`date '+%Y/%m/%d %H:%M:%S'`
    echo "$DATE : $1" >> $LOG_FILE
}
put_log "処理開始"


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
TRUNCATE TABLE dtb_order_inos_import;
__EOS
put_log "受注インポートテーブルを初期化しました"

#
#CSVファイルからデータを登録
#
mysql -h $SERVER -u $USER -p$PASSWD $DATABASE  <<__EOS
SET CHARACTER_SET_DATABASE=cp932;

LOAD DATA LOCAL INFILE '$FILE'
    INTO TABLE dtb_order_inos_import 
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n'

SET
    shipping_kana = NULLIF(shipping_kana, ''),
    shipping_addr_kana = NULLIF(shipping_addr_kana, ''),
    shipping_addr02 = NULLIF(shipping_addr02, ''),
    shipping_date = NULLIF(shipping_date, '0000-00-00 00:00:00'),
    note = NULLIF(note, ''),
    shipping_num = NULLIF(shipping_num, ''),
    web_order_no = NULLIF(web_order_no, ''),
    memo04 = NULLIF(memo04, ''),
    promotion_cd = NULLIF(promotion_cd, '')
;
__EOS
put_log "受注インポートテーブルへ登録が完了しました"

##
## レコードの重複チェック
## (コース受注NOと行NOが重複しているレコードはエラーとする)
##
#BASE_CD=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
#SELECT
#    CONCAT("受注NO：",IM.order_base_no) AS order_base_no
#   ,CONCAT("行NO：", IM.line_no) AS line_no 
#FROM
#    dtb_order_inos_import IM
#GROUP BY IM.order_base_no, IM.line_no
#HAVING COUNT(IM.order_base_no) <> 1
#__EOS`
#
#if [ ${#BASE_CD} -gt 0 ]; then
#    put_log "受注NO、行NOが重複するデータが存在します。"
#    put_log "$BASE_CD"
#    exit 1
#fi

##
## 顧客データ存在チェック
## (顧客CDに紐付く顧客データが顧客マスタに存在しない場合はエラー)
##
#CUSTOMER_CD=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
#SELECT
#    IM.customer_cd
#FROM
#    dtb_order_inos_import IM
#WHERE NOT EXISTS 
#    (SELECT * FROM dtb_customer C 
#        WHERE C.customer_cd = IM.customer_cd
#          AND C.customer_id = IM.web_customer_id) 
#__EOS`
#
#if [ ${#CUSTOMER_CD} -gt 0 ]; then
#    put_log "顧客マスタに顧客データが存在しません。"
#    put_log "$CUSTOMER_CD"
#    exit 1
#fi

#
#シーケンスを取得
#
SEQ_ORDER=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
SELECT sequence FROM dtb_order_order_id_seq;
__EOS`
SEQ_ORDER_DTL=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
SELECT sequence FROM dtb_order_detail_order_detail_id_seq;
__EOS`

###基幹宅配便CD定義
#ヤマト
INOS_DELIV_ID_YAMATO=0
#佐川
INOS_DELIV_ID_SAGAWA=1

###WEB配送業者ID
#ヤマト宅配便
DELIV_ID_YAMATO=1
#佐川宅配便
DELIV_ID_SAGAWA=2
#ヤマトメール便
DELIV_ID_YAMATO_MAIL=3

###箱ID
#宅配便
DELIV_BOX_ID_TAKUHAI=0
#メール便
DELIV_BOX_ID_MAIL=1

#
#受注情報,配送情報,受注詳細情報を登録
#
mysql -h $SERVER -u $USER -p$PASSWD $DATABASE  <<__EOS

SET @i := ${SEQ_ORDER};
SET @k := ${SEQ_ORDER_DTL};

INSERT INTO dtb_order (
    order_id
   ,order_base_no
   ,customer_id
   ,order_name
   ,order_kana
   ,order_email
   ,order_tel
   ,order_zip
   ,order_pref
   ,order_addr01
   ,order_addr02
   ,order_sex
   ,order_birth
   ,subtotal
   ,deliv_id
   ,deliv_box_id
   ,deliv_fee
   ,use_point
   ,add_point
   ,tax
   ,total
   ,payment_total
   ,payment_id
   ,payment_method
   ,note
   ,status
   ,create_date
   ,update_date
   ,commit_date
   ,device_type_id
   ,include_kbn
   ,send_flg
   ,recv_date
   ,purchase_motive_code
   ,input_assistance_code
   ,event_code
   ,regular_base_no
   ,return_num
   ,return_amount
   ,del_flg
   ,memo04
)
SELECT
    (@i := @i + 1) AS order_id
   ,IM.order_base_no
   ,CS.customer_id
   ,CS.name
   ,CS.kana
   ,CS.email
   ,CS.tel
   ,CS.zip
   ,CS.pref
   ,CS.addr01
   ,CS.addr02
   ,CS.sex
   ,CS.birth
   ,IM.subtotal
   ,CASE WHEN IM.deliv_id = ${INOS_DELIV_ID_YAMATO}
     AND IM.deliv_box_id = ${DELIV_BOX_ID_MAIL}
    THEN ${DELIV_ID_YAMATO_MAIL}
    WHEN IM.deliv_id = ${INOS_DELIV_ID_YAMATO}
     AND IM.deliv_box_id = ${DELIV_BOX_ID_TAKUHAI}
    THEN ${DELIV_ID_YAMATO}
    WHEN IM.deliv_id = ${INOS_DELIV_ID_SAGAWA}
     AND IM.deliv_box_id = ${DELIV_BOX_ID_TAKUHAI}
    THEN ${DELIV_ID_SAGAWA} END AS deliv_id
   ,IM.deliv_box_id
   ,IM.deliv_fee
   ,IM.use_point02
   ,IM.add_point
   ,0
   ,(IM.subtotal + IM.deliv_fee) AS total
   ,IM.payment_total
   ,IM.payment_id
   ,PM.payment_method
   ,IM.note
   ,IM.status
   ,IM.create_date
   ,IM.update_date
   ,IM.commit_date
   ,DVC.id AS device_type_id
   ,IM.include_kbn
   ,1
   ,NOW()
   ,IM.purchase_motive_code
   ,IM.input_assistance_code
   ,IM.event_code
   ,IM.regular_base_no
   ,IM.return_num
   ,IM.return_amount
   ,IM.del_flg
   ,IM.memo04
FROM
    dtb_order_inos_import IM
INNER JOIN dtb_customer CS
        ON CS.customer_cd = IM.customer_cd
INNER JOIN mtb_device_type DVC
        ON IM.order_kbn = DVC.kikan_id
INNER JOIN dtb_payment PM
        ON PM.payment_id = IM.payment_id
  GROUP BY IM.order_base_no
;

UPDATE dtb_order_order_id_seq 
   SET sequence = (SELECT MAX(order_id)
                     FROM dtb_order)
;

INSERT INTO dtb_shipping (
    shipping_id
   ,order_id
   ,shipping_name
   ,shipping_kana
   ,shipping_tel
   ,shipping_pref
   ,shipping_zip
   ,shipping_addr01
   ,shipping_addr02
   ,shipping_addr_kana
   ,deliv_id
   ,time_id
   ,shipping_time
   ,shipping_num
   ,shipping_date
   ,shipping_area_code
   ,shipping_commit_date
   ,deliv_kbn
   ,cool_kbn
   ,send_mail_flg
   ,create_date
   ,update_date
   ,del_flg
)
SELECT
    0
   ,ORD.order_id
   ,IM.shipping_name
   ,IM.shipping_kana
   ,IM.shipping_tel
   ,PR.id AS shipping_pref
   ,IM.shipping_zip
   ,replace(IM.shipping_addr01, IFNULL(PR.NAME, ''), '') AS shipping_addr01
   ,IM.shipping_addr02
   ,IM.shipping_addr_kana
   ,CASE WHEN IM.deliv_id = ${INOS_DELIV_ID_YAMATO}
     AND IM.deliv_box_id = ${DELIV_BOX_ID_MAIL}
    THEN ${DELIV_ID_YAMATO_MAIL}
    WHEN IM.deliv_id = ${INOS_DELIV_ID_YAMATO}
     AND IM.deliv_box_id = ${DELIV_BOX_ID_TAKUHAI}
    THEN ${DELIV_ID_YAMATO}
    WHEN IM.deliv_id = ${INOS_DELIV_ID_SAGAWA}
     AND IM.deliv_box_id = ${DELIV_BOX_ID_TAKUHAI}
    THEN ${DELIV_ID_SAGAWA} END AS deliv_id
   ,IM.time_id
   ,(SELECT DT.deliv_time AS shipping_time
       FROM dtb_delivtime DT 
      WHERE DT.time_id = IM.time_id
        AND DT.deliv_id = CASE WHEN IM.deliv_id = ${INOS_DELIV_ID_YAMATO}
                                AND IM.deliv_box_id = ${DELIV_BOX_ID_MAIL}
                               THEN ${DELIV_ID_YAMATO_MAIL}
                               WHEN IM.deliv_id = ${INOS_DELIV_ID_YAMATO}
                                AND IM.deliv_box_id = ${DELIV_BOX_ID_TAKUHAI}
                               THEN ${DELIV_ID_YAMATO}
                               WHEN IM.deliv_id = ${INOS_DELIV_ID_SAGAWA}
                                AND IM.deliv_box_id = ${DELIV_BOX_ID_TAKUHAI}
                               THEN ${DELIV_ID_SAGAWA} END
    )
   ,IM.shipping_num
   ,IM.shipping_date
   ,IM.shipping_area_code
   ,IM.commit_date
   ,IM.deliv_kbn
   ,IM.cool_kbn
   ,IF(IM.commit_date = '0000-00-00 00:00:00', 0, 1)
   ,IM.create_date
   ,IM.update_date
   ,IM.del_flg
FROM
    dtb_order_inos_import IM
INNER JOIN dtb_order ORD
        ON ORD.order_base_no = IM.order_base_no
LEFT OUTER JOIN mtb_zip ZP
        ON ZP.zipcode = replace(IM.shipping_zip, '-', '')
LEFT OUTER JOIN mtb_pref PR
        ON PR.name = ZP.state
  GROUP BY ORD.order_base_no
;

INSERT INTO dtb_order_detail (
    order_detail_id
   ,order_id
   ,product_id
   ,product_class_id
   ,product_name
   ,product_code
   ,price
   ,quantity
   ,course_cd
   ,return_quantity
   ,sell_flg
)
SELECT
    (@k := @k + 1) AS order_detail_id
   ,ORD.order_id
   ,PC.product_id
   ,PC.product_class_id
   ,IM.product_name
   ,IM.product_code
   ,IM.price
   ,IM.quantity
   ,IM.course_cd
   ,IM.return_quantity
   ,1
FROM
    dtb_order_inos_import IM
INNER JOIN dtb_order ORD
        ON IM.order_base_no = ORD.order_base_no
LEFT OUTER JOIN dtb_products_class PC
        ON IM.product_code = PC.product_code
       AND PC.del_flg = 0
;

UPDATE dtb_order_detail_order_detail_id_seq
   SET sequence = (SELECT MAX(order_detail_id)
                     FROM dtb_order_detail)
;
__EOS

put_log "受注情報のデータ移行が完了しました"

##
##プロモーションCDが存在するレコードのみ取得
##
#RECORD=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
#    SELECT CONCAT(ORD.order_id,':',IM.promotion_cd)
#      FROM dtb_order_inos_import IM
#INNER JOIN dtb_order ORD
#        ON ORD.order_base_no = IM.order_base_no
#     WHERE IM.promotion_cd IS NOT NULL
#  GROUP BY ORD.order_base_no
#__EOS`
#
##結果を配列に
#arrRECORD=( `echo $RECORD`)
#
## レコード毎にループ
#INS_VAL=""
#for i in "${arrRECORD[@]}"
#    do
#    # 受注ID抽出
#    ORDER_ID=`echo $i |awk -F':' '{ print $1 }'`
#    # プロモーションCD抽出
#    PROMOTION_CD=(`echo $i |awk -F':' '{ print $2 }'`)
#    # プロモーションCDを配列に
#    arrPROMOTION=( `echo $PROMOTION_CD | tr -s ',' ' '`)
#
#    VALUES=""
#    for k in  "${arrPROMOTION[@]}"
#        do
#        # INSERT文のVALUES作成
#        VALUES="$VALUES""(${ORDER_ID}, '${k}'),"
#        done
#    INS_VAL="${INS_VAL}""${VALUES}"
#    echo ${i}
#    done
## 末尾のカンマ消去
#INS_VAL=`echo ${INS_VAL}|sed -e 's/,$//g'`
#
##
##受注プロモーション登録
##
#mysql -h $SERVER -u $USER -p$PASSWD $DATABASE <<__EOS
#INSERT INTO dtb_order_promotion
#    (order_id, promotion_cd) VALUES
#    ${TMP}
#__EOS
#
#echo "受注プロモーション情報の登録が完了しました"

put_log "処理終了"
#
# fin.
#

