<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 会員規約設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Kiyaku.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Basis_Kiyaku extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/kiyaku.tpl';
        $this->tpl_subno = 'kiyaku';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '会員規約設定';
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

        $mode = $this->getMode();

        if (!empty($_POST)) {
            $objFormParam = new SC_FormParam_Ex();
            $this->lfInitParam($mode, $objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $kiyaku_id = $objFormParam->getValue('kiyaku_id');

            $this->arrErr = $this->lfCheckError($mode, $objFormParam);
            if (!empty($this->arrErr['kiyaku_id'])) {
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
            $this->arrForm = $_POST;

            if(count($this->arrErr) <= 0) {
                if($post['kiyaku_id'] == "") {
                    $this->lfInsertClass($this->arrForm, $_SESSION['member_id']);    // 新規作成
                } else {
                    $this->lfUpdateClass($this->arrForm, $post['kiyaku_id']);    // 既存編集
                }
                // 再表示
                $this->objDisplay->reload();
            } else {
                // POSTデータを引き継ぐ
                $this->tpl_kiyaku_id = $post['kiyaku_id'];
            }
            break;
        // 削除
        case 'delete':
            $objDb->sfDeleteRankRecord("dtb_kiyaku", "kiyaku_id", $post['kiyaku_id'], "", true);
            // 再表示
            $this->objDisplay->reload();
            break;
        // 編集前処理
        case 'pre_edit':
            // 編集項目を取得する。
            $arrKiyakuData = $this->lfGetKiyakuDataByKiyakuID($post['kiyaku_id']);

            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['kiyaku_title'] = $arrKiyakuData[0]['kiyaku_title'];
            $this->arrForm['kiyaku_text'] = $arrKiyakuData[0]['kiyaku_text'];
            // POSTデータを引き継ぐ
            $this->tpl_kiyaku_id = $post['kiyaku_id'];
        break;
        case 'down':
            $objDb->sfRankDown("dtb_kiyaku", "kiyaku_id", $post['kiyaku_id']);
            // 再表示
            $this->objDisplay->reload();
            break;
        case 'up':
            $objDb->sfRankUp("dtb_kiyaku", "kiyaku_id", $post['kiyaku_id']);
            // 再表示
            $this->objDisplay->reload();
            break;
        default:
            break;
        }

        $this->arrKiyaku = $this->lfGetKiyakuList();
        // POSTデータを引き継ぐ
        $this->tpl_kiyaku_id = $kiyaku_id;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* DBへの挿入 */
    function lfInsertClass($arrData, $member_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // INSERTする値を作成する。
        $sqlval['kiyaku_title'] = $arrData['kiyaku_title'];
        $sqlval['kiyaku_text'] = $arrData['kiyaku_text'];
        $sqlval['creator_id'] = $member_id;
        $sqlval['rank'] = $objQuery->max('rank', "dtb_kiyaku") + 1;
        $sqlval['update_date'] = "Now()";
        $sqlval['create_date'] = "Now()";
        // INSERTの実行
        $sqlval['kiyaku_id'] = $objQuery->nextVal('dtb_kiyaku_kiyaku_id');
        $ret = $objQuery->insert("dtb_kiyaku", $sqlval);
        return $ret;
    }

    function lfGetKiyakuDataByKiyakuID($kiyaku_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    kiyaku_title,
    kiyaku_text
FROM
    dtb_kiyaku
WHERE
    kiyaku_id = "$kiyaku_id"
ORDER BY rank DESC
EOF;

        return $objQuery->getAll($sql);
    }

    function lfGetKiyakuList() {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    kiyaku_title,
    kiyaku_text,
    kiyaku_id
FROM
    dtb_kiyaku
WHERE
    del_flg <> 1
ORDER BY rank DESC
EOF;

        return $objQuery->getAll($sql);
    }

    /* DBへの更新 */
    function lfUpdateClass($arrData, $kiyaku_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEする値を作成する。
        $sqlval['kiyaku_title'] = $arrData['kiyaku_title'];
        $sqlval['kiyaku_text'] = $arrData['kiyaku_text'];
        $sqlval['update_date'] = "Now()";
        $where = "kiyaku_id = ?";
        // UPDATEの実行
        $ret = $objQuery->update("dtb_kiyaku", $sqlval, $where, array($kiyaku_id));
        return $ret;
    }

    function lfInitParam($mode, &$objFormParam) {
        switch ($mode) {
            case 'edit':
                $objFormParam->addParam('規約タイトル', 'kiyaku_title', SMTEXT_LEN, 'KVa', array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
                $objFormParam->addParam('規約内容', 'kiyaku_text', MLTEXT_LEN, 'KVa', array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
            case 'delete':
            case 'pre_edit':
            case 'down':
            case 'up':
                $objFormParam->addParam('規約ID', 'kiyaku_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
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
    function lfCheckError($mode, $objFormParam) {
        $arrErr = $objFormParam->checkError();
        if(!isset($arrErr['name']) && $mode == 'edit') {

            $post = $objFormParam->getHashArray();
            $kiyaku_title = $post['kiyaku_title'];

            $objQuery =& SC_Query_Ex::getSingletonInstance();

            $sql =<<<EOF
SELECT
    kiyaku_id,
    kiyaku_title
FROM
    dtb_kiyaku
WHERE
    del_flg = 0
    AND kiyaku_title = "$kiyaku_title"
EOF;
            $arrRet = $objQuery->getAll($sql);

            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrRet[0]['kiyaku_id'] != $post['kiyaku_id'] 
                && $arrRet[0]['kiyaku_title'] == $post['kiyaku_title']) {

                $arrErr['name'] = "※ 既に同じ内容の登録が存在します。<br>";
            }
        }
        return $arrErr;
    }
}
?>
