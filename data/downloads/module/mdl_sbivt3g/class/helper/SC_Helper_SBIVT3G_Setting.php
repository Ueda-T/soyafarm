<?php
/**
 * SC_Helper_SBIVT3G_Setting.php - SC_Helper_SBIVT3G_Setting クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: SC_Helper_SBIVT3G_Setting.php 193 2013-07-31 01:24:57Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/

/**
 * SBIVT3Gモジュールの設定値管理ヘルパークラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version    Release: @package_version@
 * @link        http://www.veritrans.co.jp/3gps
 * @access  public
 * @author  K.Hiranuma
 */
class SC_Helper_SBIVT3G_Setting {

    // {{{ properties

    /** 設定値の格納配列 */
    var $arrSettings;

    /** 設定値除外値の格納配列 */
    var $arrSettingExcept;

    // }}}
    // {{{ functions

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    function SC_Helper_SBIVT3G_Setting() {
        $this->__counstruct();
    }

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    function __counstruct() {
        $this->init();
    }

    /**
     * シングルトンパターン
     *
     * @access public
     * @return SC_Helper_SBIVT3G_Setting シングルトン・インスタンス
     */
    function getSingletonInstance() {
        $myName = '_SC_Helper_SBIVT3G_Setting_instance';
        if (isset($GLOBALS[$myName]) == false
                || get_class($GLOBALS[$myName]) != "SC_Helper_SBIVT3G_Setting") {
            $GLOBALS[$myName] =& new SC_Helper_SBIVT3G_Setting();
        }
        return $GLOBALS[$myName];
    }

    /**
     * 初期化処理
     *
     * @access public
     * @return void
     */
    function init() {

        // 設定除外値を指定
        $this->arrSettingExcept = array(
        );

        $this->arrSettings = $this->loadSetting();

        // マーチャントID等のチェック
        if ($this->checkProperties() == false) {
            // プロパティファイルの設定を試みる
            $objInstall =& SC_Helper_SBIVT3G_Install::getSingletonInstance();
            $objInstall->setMdkProperties($this->arrSettings);
            $objInstall->setLogProperties();
        }
    }

    /**
     * プロパティファイルのチェック
     *
     * @access protected
     * @return boolean
     */
    function checkProperties() {
        // 未設定なら関知しない
        if (!isset($this->arrSettings['merchantCcId'])
        || strcmp($this->arrSettings['merchantCcId'], '') == 0) { 
            return true;
        }

        // マーチャントCCIDのみ調査
        $ppt = GC_Utils_SBIVT3G::file_get_contents(MDL_SBIVT3G_PROPERTIES_PATH);
        if (preg_match('/\r\n *MERCHANT_CC_ID *= *([^ ]+) *\r\n/', $ppt, $m)) {
            if (strcmp($this->arrSettings['merchantCcId'], $m[1]) == 0) { 
                return true;
            }
        }
        return false;
    }

    /**
     * 設定値を一括取得
     *
     * @access public
     * @return array 設定値配列
     */
    function getSetting() {
        return $this->arrSettings;
    }

    /**
     * 設定値を保存
     *
     * @access public
     * @param array $subData 設定値配列
     * @return void
     */
    function saveSetting($subData) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 除外項目を消去
        foreach ($this->arrSettingExcept as $name) {
            if (isset($subData[$name]) == true) {
                unset($subData[$name]);
            }
        }

        $arrUpdate = array('sub_data' => serialize($subData));
        $objQuery->update('dtb_module',
            $arrUpdate, 'module_code = ?',
            array(MDL_SBIVT3G_MODULE_CODE)
        );

        $this->arrSettings = $subData;
    }

    /**
     * 設定値をロード
     *
     * @access public
     * @return array 設定値配列
     */
    function loadSetting() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql = 'SELECT sub_data FROM dtb_module WHERE module_code = ?';
        $subData = $objQuery->getOne($sql, array(MDL_SBIVT3G_MODULE_CODE));

        $arrSettings = unserialize($subData);
        return $arrSettings;
    }

    /**
     * 設定値を取得
     *
     * @access public
     * @return mixed 設定値
     */
    function get($name) {
        if (isset($this->arrSettings[$name]) == false) {
            return null;
        }
        return $this->arrSettings[$name];
    }

    /**
     * 設定用にクレジットカードブランドを返す
     *
     * @access public
     * @return array ブランド
     */
    function getSettingCardBrand() {
        $arrBrand = array(
            'C_visaFlg'   => 'VISA(一括・分割・リボルビング)',
            'C_masterFlg' => 'MasterCard(一括・分割・リボルビング)',
            'C_jcbFlg'    => 'JCB(一括・分割・リボルビング)',
            'C_dinersFlg' => 'Diners Club(一括・リボルビング)',
            'C_amexFlg'   => 'American Express(一括・分割)',
        );
        return $arrBrand;
    }

    /**
     * 設定用にクレジットカード決済の支払方法・回数を返す
     *
     * @access public
     * @return array 支払方法・回数
     */
    function getSettingCount() {

        $arrCounts = array(
            MDL_SBIVT3G_SETTING_PTYPE_SECOND
                => MDL_SBIVT3G_SETTING_PTYPE_STRING_SECOND,
            MDL_SBIVT3G_SETTING_PTYPE_SPLIT
                => MDL_SBIVT3G_SETTING_PTYPE_STRING_SPLIT,
            MDL_SBIVT3G_SETTING_PTYPE_REVO
                => MDL_SBIVT3G_SETTING_PTYPE_STRING_REVO,
            MDL_SBIVT3G_SETTING_PTYPE_BONUS_BULK
                => MDL_SBIVT3G_SETTING_PTYPE_STRING_BONUS_BULK,
        );
        return $arrCounts;
    }

    /**
     * クレジットカード決済の支払方法・回数の有効性チェック
     *
     * @access public
     * @param array $arrBland  選択カードブランド
     * @param array $arrCounts 選択支払方法・回数
     * @return boolean 正否
     */
    function isValidPaymentCount($arrBland, $arrCounts) {

        if ($arrBland['C_visaFlg'] == false
        && $arrBland['C_masterFlg'] == false
        && $arrBland['C_jcbFlg'] == false
        && $arrBland['C_amexFlg'] == false) {
            // dinersは分割NG
            if (isset($arrCounts[MDL_SBIVT3G_SETTING_PTYPE_SECOND])
            || isset($arrCounts[MDL_SBIVT3G_SETTING_PTYPE_SPLIT])) {
                return false;
            }
        }

        if ($arrBland['C_visaFlg'] == false
        && $arrBland['C_masterFlg'] == false
        && $arrBland['C_jcbFlg'] == false
        && $arrBland['C_dinersFlg'] == false) {
            // AMEXはリボNG
            if (isset($arrCounts[MDL_SBIVT3G_SETTING_PTYPE_REVO])) {
                return false;
            }
        }
        return true;
    }

    /**
     * クレジットカード決済の支払方法を返す
     *
     * @access public
     * @return array 支払方法
     */
    function getPaymentType() {
        $arrType = array(
            MDL_SBIVT3G_PTYPE_BULK  => MDL_SBIVT3G_PTYPE_STRING_BULK,
            MDL_SBIVT3G_PTYPE_SPLIT => MDL_SBIVT3G_PTYPE_STRING_SPLIT,
            MDL_SBIVT3G_PTYPE_REVO  => MDL_SBIVT3G_PTYPE_STRING_REVO,
            MDL_SBIVT3G_PTYPE_BONUS_BULK => MDL_SBIVT3G_PTYPE_STRING_BONUS_BULK,
        );
        $arrSetting = &$this->arrSettings['C_settingCount'];

        // 選択がなければリボを除去
        if (isset($arrSetting[MDL_SBIVT3G_SETTING_PTYPE_REVO]) == false) {
            unset($arrType[MDL_SBIVT3G_PTYPE_REVO]);
        }

        // 選択がなければボーナス一括を除去
        if (isset($arrSetting[MDL_SBIVT3G_SETTING_PTYPE_BONUS_BULK]) == false) {
            unset($arrType[MDL_SBIVT3G_PTYPE_BONUS_BULK]);
        }

        // 2回払い、分割払いの選択がなければ分割払いも除去
        if (isset($arrSetting[MDL_SBIVT3G_SETTING_PTYPE_SECOND]) == false
        && isset($arrSetting[MDL_SBIVT3G_SETTING_PTYPE_SPLIT]) == false) {
            unset($arrType[MDL_SBIVT3G_PTYPE_SPLIT]);
        }

        return $arrType;
    }

    /**
     * クレジットカード決済の支払回数を返す
     *
     * @access public
     * @return array 支払回数
     */
    function getPaymentCount() {

        $arrCounts = array();

        // パターンのテンプレート取得
        $arrCounts = explode(',', MDL_SBIVT3G_PCOUNT_PATTERN);

        // 設定の取得
        $second = MDL_SBIVT3G_SETTING_PTYPE_SECOND;
        $split = MDL_SBIVT3G_SETTING_PTYPE_SPLIT;
        $isSecond = isset($this->arrSettings['C_settingCount'][$second]);
        $isSplit  = isset($this->arrSettings['C_settingCount'][$split]);

        // 設定に応じて支払回数の配列を作る
        $arrFiltered = array();
        foreach ($arrCounts as $val) {
            if ($isSecond == false && $val == '2') {
                continue;
            } else if ($isSplit == false && $val != '2') {
                continue;
            }
            $key = sprintf('%s%02d', MDL_SBIVT3G_PCOUNT_PREFIX, $val);
            $arrFiltered[$key] = sprintf('%d回払い', $val);
        }
        return $arrFiltered;
    }

    /**
     * カード用売上フラグの選択肢を返す
     *
     * @access public
     * @return array 売上フラグ選択肢
     */
    function getCardCaptures() {
        $arrCaptures = array(
            1 => '与信＋売上請求',
            0 => '与信のみ'
        );
        return $arrCaptures;
    }

    /**
     * MPIホスティングのID=>コードの配列を返す
     *
     * @access public
     * @return array MPIホスティングのID=>送信コードの連想配列
     */
    function getMpiOption() {
        $arrMpiOption = array(
            MDL_SBIVT3G_MPI_ID_COMPLETE => MDL_SBIVT3G_MPI_CODE_COMPLETE,
            MDL_SBIVT3G_MPI_ID_COMPANY  => MDL_SBIVT3G_MPI_CODE_COMPANY, 
            MDL_SBIVT3G_MPI_ID_MERCHANT => MDL_SBIVT3G_MPI_CODE_MERCHANT,
        );
        return $arrMpiOption;
    }

    /**
     * MPIホスティングのID=>文言の配列を返す
     *
     * @access public
     * @return array MPIホスティングのID=>送信コードの連想配列
     */
    function getMpiOptionString() {
        $arrMpiOptionString = array(
            MDL_SBIVT3G_MPI_ID_COMPLETE => '完全認証',
            MDL_SBIVT3G_MPI_ID_COMPANY  => '通常認証（カード会社リスク負担）',
            MDL_SBIVT3G_MPI_ID_MERCHANT => '通常認証（カード会社、加盟店リスク負担）',
        );
        return $arrMpiOptionString;
    }

    /**
     * コンビニ決済の選択可能店舗を返す
     *
     * @access public
     * @param string $command all:全店舗取得
     * @return array コンビニ店舗
     */
    function getCvsShop($command = '') {
        // 管理向けコマンド
        $all = false;
        if (strcmp($command, 'all') == 0) {
            $all = true;
        }

        $arrShops = array();
        if ($this->arrSettings['V_sejFlg'] == true || $all == true) {
            $arrShops[MDL_SBIVT3G_CVS_TYPE_SEVEN] = 'セブンイレブン';
        }
        if ($this->arrSettings['V_lawsonFlg'] == true || $all == true) {
            $arrShops[MDL_SBIVT3G_CVS_TYPE_LAWSON] =
                'ローソン・ミニストップ・セイコーマート';
        }
        if ($this->arrSettings['V_famimaFlg'] == true || $all == true) {
            $arrShops[MDL_SBIVT3G_CVS_TYPE_FM] = 'ファミリーマート';
        }
        if ($this->arrSettings['V_econFlg'] == true || $all == true) {
            $arrShops[MDL_SBIVT3G_CVS_TYPE_ECON] =
                'ローソン・ファミリーマート・ミニストップ・セイコーマート';
        }
        if ($this->arrSettings['V_otherFlg'] == true || $all == true) {
            $arrShops[MDL_SBIVT3G_CVS_TYPE_OTHER] =
                'サークルKサンクス・デイリーヤマザキ';
        }
        return $arrShops;
    }

    /**
     * PayPal用売上フラグの選択肢を返す
     *
     * @access public
     * @return array 売上フラグ選択肢
     */
    function getPaypalCaptures() {
        $arrCaptures = array(
            1 => '与信＋売上請求',
            0 => '与信のみ'
        );
        return $arrCaptures;
    }

    /**
     * キャリア決済の選択可能キャリアサービスを返す
     *
     * @access public
     * @param string $command all:全サービス取得
     * @return array キャリアサービス
     */
    function getCarrierServices($command = '') {
        // 端末種別を取得
        $terminal = MDL_SBIVT3G_CARRIER_TERMINAL_PC;
        $nu = new Net_UserAgent_Mobile();
        if ($nu->isMobile()) {
            $terminal = MDL_SBIVT3G_CARRIER_TERMINAL_KTAI;
        } else if ($nu->isSmartphone()) {
            $terminal = MDL_SBIVT3G_CARRIER_TERMINAL_SMAHO;
        }

        // 管理向けコマンド
        $all = false;
        if (strcmp($command, 'all') == 0) {
            $all = true;
        }

        $arrServices = array();

        // ドコモケータイ払い
        if ($all == true) {
            $arrServices[MDL_SBIVT3G_CARRIER_TYPE_DOCOMO] =
                PAYMENT_NAME_CARRIER_DOCOMO;
        } else if ($this->arrSettings['CA_docomoFlg'] == true) {
            // PCでドコモケータイ払いを許可する場合と
            // スマートフォン、フィーチャーフォンは選択可能
            if (($terminal == MDL_SBIVT3G_CARRIER_TERMINAL_PC &&
                 $this->arrSettings['CA_docomoPcFlg'] == true) ||
                $terminal == MDL_SBIVT3G_CARRIER_TERMINAL_SMAHO ||
                $terminal == MDL_SBIVT3G_CARRIER_TERMINAL_KTAI) {
                $arrServices[MDL_SBIVT3G_CARRIER_TYPE_DOCOMO] =
                    PAYMENT_NAME_CARRIER_DOCOMO;
            }
        }

        // au かんたん決済
        if ($this->arrSettings['CA_auFlg'] == true || $all == true) {
            $arrServices[MDL_SBIVT3G_CARRIER_TYPE_AU] = PAYMENT_NAME_CARRIER_AU;
        }

        // ソフトバンクまとめて支払い（B）
        if ($all == true) {
            $arrServices[MDL_SBIVT3G_CARRIER_TYPE_SB_KTAI] =
                PAYMENT_NAME_CARRIER_SB_KTAI;
        } else if ($this->arrSettings['CA_sb_ktaiFlg'] == true &&
                   $terminal != MDL_SBIVT3G_CARRIER_TERMINAL_KTAI) {
            $arrServices[MDL_SBIVT3G_CARRIER_TYPE_SB_KTAI] =
                PAYMENT_NAME_CARRIER_SB_KTAI;
        }

        // ソフトバンクまとめて支払い（A）
        if ($all == true) {
            $arrServices[MDL_SBIVT3G_CARRIER_TYPE_SB_MATOMETE] =
                PAYMENT_NAME_CARRIER_SB_MATOMETE;
        } else if ($this->arrSettings['CA_sb_matometeFlg'] == true &&
                   $terminal != MDL_SBIVT3G_CARRIER_TERMINAL_KTAI) {
            $arrServices[MDL_SBIVT3G_CARRIER_TYPE_SB_MATOMETE] =
                PAYMENT_NAME_CARRIER_SB_MATOMETE;
        }

        // S!まとめて支払い
        if ($all == true ||
            ($this->arrSettings['CA_s_bikkuriFlg'] == true &&
             $terminal == MDL_SBIVT3G_CARRIER_TERMINAL_KTAI)) {
            $arrServices[MDL_SBIVT3G_CARRIER_TYPE_S_BIKKURI] =
                PAYMENT_NAME_CARRIER_S_BIKKURI;
        }

        return $arrServices;
    }

    /**
     * 3G決済ステータス配列を返す
     *
     * @access public
     * @return array 売上フラグ選択肢
     */
    function getPayStatus() {
        $arrPayStatus = array(
            MDL_SBIVT3G_STATUS_AUTH    => MDL_SBIVT3G_STATUS_STRING_AUTH,
            MDL_SBIVT3G_STATUS_CAPTURE => MDL_SBIVT3G_STATUS_STRING_CAPTURE,
            MDL_SBIVT3G_STATUS_CANCEL  => MDL_SBIVT3G_STATUS_STRING_CANCEL,
            MDL_SBIVT3G_STATUS_REQUEST => MDL_SBIVT3G_STATUS_STRING_REQUEST,
            MDL_SBIVT3G_STATUS_DEPOSIT => MDL_SBIVT3G_STATUS_STRING_DEPOSIT,
            MDL_SBIVT3G_STATUS_REFUND  => MDL_SBIVT3G_STATUS_STRING_REFUND,
            MDL_SBIVT3G_STATUS_EXPIRED => MDL_SBIVT3G_STATUS_STRING_EXPIRED,
        );
        return $arrPayStatus;
    }

    /**
     * キャリア決済用商品タイプの選択肢を返す
     *
     * @access public
     * @return array 商品タイプ選択肢
     */
    function getCarrierItemTypes() {
        $arrItemTypes = array(
            (int)MDL_SBIVT3G_CARRIER_ITEMTYPE_DIGITAL => 'デジタルコンテンツ',
            (int)MDL_SBIVT3G_CARRIER_ITEMTYPE_BUPPAN  => '物販',
            (int)MDL_SBIVT3G_CARRIER_ITEMTYPE_EKIMU   => '役務'
        );
        return $arrItemTypes;
    }

    /**
     * キャリア決済用売上フラグの選択肢を返す
     *
     * @access public
     * @return array 売上フラグ選択肢
     */
    function getCarrierCaptures() {
        $arrCaptures = array(
            (int)MDL_SBIVT3G_CARRIER_WC_CAPTURE   => '与信＋売上請求',
            (int)MDL_SBIVT3G_CARRIER_WC_AUTHORIZE => '与信のみ'
        );
        return $arrCaptures;
    }

    /**
     * キャリア決済用本人認証(3Dセキュア)の選択肢を返す
     *
     * @access public
     * @return array 本人認証(3Dセキュア)選択肢
     */
    function getCarrier3D() {
        $arr3Ds = array(
            (int)MDL_SBIVT3G_CARRIER_D3FLAG_NONE   => '無し',
            (int)MDL_SBIVT3G_CARRIER_D3FLAG_BYPASS => 'バイパス',
            (int)MDL_SBIVT3G_CARRIER_D3FLAG_HAS     => '有り'
        );
        return $arr3Ds;
    }
}
