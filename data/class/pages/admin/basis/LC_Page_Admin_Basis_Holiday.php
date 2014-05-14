<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 定休日管理のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Holiday.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Basis_Holiday extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/holiday.tpl';
        $this->tpl_subno = 'holiday';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '定休日管理';
        $this->tpl_mainno = 'basis';
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
        $objDb = new SC_Helper_DB_Ex();

        $objDate = new SC_Date_Ex();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        $mode = $this->getMode();

        if (!empty($_POST)) {

            $objFormParam = new SC_FormParam_Ex();
            $this->lfInitParam($mode, $objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $holiday_id = $objFormParam->getValue('holiday_id');

            $this->arrErr = $this->lfCheckError($mode, $objFormParam);
            if (!empty($this->arrErr['holiday_id'])) {
                SC_Utils_Ex::sfDispException();
                return;
            }

            $post = $objFormParam->getHashArray();
        }

        // 要求判定
        switch($mode) {
        // 編集処理
        case 'edit':
            // POST値の引き継ぎ
            $this->arrForm = $this->arrForm = $_POST;

            if(count($this->arrErr) <= 0) {
                // 新規作成
                if($post['holiday_id'] == "") {
                    $this->lfInsertClass($this->arrForm, $_SESSION['member_id']);
                }
                // 既存編集
                else {
                    $this->lfUpdateClass($this->arrForm, $post['holiday_id']);
                }
                // 再表示
                $this->objDisplay->reload();
            } else {
                // POSTデータを引き継ぐ
                $this->tpl_holiday_id = $post['holiday_id'];
            }
            break;
        // 削除
        case 'delete':
            $objDb->sfDeleteRankRecord("dtb_holiday", "holiday_id", $post['holiday_id'], "", true);
            // 再表示
            $this->objDisplay->reload();
            break;
        // 編集前処理
        case 'pre_edit':
            // 編集項目を取得する。
            $arrHolidayData = $this->lfGetHolidayDataByHolidayID($post['holiday_id']);

            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['title'] = $arrHolidayData[0]['title'];
            $this->arrForm['month'] = $arrHolidayData[0]['month'];
            $this->arrForm['day'] = $arrHolidayData[0]['day'];
            // POSTデータを引き継ぐ
            $this->tpl_holiday_id = $post['holiday_id'];
        break;
        case 'down':
            $objDb->sfRankDown("dtb_holiday", "holiday_id", $post['holiday_id']);
            // 再表示
            $this->objDisplay->reload();
            break;
        case 'up':
            $objDb->sfRankUp("dtb_holiday", "holiday_id", $post['holiday_id']);
            // 再表示
            $this->objDisplay->reload();
            break;
        default:
            break;
        }

        $this->arrHoliday = $this->lfGetHolidayList();
        // POSTデータを引き継ぐ
        $this->tpl_holiday_id = $holiday_id;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfGetHolidayDataByHolidayID($holiday_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    title,
    month,
    day
FROM
    dtb_holiday
WHERE
    holiday_id = "$holiday_id"
EOF;

        return $objQuery->getAll($sql);
    }

    function lfGetHolidayList() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    holiday_id,
    title, 
    month,
    day
FROM
    dtb_holiday
WHERE
    del_flg <> 1
ORDER BY rank DESC
EOF;
        return $objQuery->getAll($sql);
    }

    /* DBへの挿入 */
    function lfInsertClass($arrData, $member_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // INSERTする値を作成する。
        $sqlval['title'] = $arrData['title'];
        $sqlval['month'] = $arrData['month'];
        $sqlval['day'] = $arrData['day'];
        $sqlval['creator_id'] = $member_id;
        $sqlval['rank'] = $objQuery->max('rank', "dtb_holiday") + 1;
        $sqlval['update_date'] = "Now()";
        $sqlval['create_date'] = "Now()";
        // INSERTの実行
        $sqlval['holiday_id'] = $objQuery->nextVal('dtb_holiday_holiday_id');
        $ret = $objQuery->insert("dtb_holiday", $sqlval);
        return $ret;
    }

    /* DBへの更新 */
    function lfUpdateClass($arrData) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEする値を作成する。
        $sqlval['title'] = $arrData['title'];
        $sqlval['month'] = $arrData['month'];
        $sqlval['day'] = $arrData['day'];
        $sqlval['update_date'] = "Now()";
        $where = "holiday_id = ?";
        // UPDATEの実行
        $ret = $objQuery->update("dtb_holiday", $sqlval, $where, array($arrData['holiday_id']));
        return $ret;
    }

    function lfInitParam($mode, &$objFormParam)
    {
        switch ($mode) {
            case 'edit':
                $objFormParam->addParam('タイトル', 'title', STEXT_LEN, 'KVa', array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
                $objFormParam->addParam('月', 'month', INT_LEN, 'n', array("SELECT_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
                $objFormParam->addParam('日', 'day', INT_LEN, 'n', array("SELECT_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
                // breakしない
            case 'delete':
            case 'pre_edit':
            case 'down':
            case 'up':
                $objFormParam->addParam('定休日ID', 'holiday_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;
            default:
                break;
        }
    }

    /**
     * 入力エラーチェック
     *
     * @param string $mode
     * @return array
     */
    function lfCheckError($mode, &$objFormParam) {
        $objFormParam->convParam();
        $arrErr = $objFormParam->checkError();
        $post = $objFormParam->getHashArray();

        if(!isset($arrErr['date'])) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $where = "del_flg = 0 AND month = ? AND day = ?";
            $arrval = array($post['month'], $post['day']);
            if (!empty($post['holiday_id'])) {
                $where .= " AND holiday_id <> ?";
                $arrval[] = $post['holiday_id'];
            }
            $arrRet = $objQuery->select("count(holiday_id) as count", "dtb_holiday", $where, $arrval);

            // 編集中のレコード以外に同じ日付が存在する場合
            if ($arrRet[0]['count'] > 0) {
                $arrErr['date'] = "※ 既に同じ日付の登録が存在します。<br>";
            }
        }
        return $arrErr;
    }
}
?>
