#!/bin/sh

# cron設定時は以下のようにcrontabに記述する
#0 6 * * * /home/soyafarm/htdocs/batch/inos_export.sh >/dev/null 2>&1 


#
# 当日日付を求める
#

TODAY=`date +"%Y-%m-%d"`

# 引数が存在する場合は整合性チェック
#if [ $# -gt 0 ]; then
#    TODAY=$1
#    if ! [ `echo $TODAY | egrep '^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$'` ]; then
#        echo "日付フォーマットが正しくありません。"
#        exit 1
#    fi
#fi

#
# 顧客情報エクスポート起動
#

/usr/bin/php -f $HOME/htdocs/batch/inos_export_customer.php

#
# 受注・定期情報エクスポート起動
#

/usr/bin/php -f $HOME/htdocs/batch/inos_export_order_regular.php


#
# fin.
#

