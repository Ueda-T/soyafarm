<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * ログインチェック のページクラス.
 *
 * TODO mypage/LC_Page_Mypage_LoginCheck と統合
 *
 * @package Page
 * @version $Id:LC_Page_FrontParts_LoginCheck.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_LoginCheck extends LC_Page_Ex
{
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
    function action()
    {
        // 会員管理クラス
        $objCustomer = new SC_Customer_Ex();
        // クッキー管理クラス
        $objCookie = new SC_Cookie_Ex(COOKIE_EXPIRE);
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        // リクエスト値をフォームにセット
        $objFormParam->setParam($_POST);

        // モードによって分岐
        switch ($this->getMode()) {
        // ログイン
        case 'login':
            // 入力値のエラーチェック
            $objFormParam->trimParam();
            $arrErr = $objFormParam->checkError();

            // エラーの場合はエラー画面に遷移
            if (count($arrErr) > 0) {
                if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                    echo $this->lfGetErrorMessage(TEMP_LOGIN_ERROR);
                } else {
                    SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                }
		exit;
            }

            // 入力チェック後の値を取得
            $arrForm = $objFormParam->getHashArray();

            // クッキー保存判定
            if ($arrForm['login_memory'] == '1' &&
		$arrForm['login_email'] != '') {
                $objCookie->setCookie('login_email', $arrForm['login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            // 遷移先の制御
            if (count($arrErr) == 0) {
                // ログイン判定
                $loginFailFlag = false;
/*
                if(SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
                    // モバイルサイト
                    if(!$objCustomer->getCustomerDataFromMobilePhoneIdPass($arrForm['login_pass']) && !$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'], true)) {
                        $loginFailFlag = true;
                    }
                } else {
                    // モバイルサイト以外
                    if (!$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
                        $loginFailFlag = true;
                    }
                }
*/
                if (!$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
                    $loginFailFlag = true;
                }

                // ログイン処理
                if ($loginFailFlag == false) {
		    // ログイン日時を更新する。
		    $objCustomer->updateLastLoginDate();
		    
                    if(SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
                        // ログインが成功した場合は携帯端末IDを保存する。
                        $objCustomer->updateMobilePhoneId();
                    }

                    // --- ログインに成功した場合
                    if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                        echo SC_Utils_Ex::jsonEncode(array('success' => $_POST['url']));
                    } else {
                        SC_Response_Ex::sendRedirect($_POST['url']);
                    }
                    exit;
                } else {
                    $objQuery = SC_Query_Ex::getSingletonInstance();
		    $email = $arrForm['login_email'];
		    $sql =<<<__EOS
select count(*)
  from dtb_customer
 where email = binary '{$email}'
   and status = 1
   and del_flg = 0
__EOS;

                    // $where = '(email = ? OR email_mobile = ?) AND status = 1 AND del_flg = 0';
                    // $ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email'], $arrForm['login_email']));
		    $ret = $objQuery->getOne($sql);

                    // ログインエラー表示 TODO リファクタリング
                    if ($ret > 0) {
#if 1 // 2014/3/11 by nao.
{
    SC_Response_Ex::sendRedirect('../renewal/');
}
#endif
                        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                            echo $this->lfGetErrorMessage(TEMP_LOGIN_ERROR);
                        } else {
                            SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                        }
			exit;
                    } else {
                        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                            echo $this->lfGetErrorMessage(SITE_LOGIN_ERROR);
                        } else {
                            SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                        }
			exit;
                    }
                }
            } else {
                // XXX 到達しない？
                // 入力エラーの場合、元のアドレスに戻す。
                SC_Response_Ex::sendRedirect($_POST['url']);
                exit;
            }
            break;

        // ログアウト
        case 'logout':
            // ログイン情報の解放
            $objCustomer->EndSession();
            // 画面遷移の制御
            $mypage_url_search = strpos('.'.$_POST['url'], 'mypage');
            if ($mypage_url_search == 2) {
                // マイページログイン中はログイン画面へ移行
                SC_Response_Ex::sendRedirectFromUrlPath('mypage/login.php');
            } else {
                // 上記以外の場合、トップへ遷移
                //SC_Response_Ex::sendRedirect(HTTP_URL);
                SC_Response_Ex::sendRedirect(URL_MYPAGE_TOP);
            }
            exit;
        default:
            break;
        }
    }

    /**
     * パラメーター情報の初期化.
     *
     * @param SC_FormParam $objFormParam パラメーター管理クラス
     * @return void
     */
    function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('記憶する', 'login_memory', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('メールアドレス', 'login_email', MTEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('パスワード', 'login_pass', PASSWORD_MAX_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * エラーメッセージを JSON 形式で返す.
     *
     * TODO リファクタリング
     * この関数は主にスマートフォンで使用します.
     *
     * @param integer エラーコード
     * @return string JSON 形式のエラーメッセージ
     * @see LC_PageError
     */
    function lfGetErrorMessage($error)
    {
        switch ($error) {
	case TEMP_LOGIN_ERROR:
	    $msg = "メールアドレスもしくはパスワードが正しくありません。\n本登録がお済みでない場合は、仮登録メールに記載されているURLより本登録を行ってください。";
	    break;
	case SITE_LOGIN_ERROR:
	default:
	    $msg = "メールアドレスもしくはパスワードが正しくありません。";
        }
        return SC_Utils_Ex::jsonEncode(array('login_error' => $msg));
    }
}
?>
