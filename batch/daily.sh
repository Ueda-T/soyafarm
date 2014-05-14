#!/bin/sh

SERVER="localhost"
DATABASE=rohto
USER=rohto
PASSWD=rohto

#
# 前日日付を求める
#
YESTERDAY=`date -d '1 days ago' +%Y-%m-%d`

#
# 引数にて日付が指定された場合、最低限その妥当性をチェックする
#
if [ $# -gt 0 ]; then
    YESTERDAY=$1
    if ! [ `echo $1 | egrep '^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$'` ]; then
        echo "Invalid date format."
        exit 1
    fi
fi

#
# 昨日の売上及び注文件数を取得する
#
DREPORT=`mysql -h ${SERVER} -u ${USER} -p${PASSWD} ${DATABASE}  <<__EOS | awk '1 < NR {print}'
select sum(total) as amount
     , count(total) as cnt
  from dtb_order
 where del_flg = 0
   and create_date between '${YESTERDAY} 00:00:00' and '${YESTERDAY} 23:59:59'
   and status <> 6
__EOS
`

#
# 月集計範囲の設定
#
MONTH=`echo $YESTERDAY | sed -e "s/^\([0-9]\{4\}\)-\([0-9]\{2\}\)-\([0-9]\{2\}\)/\1-\2/g"`
FROM="${MONTH}-01 00:00:00"
TO=`date +'%Y-%m-%d' -d "1 day ago \`date -d "${MONTH}-01 1 month" '+%F'\`"`" 23:59:59"

#
# 今月の売上及び注文件数を取得する
#
MREPORT=`mysql -h ${SERVER} -u ${USER} -p${PASSWD} ${DATABASE}  <<__EOS | awk '1 < NR {print}'
select sum(total) as amount
     , count(total) as cnt
  from dtb_order
 where del_flg = 0
   and create_date between '${FROM}' and '${TO}'
   and status <> 6
__EOS
`

DAMOUNT=`echo ${DREPORT} | awk '{print $1}'`
DCOUNT=`echo ${DREPORT} | awk '{print $2}'`
MAMOUNT=`echo ${MREPORT} | awk '{print $1}'`
MCOUNT=`echo ${MREPORT} | awk '{print $2}'`
mysql -h ${SERVER} -u ${USER} -p${PASSWD} ${DATABASE}  <<__EOS
update dtb_summary
   set damount=${DAMOUNT}
     , dcount=${DCOUNT}
     , mamount=${MAMOUNT}
     , mcount=${MCOUNT}
;
__EOS

exit 0