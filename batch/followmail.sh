#!/bin/sh

# cron設定時は以下のようにcrontabに記述する
#0 12 * * * (絶対パス)htdocs/batch/followmail.sh >/dev/null 2>&1 


#
# 当日日付を求める
#

TODAY=`date +"%Y-%m-%d"`

# 引数が存在する場合は整合性チェック
if [ $# -gt 0 ]; then
    TODAY=$1
    if ! [ `echo $TODAY | egrep '^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$'` ]; then
        echo "日付フォーマットが正しくありません。"
        exit 1
    fi
fi

#
# フォローメール送信起動
#

/usr/bin/php -f $HOME/htdocs/batch/followmail.php $TODAY


#
# fin.
#

