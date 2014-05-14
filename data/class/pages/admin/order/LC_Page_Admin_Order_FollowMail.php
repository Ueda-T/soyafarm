<?php
// {{{ requires
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');

/**
 * フォローメールマスタ登録 のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Order_FollowMail.php 439 2014-01-16 02:00:30Z taizo $
 */
class LC_Page_Admin_Order_FollowMail extends LC_Page_Admin_Ex
{
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/follow_mail.tpl';

        $this->tpl_complete = 'order/follow_mail_complete.tpl';
        $this->tpl_confirm = 'order/follow_mail_confirm.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'follow_mail_search';
        $this->tpl_maintitle = 'フォローメール管理';
        $this->tpl_subtitle = 'フォローメールマスタ登録';

        $this->arrFollowMailStatus = array(1 => '有効', 0 => '停止');
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
        $objFormParam = new SC_FormParam_Ex();
        $target_product = array();
        // パラメーター初期化, 取得
        $this->lfInitFormParam($objFormParam, $_POST);
        $this->arrForm = $objFormParam->getHashArray();

        // 検索パラメーター引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();

        $mode = $this->getMode();
        switch($mode) {
        case 'edit':
            // エラーチェック
            $this->arrErr = $this->lfCheckError($objFormParam, $_POST);

            if (count($this->arrErr) == 0) {
                // 確認画面表示設定
                $this->tpl_mainpage = $this->tpl_confirm;
            }
            break;
        case 'complete':
            // エラーチェック
            $this->arrErr = $this->lfCheckError($objFormParam, $_POST);

            if (count($this->arrErr) == 0) {
                // DBへデータ登録
                $follow_id = $this->lfRegistFollowMail($this->arrForm);
                // 完了画面表示設定
                $this->tpl_mainpage = $this->tpl_complete;
            }
            break;

        // 確認ページからの戻り
        case 'confirm_return':
            break;
        default:
            if ($this->arrForm['follow_id'] != "") {
                /* 編集モード */
                $this->arrForm =
                    $this->lfGetFollowMail($this->arrForm['follow_id']);
                // brタグを改行コードに変換
                $this->arrForm['mail_body'] = str_replace(array('<br />','<br>'), "\n", $this->arrForm['mail_body']);
            }
            break;
        }
        // フォローメール商品情報取得
        $this->arrFollowMailProducts = $this->lfGetFollowMailProducts($this->arrForm['follow_id']);
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
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return void
     */
    function lfInitFormParam(&$objFormParam, $arrPost) {
        $objFormParam->addParam
            ("フォローメールID", "follow_id", 4, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("商品番号", "product_code", 4, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("フォローメールコード", "follow_code", 10, 'n',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("フォローメール名", 'follow_name', 40, 'KVa',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("送信日設定", 'send_term', 4, 'a',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("状態", 'status', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("メールタイトル", 'subject', SMTEXT_LEN, 'KVa',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("メール本文", 'mail_body', LLTEXT_LEN, 'KVa',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        // オリジナル対象購入商品
        $objFormParam->addParam
            ("オリジナル商品IDS", 'org_product_cds', 0, '', array("SPTAB_CHECK"));
        // 追加対象購入商品
        $objFormParam->addParam
            ("追加商品IDS", 'add_product_cds', 0, '', array("SPTAB_CHECK"));
        // 削除対象購入商品
        $objFormParam->addParam
            ("削除商品IDS", 'del_product_cds', 0, '', array("SPTAB_CHECK"));

        // 検索条件
        $objFormParam->addParam("フォローメール名", "search_follow_name",
                                40, 'n', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("送信日設定", "search_send_term",
                                4, 'n', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ページ送り番号", "search_pageno",
                                INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("表示件数", "search_page_max",
                                INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * フォーム入力パラメーターのエラーチェック
     * 
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return array エラー情報を格納した連想配列
     */
    function lfCheckError(&$objFormParam, $arrPost) {
        // 入力パラメーターチェック
        $arrErr = $objFormParam->checkError();

        // フォローメールコード重複チェック
        $follow_id = $objFormParam->getValue('follow_id');
        $follow_code = $objFormParam->getValue('follow_code');

        if (!$this->lfCheckFollowMailCd($follow_id, $follow_code)) {
            $arrErr["follow_code"] = "※ フォローメールコードが重複しています";
        }

        // 対象購入商品選択必須チェック
        $product_cds = explode(",", $objFormParam->getValue('org_product_cds'));
        for ($i = 0; $i < count($product_cds); ++$i) {
            if (empty($product_cds[$i])) {
                $org_product_cd_err = 1;
                break;
            }
        }
        $product_cds = explode(",", $objFormParam->getValue('add_product_cds'));
        for ($i = 0; $i < count($product_cds); ++$i) {
            if (empty($product_cds[$i])) {
                $add_product_cd_err = 1;
                break;
            }
        }
        if ($org_product_cd_err == 1 && $add_product_cd_err) {
            $arrErr["target_products"] = "※ 対象購入商品を選択してください";
        }

        return $arrErr;
    }

    /**
     * DBにフォローメールデータを登録する
     * 
     * @param array $arrList フォーム入力パラメーター配列
     * @return integer 登録フォローメールID
     */
    function lfRegistFollowMail($arrList) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // トランザクションを開始
        $objQuery->begin();

        // フォローメールIDを持っているかどうかで、新規か更新かを判断する
        $follow_id = 0;
        $sql = '';
        // メール本文内に改行されていた場合は<br>に置き換え
        $arrList['mail_body'] = str_replace("\r\n", "<br>", $arrList['mail_body']);

        if ($arrList['follow_id'] == "") {
            /* 新規登録 */
            $follow_id = $objQuery->nextVal("dtb_follow_mail_follow_id");

            // 新規SQL
            $sql =<<< __EOS
INSERT INTO dtb_follow_mail (
    follow_id
  , follow_code
  , follow_name
  , send_term
  , subject
  , mail_body
  , status
  , del_flg
  , creator_id
  , create_date
  , updator_id
  , update_date
) VALUES (
    {$follow_id}
  , '{$arrList['follow_code']}'
  , '{$arrList['follow_name']}'
  , {$arrList['send_term']}
  , '{$arrList['subject']}'
  , '{$arrList['mail_body']}'
  , {$arrList['status']}
  , 0
  , {$_SESSION['member_id']}
  , NOW()
  , {$_SESSION['member_id']}
  , NOW()
)
__EOS;
        } else {
            /* 更新 */
            $follow_id = $arrList['follow_id'];

            // 更新SQL
            $sql =<<< __EOS
UPDATE dtb_follow_mail
   SET follow_code = '{$arrList['follow_code']}'
     , follow_name = '{$arrList['follow_name']}'
     , send_term   = {$arrList['send_term']}
     , subject     = '{$arrList['subject']}'
     , mail_body   = '{$arrList['mail_body']}'
     , status      = {$arrList['status']}
     , updator_id  = {$_SESSION['member_id']}
     , update_date = NOW()
 WHERE follow_id   = {$follow_id}
__EOS;
        }

        // 実行
        $objQuery->exec($sql);

        // 対象購入商品を追加
        $product_cds = explode(",", $this->arrForm['add_product_cds']);
        for ($i = 0; $i < count($product_cds); ++$i) {
            if (!empty($product_cds[$i])) {
                // 画面操作で同商品コードでadd・del両パラメータに入るため
                // 既存データチェックを行う
                $count = $objQuery->count("dtb_follow_mail_products"
                    ,"follow_id = ? AND product_code = ? AND del_flg = 0"
                    , array($follow_id, $product_cds[$i]));
                if ($count == 0) {
                    $this->lfInsertFollowMailProduct($follow_id, $product_cds[$i]);
                }
            }
        }

        // 対象購入商品を削除
        $product_cds = explode(",", $this->arrForm['del_product_cds']);
        for ($i = 0; $i < count($product_cds); ++$i) {
            if (!empty($product_cds[$i])) {
                $this->lfDeleteFollowMailProduct($follow_id, $product_cds[$i]);
            }
        }

        // トランザクション終了
        $objQuery->commit();

        return $follow_id;
    }

    /**
     * DBにフォローメール商品データを登録する
     * 
     * @param string $follow_id フォローメールid
     * @param string $product_cd 商品コード
     * @return void
     */
    function lfInsertFollowMailProduct($follow_id, $product_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
INSERT INTO dtb_follow_mail_products (
    follow_id
  , product_code
  , del_flg
  , creator_id
  , create_date
  , updator_id
  , update_date
) VALUES (
    {$follow_id}
  , '{$product_code}'
  , 0
  , {$_SESSION['member_id']}
  , NOW()
  , {$_SESSION['member_id']}
  , NOW()
)
__EOS;
        // 実行
        $objQuery->exec($sql);

        // トランザクション終了
        $objQuery->commit();
    }

    /**
     * DBからフォローメールデータを取得する
     * 
     * @param integer $follow_id フォローメールID
     * @return array フォローメールデータ
     */
    function lfGetFollowMail($follow_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT *
  FROM dtb_follow_mail
 WHERE del_flg = 0
   AND follow_id = {$follow_id}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0];
    }

    /**
     * DBからフォローメール商品データを取得する
     * 
     * @param integer $follow_id フォローメールID
     * @return array フォローメール商品データ
     */
    function lfGetFollowMailProducts($follow_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = '';

        if (empty($follow_id)) {
            if (empty($this->arrForm['add_product_cds'])) {
                return array();
            }

            $cds = $this->arrForm['add_product_cds'];
            $cds = str_replace(",", "','", $cds);

            $sql =<<< __EOS
SELECT pc.product_code
     , pd.name AS product_name
  FROM dtb_products_class pc
 INNER JOIN dtb_products pd
    ON pc.product_id = pd.product_id
   AND pd.del_flg = 0
 WHERE pc.del_flg = 0
   AND pc.product_code IN ('{$cds}')
__EOS;
        } else {
            $fmt =<<< __EOS
SELECT fp.product_code
     , pd.name AS product_name
  FROM dtb_follow_mail_products fp
  LEFT JOIN dtb_products_class pc
    ON fp.product_code = pc.product_code
   AND pc.del_flg = 0
  LEFT JOIN dtb_products pd
    ON pc.product_id = pd.product_id
   AND pd.del_flg = 0
 WHERE fp.del_flg = 0
   AND fp.follow_id = {$follow_id}
%s
%s
__EOS;

            $cond1 = '';
            if (!empty($this->arrForm['del_product_cds'])) {
                $cds = $this->arrForm['del_product_cds'];
                $cds = str_replace(",", "','", $cds);
                $cond1 =<<< __EOS
   AND fp.product_code NOT IN ('{$cds}')
__EOS;
            }
            $cond2 = '';
            if (!empty($this->arrForm['add_product_cds'])) {
                $cds = $this->arrForm['add_product_cds'];
                $cds = str_replace(",", "','", $cds);
                $cond2 =<<< __EOS
 UNION
SELECT pc.product_code
     , pd.name AS product_name
  FROM dtb_products_class pc
 INNER JOIN dtb_products pd
    ON pc.product_id = pd.product_id
   AND pd.del_flg = 0
 WHERE pc.del_flg = 0
   AND pc.product_code IN ('{$cds}')
__EOS;
            }
            $sql = sprintf($fmt, $cond1, $cond2);
        }

        $results = $objQuery->getAll($sql);

        return $results;
    }

    /**
     * DBからフォローメール商品データを物理削除する
     * 
     * @param integer $brand_id ブランドID
     * @param integer $product_code 商品コード
     */
    function lfDeleteFollowMailProduct($follow_id, $product_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
DELETE FROM dtb_follow_mail_products
 WHERE follow_id     = {$follow_id}
   AND product_code = '{$product_code}'
__EOS;

        // 実行
        $objQuery->exec($sql);
    }

    /**
     * フォローメールコード重複チェック
     *
     * @param string folloMailCd
     * @return boolean
     */
    function lfCheckFollowMailCd($folloId, $folloMailCd){
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $from = "dtb_follow_mail";
        $where = " del_flg = 0 AND follow_code = ? ";
        if (!empty($folloId)) {
            $where .= " AND follow_id != ? ";
            $arrval= array($folloMailCd, $folloId);
        } else {
            $arrval= array($folloMailCd);
        }
        $count = $objQuery->count($from, $where, $arrval);

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }
}
?>
