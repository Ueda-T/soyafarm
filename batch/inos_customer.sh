#!/bin/sh

SERVER="localhost"
DATABASE=rohto
USER=rohto
PASSWD=rohto

LOG_FILE="./customer_import.log"

put_log() {
    DATE=`date '+%Y/%m/%d %H:%M:%S'`
    echo "$DATE : $1" >> $LOG_FILE
}
put_log "処理開始"

#
# 引数が存在する場合は整合性チェック
#
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
TRUNCATE TABLE dtb_customer_inos_import;
__EOS
put_log "顧客インポートテーブルを初期化しました"

#
#CSVファイルからデータを登録
#
mysql -h $SERVER -u $USER -p$PASSWD $DATABASE  <<__EOS
set character_set_database=cp932;
LOAD DATA LOCAL INFILE '$FILE' INTO TABLE dtb_customer_inos_import 
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n'

SET customer_cd = nullif(customer_cd, ''),
    kana = nullif(kana, ''),
    name = nullif(name, ''),
    tel = nullif(tel, ''),
    tel2 = nullif(tel2, ''),
    zip = nullif(zip, ''),
    addr_kana = nullif(addr_kana, ''),
    addr01 = nullif(addr01, ''),
    addr02 = nullif(addr02, ''),
    email = nullif(email, ''),
    birth = nullif(birth, '0000-00-00'),
    point_valid_date = nullif(point_valid_date, '0000-00-00'),
    create_date = nullif(create_date, '0000-00-00 00:00:00'),
    update_date = nullif(update_date, '0000-00-00 00:00:00'),
    salt = substring(md5(rand()), 1, 10),
    password = substring(md5(rand()), 1, 30)
;
__EOS
put_log "顧客インポートテーブルへ登録が完了しました"

##
## 郵便番号データ存在チェック
##
ZIP=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE --default-character-set=utf8 <<__EOS
    UPDATE dtb_customer_inos_import T1
INNER JOIN  (SELECT IM.customer_cd
               FROM dtb_customer_inos_import IM
              WHERE NOT EXISTS 
            (SELECT *
               FROM mtb_zip ZP
         INNER JOIN mtb_pref PR
                 ON PR.name = ZP.state
              WHERE ZP.zipcode = replace(IM.zip, '-', '')
            )) T2
        ON T1.CUSTOMER_CD = T2.CUSTOMER_CD
    SET T1.ERROR_FLG = 0
      , T1.ERROR_NAME = '都道府県コードが存在しません'
__EOS`

#
#顧客のユニークチェック
#
EMAIL01=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N  --default-character-set=utf8 <<__EOS
    UPDATE dtb_customer_inos_import T1
INNER JOIN (SELECT EMAIL
                     FROM dtb_customer_inos_import
                    WHERE DEL_FLG = 0
                 GROUP BY EMAIL
                   HAVING COUNT(EMAIL) > 1) T2
        ON T1.EMAIL = T2.EMAIL
       SET ERROR_FLG = 1
         , ERROR_NAME = 'メール重複エラー'
__EOS`

#EMAIL02=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
#SELECT IMP.EMAIL FROM dtb_customer_inos_import IMP
#INNER JOIN dtb_customer CUS
#        ON CUS.EMAIL = IMP.EMAIL
#__EOS`
#if [ ${#EMAIL01} -gt 0 -o ${#EMAIL02} -gt 0 ]; then
#CUSTOMER_CD01=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
#  SELECT IMP.CUSTOMER_CD
#    FROM dtb_customer_inos_import IMP
#GROUP BY IMP.CUSTOMER_CD
#  HAVING COUNT(IMP.CUSTOMER_CD) > 1
#__EOS`
#CUSTOMER_CD02=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N <<__EOS
#SELECT IMP.CUSTOMER_CD FROM dtb_customer_inos_import IMP
#INNER JOIN dtb_customer CUS
#        ON CUS.CUSTOMER_CD = IMP.CUSTOMER_CD
#__EOS`
#if [ ${#CUSTOMER_CD01} -gt 0 -o ${#CUSTOMER_CD02} -gt 0 ]; then
#    put_log "基幹顧客CDが重複しています。"
#    put_log "$CUSTOMER_CD01"
#    put_log "$CUSTOMER_CD02"
#    exit 1
#fi

#
#必須チェック
#
RECORD=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE  --default-character-set=utf8 <<__EOS
SELECT IMP.customer_cd
     , IMP.kana
     , IMP.name
     , IMP.tel
     , IMP.zip
     , IMP.addr01
     , IMP.email
     , IMP.sex
     , IMP.customer_kbn
     , IMP.dm_flg
     , IMP.tel_flg
     , IMP.mailmaga_flg
     , IMP.privacy_kbn
     , IMP.kashidaore_kbn
     , IMP.point
     , IMP.del_flg
     , IMP.create_date
     , IMP.update_date
  FROM dtb_customer_inos_import IMP
 WHERE IMP.customer_cd IS NULL
    OR IMP.kana IS NULL
    OR IMP.name IS NULL
    OR IMP.tel IS NULL
    OR IMP.zip IS NULL
    OR IMP.addr01 IS NULL
    OR IMP.email IS NULL
    OR IMP.sex NOT IN (0,1,2)
    OR IMP.customer_kbn NOT IN (0,1,2)
    OR IMP.dm_flg NOT IN (0,1)
    OR IMP.tel_flg NOT IN (0,1)
    OR IMP.mailmaga_flg NOT IN (0,1)
    OR IMP.privacy_kbn NOT IN (0,1)
    OR IMP.kashidaore_kbn NOT IN (0,1)
    OR IMP.point IS NULL
    OR IMP.del_flg NOT IN (0,1)
    OR IMP.create_date IS NULL
    OR IMP.update_date IS NULL
    \G
__EOS`
if [ ${#RECORD} -gt 0 ]; then
    put_log "必須項目が空,もしくはデータが正しくありません。"
    put_log "$RECORD"
    exit 1
fi
put_log "チェック処理が完了しました"

#
#シーケンスを取得
#
SEQ=`mysql -h $SERVER -u $USER -p$PASSWD $DATABASE -N  --default-character-set=utf8 <<__EOS
SELECT sequence FROM dtb_customer_customer_id_seq;
__EOS`

#
#顧客インポートテーブルから顧客テーブルへ移行
#
mysql -h $SERVER -u $USER -p$PASSWD $DATABASE  --default-character-set=utf8 <<__EOS
SET @i := ${SEQ};
SET @k := ${SEQ};
INSERT INTO dtb_customer (
    CUSTOMER_CD
  , KANA
  , NAME
  , TEL
  , TEL2
  , ZIP
  , PREF
  , ADDR_KANA
  , ADDR01
  , ADDR02
  , EMAIL
  , BIRTH
  , SEX
  , CUSTOMER_KBN
  , DM_FLG
  , TEL_FLG
  , MAILMAGA_FLG
  , PRIVACY_KBN
  , KASHIDAORE_KBN
  , POINT
  , POINT_VALID_DATE
  , TORIHIKI_ID
  , CUSTOMER_ID
  , CUSTOMER_STATUS_CD
  , DEL_FLG
  , CREATE_DATE
  , UPDATE_DATE
  , SECRET_KEY
  , PASSWORD
  , SALT)
SELECT 
    IMP.CUSTOMER_CD
  , IMP.KANA
  , IMP.NAME
  , replace(IMP.TEL, '-', '')
  , replace(IMP.TEL2, '-', '')
  , IMP.ZIP
  , PRE.ID AS PREF
  , IMP.ADDR_KANA
  , replace(IMP.ADDR01, IFNULL(PRE.NAME, ''), '') AS ADDR01
  , IFNULL(IMP.ADDR02, '番地なし') AS ADDR02
  , IMP.EMAIL
  , IMP.BIRTH
  , IMP.SEX
  , IMP.CUSTOMER_KBN
  , IMP.DM_FLG
  , IMP.TEL_FLG
  , IMP.MAILMAGA_FLG
  , IMP.PRIVACY_KBN
  , IMP.KASHIDAORE_KBN
  , IMP.POINT
  , IMP.POINT_VALID_DATE
  , IMP.TORIHIKI_ID
  , (@i := @i + 1) AS CUSTOMER_ID
  , IMP.CUSTOMER_STATUS_CD
  , IMP.DEL_FLG
  , IMP.CREATE_DATE
  , IMP.UPDATE_DATE
  , CONCAT('r1', LPAD((@k := @k + 1), 20, 0)) AS SECRET_KEY
  , IMP.PASSWORD
  , IMP.SALT
FROM dtb_customer_inos_import IMP
LEFT OUTER JOIN mtb_zip MZIP
   ON MZIP.ZIPCODE = replace(IMP.ZIP, '-', '')
LEFT OUTER JOIN mtb_pref PRE
   ON PRE.NAME = MZIP.STATE
WHERE IMP.CUSTOMER_CD IS NOT NULL
  AND IMP.ERROR_FLG = 0
GROUP BY IMP.CUSTOMER_CD
;

UPDATE dtb_customer_customer_id_seq
   SET sequence = (SELECT MAX(customer_id)
                     FROM dtb_customer)
;

__EOS
put_log "顧客テーブルへデータ移行が完了しました"

put_log "処理終了"
#
# fin.
#
