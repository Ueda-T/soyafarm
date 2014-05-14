<?php
/******************************************************************************
 * 3GPSMDK.php - 3GPS MDK Library main file.
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   VeriTrans Inc.
 ******************************************************************************/

if (defined('3GPSMDK_INCLUDED')) {
    $GLOBAL['3GPSMDK_COUNT_OF_INCLUDE']++;
    return;
} else {
    $GLOBAL['3GPSMDK_COUNT_OF_INCLUDE'] = 1;
    define('3GPSMDK_INCLUDED', TRUE);
}

// デフォルトタイムゾーンの設定
// php.iniにdate.timezoneが設定されていない場合のみ
$date_timezone = ini_get("date.timezone");
if (empty($date_timezone)) {
    date_default_timezone_set("Asia/Tokyo");
}

if (!defined('DS'))             define('DS', DIRECTORY_SEPARATOR);                       //  DS ディレクトリセパレータ省略形
if (!defined('LF'))             define('LF', PHP_EOL);                                   //  OS依存の改行

if (!defined('MDK_DIR'))        define('MDK_DIR', dirname(__FILE__));                    //  MDKインストールディレクトリ
if (!defined('MDK_LIB_DIR'))    define('MDK_LIB_DIR', MDK_DIR . DS . "Lib");             //  MDK/Libディレクトリ
if (!defined('MDK_DTO_DIR'))    define('MDK_DTO_DIR', MDK_LIB_DIR . DS . "tgMdkDto");    //  MDK/Lib/tgMdkDtoディレクトリ


/******************************************************************************
 *  log4phpが参照する定数
 ******************************************************************************/
//  LOG4PHP_DIR
if (!defined('LOG4PHP_DIR')) { define('LOG4PHP_DIR', MDK_LIB_DIR .DS. "log4php"); }

//  LOG4PHP_CONFIGURATION
if (!defined('LOG4PHP_CONFIGURATION')) { define('LOG4PHP_CONFIGURATION', MDK_DIR .DS. "log4php.properties"); }


/******************************************************************************
 *  各ディレクトリの実在チェック
 ******************************************************************************/
$dh = opendir(MDK_LIB_DIR) or die(MDK_LIB_DIR . " is not a valid directory.");
@closedir($dh);

$dh = opendir(MDK_DTO_DIR) or die(MDK_DTO_DIR . " is not a valid directory.");
@closedir($dh);

$dh = opendir(LOG4PHP_DIR) or die(LOG4PHP_DIR . " is not a valid directory.");
@closedir($dh);


/******************************************************************************
 *  クラスをロードする
 ******************************************************************************/
require_once(MDK_LIB_DIR . DS . 'TGMDK_ErrorMessage.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_Logger.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_Exception.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_Config.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_Cipher.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_Connection.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_ConnectionServlet.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_ConnectionSoap.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_ErrorHandler.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_MaskSaxHandler.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_ContentHandler.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_MerchantUtility.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_Util.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_MerchantSettingContext.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_NVQuery.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_ResElementConstants.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_ResElementProcessor.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_SaxHandler.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_Transaction.php');
require_once(MDK_LIB_DIR . DS . 'TGMDK_JSONQuery.php');

require_once(MDK_DTO_DIR . DS . 'MdkBaseDto.php');
require_once(MDK_DTO_DIR . DS . 'PayNowIdConstants.php');
require_once(MDK_DTO_DIR . DS . 'AbstractPayNowIdRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AbstractPaymentRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AbstractPaymentCreditRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AbstractPayNowIdResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'OptionParams.php');
require_once(MDK_DTO_DIR . DS . 'SearchRange.php');
require_once(MDK_DTO_DIR . DS . 'TradRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'TransactionInfo.php');
require_once(MDK_DTO_DIR . DS . 'TransactionInfos.php');
require_once(MDK_DTO_DIR . DS . 'ProperOrderInfo.php');
require_once(MDK_DTO_DIR . DS . 'ProperTransactionInfo.php');
require_once(MDK_DTO_DIR . DS . 'CommonSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'AlipaySearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'BankSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'CardSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'CarrierSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'CvsSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'EmSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'PaypalSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'SaisonSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'MpiSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'SearchParameters.php');
require_once(MDK_DTO_DIR . DS . 'OrderInfo.php');
require_once(MDK_DTO_DIR . DS . 'OrderInfos.php');
require_once(MDK_DTO_DIR . DS . 'TransactionApi.php');
require_once(MDK_DTO_DIR . DS . 'TransactionApis.php');
require_once(MDK_DTO_DIR . DS . 'TransactionCard.php');
require_once(MDK_DTO_DIR . DS . 'TransactionCards.php');
require_once(MDK_DTO_DIR . DS . 'BankAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'BankAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardCancelRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardCancelResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardCaptureRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardCaptureResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardReAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardReAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardRetryRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardRetryResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CarrierAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CarrierAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CarrierCancelRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CarrierCancelResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CarrierCaptureRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CarrierCaptureResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CarrierTerminateRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CarrierTerminateResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CvsAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CvsAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CvsCancelRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CvsCancelResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'EmAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'EmAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'EmCancelRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'EmCancelResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'EmRefundRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'EmRefundResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'PaypalAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'PaypalAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'PaypalCancelRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'PaypalCancelResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'PaypalCaptureRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'PaypalCaptureResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'PaypalRefundRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'PaypalRefundResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'MpiAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'MpiAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'MpiReAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'MpiReAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'SaisonAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'SaisonAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'SaisonCancelRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'SaisonCancelResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'SaisonCaptureRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'SaisonCaptureResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'SearchRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'SearchResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopCancelRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopCancelResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopCaptureRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopCaptureResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopRefundRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopRefundResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'UpopSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'AlipayAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AlipayAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'AlipayRefundRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AlipayRefundResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'AlipaySearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'BankFinancialInstInfo.php');
require_once(MDK_DTO_DIR . DS . 'MasterInfo.php');
require_once(MDK_DTO_DIR . DS . 'MasterInfos.php');
require_once(MDK_DTO_DIR . DS . 'Masters.php');
require_once(MDK_DTO_DIR . DS . 'OricoscAuthorizeRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'OricoscAuthorizeResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'OricoscSearchParameter.php');
require_once(MDK_DTO_DIR . DS . 'Account.php');
require_once(MDK_DTO_DIR . DS . 'RecurringUpdateResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'RecurringUpdateRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'RecurringGetResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'RecurringGetRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'RecurringDeleteResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'RecurringDeleteRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'RecurringAddResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'RecurringAddRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'ChargeUpdateResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'ChargeUpdateRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'ChargeDeleteResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'ChargeDeleteRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'ChargeAddResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'ChargeAddRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardInfoUpdateResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardInfoUpdateRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardInfoGetResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardInfoGetRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardInfoDeleteResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardInfoDeleteRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'CardInfoAddResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'CardInfoAddRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AccountUpdateResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'AccountUpdateRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AccountRestoreResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'AccountRestoreRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AccountDeleteResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'AccountDeleteRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'AccountAddResponseDto.php');
require_once(MDK_DTO_DIR . DS . 'AccountAddRequestDto.php');
require_once(MDK_DTO_DIR . DS . 'RecurringChargeParam.php');
require_once(MDK_DTO_DIR . DS . 'RecurringCharge.php');
require_once(MDK_DTO_DIR . DS . 'PayNowIdResponse.php');
require_once(MDK_DTO_DIR . DS . 'PayNowIdParam.php');
require_once(MDK_DTO_DIR . DS . 'OptionResults.php');
require_once(MDK_DTO_DIR . DS . 'ChargeParam.php');
require_once(MDK_DTO_DIR . DS . 'CardParam.php');
require_once(MDK_DTO_DIR . DS . 'CardInfo.php');
require_once(MDK_DTO_DIR . DS . 'AccountParam.php');
require_once(MDK_DTO_DIR . DS . 'AccountInfo.php');
require_once(MDK_DTO_DIR . DS . 'AccountBasicParam.php');
require_once(MDK_DTO_DIR . DS . 'AccountBasic.php');

////  各クラスをロード
//foreach ($lib_includes as $incfile) {
//    require_once(MDK_LIB_DIR . DS . $incfile);
//}
//
////  各DTOクラスをロード
//foreach ($dto_includes as $incfile) {
//    require_once(MDK_DTO_DIR . DS . $incfile);
//}

?>
