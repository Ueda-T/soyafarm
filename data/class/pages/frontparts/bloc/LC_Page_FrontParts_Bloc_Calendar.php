<?php

// {{{ requires
$current_dir = realpath(dirname(__FILE__));
define('CALENDAR_ROOT', DATA_REALDIR.'module/Calendar'.DIRECTORY_SEPARATOR);
require_once $current_dir . '/../../../../module/Calendar/Month/Weekdays.php';
require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';

/**
 * Calendar のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $ $
 */
class LC_Page_FrontParts_Bloc_Calendar extends LC_Page_FrontParts_Bloc {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        // 休日取得取得
        $this->arrHoliday = $this->lfGetHoliday();
        // 定休日取得取得
        $this->arrRegularHoliday = $this->lfGetRegularHoliday();
        // カレンダーデータ取得
        $this->arrCalendar = $this->lfGetCalendar(2);
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
     * カレンダー情報取得.
     *
     * @param integer $disp_month 表示する月数
     * @return array $arrCalendar カレンダー情報の配列を返す
     */
    function lfGetCalendar($disp_month = 1){

        for ($j = 0; $j <= $disp_month-1; ++$j) {
            $year = date('Y');
            $month = date('n') + $j;
            if ($month > 12) {
                $month = $month%12;
                $year = $year + $month%12;
            }

            $objMonth = new Calendar_Month_Weekdays($year, $month, 0);
            $objMonth->build();
            $i = 0;
            while ($objDay = $objMonth->fetch()) {
                if ($month == $objDay->month) {
                    $arrCalendar[$j][$i]['in_month'] = true;
                } else {
                    $arrCalendar[$j][$i]['in_month'] = false;
                }
                $arrCalendar[$j][$i]['first'] = $objDay->first;
                $arrCalendar[$j][$i]['last'] = $objDay->last;
                $arrCalendar[$j][$i]['empty'] = $objDay->empty;
                $arrCalendar[$j][$i]['year'] = $year;
                $arrCalendar[$j][$i]['month'] = $month;
                $arrCalendar[$j][$i]['day'] = $objDay->day;
                if ($this->lfCheckHoliday($year, $month, $objDay->day)) {
                    $arrCalendar[$j][$i]['holiday'] = true;
                } else {
                    $arrCalendar[$j][$i]['holiday'] = false;
                }
                ++$i;
            }
        }

        return $arrCalendar;
    }

    /**
     * 休日取得.
     *
     * @return array $arrHoliday 休日情報の配列を返す
     */
    function lfGetHoliday() {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    month,
    day
FROM
    dtb_holiday
WHERE
    del_flg <> 1
ORDER BY rank DESC
EOF;
        $arrRet = $objQuery->getAll($sql);


        foreach ($arrRet AS $key=>$val) {
            $arrHoliday[$val['month']][] = $val['day'];
        }
        return $arrHoliday;
    }

    /**
     * 定休日取得.
     *
     * @return array $arrRegularHoliday 定休日情報の配列を返す
     */
    function lfGetRegularHoliday() {
        $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $arrRegularHoliday = explode('|', $arrInfo['regular_holiday_ids']);
        return $arrRegularHoliday;
    }

    /**
     * 休日チェック取得.
     *
     * @param integer $year 年
     * @param integer $month 月
     * @param integer $day 日
     * @return boolean 休日の場合trueを返す
     */
    function lfCheckHoliday($year, $month, $day) {
        if (!empty($this->arrHoliday[$month])) {
            if (in_array($day, $this->arrHoliday[$month])) {
                return true;
            }
        }
        if (!empty($this->arrRegularHoliday)) {
            $day = date('w', mktime(0,0,0 ,$month, $day, $year));
            if (in_array($day, $this->arrRegularHoliday)) {
                return true;
            }
        }
        return false;
    }

}
?>
