<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once CLASS_REALDIR . 'helper/SC_Helper_Payment.php';

/** ベリトランス3Gモジュールの参照 */
require_once MODULE_REALDIR . 'mdl_sbivt3g' .DIRECTORY_SEPARATOR. 'define.php';

/**
 * 支払方法を管理するヘルパークラス(拡張).
 *
 * LC_Helper_Payment をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Helper
 * @author
 * @version $Id:$
 */
class SC_Helper_Payment_Ex extends SC_Helper_Payment
{

    /**
     * 購入金額に応じた支払方法を取得する.
     * オーバーライド by Veritrans3G
     *
     * @param  integer $total 購入金額
     * @return array   購入金額に応じた支払方法の配列
     */
    public function getByPrice($total)
    {

        // 親処理実行
        $arrPayments = parent::getByPrice($total, $deliv_id);

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // ベリトランスの支払方法を取得
        $arrIds = array();
        foreach ($arrPayments as $payment) {
            $arrIds[] = $payment['payment_id'];
        }
        $where = sprintf('memo01 IS NOT NULL AND payment_id IN (%s)',
            implode(',', array_pad(array(), count($arrIds), '?'))
        );
        $sbiPayments = $objQuery->select('payment_id, memo01',
            'dtb_payment', $where, $arrIds);
        $arrInnerIds = array();
        foreach ($sbiPayments as $record) {
            $arrInnerIds[$record['payment_id']] = $record['memo01']; 
        }

        // 有効な支払方法のみ残す
        $arrValidPayments = array();
        $objMobile = new Net_UserAgent_Mobile();
        foreach ($arrPayments as $payment) {
            $valid = true;
            switch ($arrInnerIds[$payment['payment_id']]) {

            // PC環境に限るもの
            case MDL_SBIVT3G_INNER_ID_CUP :
            case MDL_SBIVT3G_INNER_ID_PAYPAL :
                if ($objMobile->isMobile() == true
                || $objMobile->isSmartphone() == true){
                    $valid = false;
                }
                break;

            // IEの特定バージョンに限るもの
            case MDL_SBIVT3G_INNER_ID_EDY_PC_APP :
            case MDL_SBIVT3G_INNER_ID_SUICA_PC_APP :
            case MDL_SBIVT3G_INNER_ID_WAON_PC_APP :
                if (GC_Utils_SBIVT3G::isValidBrowserForPasori() == false) {
                    $valid = false;
                }
                break;

            // 携帯電話＋特定のスマートフォン
            case MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP :
            case MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP :
                if ($objMobile->isMobile() == false
                && GC_Utils_SBIVT3G::isValidSphoneForEMoney() == false){
                    $valid = false;
                }
                break;
            default : // それ以外
                break;
            }
            if ($valid == true) {
                $arrValidPayments[] = $payment;
            }
        }
        return $arrValidPayments;
    }
}
