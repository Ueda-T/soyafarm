<?php

/*----------------------------------------------------------------------
 * [名称] SC_Fpdf
 * [概要] pdfファイルを表示する。
 *----------------------------------------------------------------------
 */

require(DATA_REALDIR . 'module/fpdf/fpdf.php');
require(DATA_REALDIR . 'module/fpdi/japanese.php');
define('PDF_TEMPLATE_REALDIR', TEMPLATE_ADMIN_REALDIR . 'pdf/');

class SC_Fpdf {
    // 2012.08.15 テンプレート変更
    function SC_Fpdf($download, $title, $tpl_pdf = 'receipt.pdf') {
        // デフォルトの設定
        $this->tpl_pdf = PDF_TEMPLATE_REALDIR . $tpl_pdf;  // テンプレートファイル
        $this->pdf_download = $download;      // PDFのダウンロード形式（0:表示、1:ダウンロード）
        $this->tpl_title = $title;
        $this->tpl_dispmode = 'real';      // 表示モード
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->width_cell = array(110.3,12,21.7,24.5);
        // 2011.05.06 ギフトラッピング,熨斗用 選択肢
        $this->arrOptions = array(0 => ' 無し', 1 => ' 有り');

        $this->label_cell[] = $this->lfConvSjis("商品名 / 商品コード / [ 規格 ]");
        $this->label_cell[] = $this->lfConvSjis("数量");
        $this->label_cell[] = $this->lfConvSjis("単価");
        $this->label_cell[] = $this->lfConvSjis("金額(税込)");

        $this->arrMessage = array(
            'このたびはお買上げいただきありがとうございます。',
            '下記の内容にて納品させていただきます。',
            'ご確認くださいますよう、お願いいたします。'
        );

        $this->pdf  = new PDF_Japanese();

        // SJISフォント
        $this->pdf->AddSJISFont();
        $this->pdf->SetFont('SJIS');

        //ページ総数取得
        $this->pdf->AliasNbPages();

        // マージン設定
        $this->pdf->SetMargins(15, 20);

        // PDFを読み込んでページ数を取得
        $pageno = $this->pdf->setSourceFile($this->tpl_pdf);
    }

    function setData($arrData) {
        $this->arrData = $arrData;

        // ページ番号よりIDを取得
        $tplidx = $this->pdf->ImportPage(1);

        // 2011.05.06 配送先情報を取得する
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrResult = $objPurchase->getShippings($this->arrData['order_id'],true);
        //$arrProduct = $this->lfGetOrderDetail($this->arrData['order_id']);
 

        // 複数配送指定かどうか
        if(count($arrResult) > 1){
            // 複数配送指定の場合は配送先数のページを出力する
            foreach($arrResult as $key => $val){
                // ページを追加（新規）
                $this->pdf->AddPage();

                //表示倍率(100%)
                $this->pdf->SetDisplayMode($this->tpl_dispmode);

                if (SC_Utils_Ex::sfIsInt($arrData['order_id'])) {
                    $this->disp_mode = true;
                    $order_id = $arrData['order_id'];
                }

                // テンプレート内容の位置、幅を調整 ※useTemplateに引数を与えなければ100%表示がデフォルト
                $this->pdf->useTemplate($tplidx);
            
                // ショップ情報 
                $this->setShopData();
                // メッセージ
                $this->setMessageData();
                // 受注データ
                $this->setOrderData($val,1);
                // 備考欄
                $this->setEtcData();
            }
        
        } else {
            // ページを追加（新規）
            $this->pdf->AddPage();

            //表示倍率(100%)
            $this->pdf->SetDisplayMode($this->tpl_dispmode);

            if (SC_Utils_Ex::sfIsInt($arrData['order_id'])) {
                $this->disp_mode = true;
                $order_id = $arrData['order_id'];
            }

            // テンプレート内容の位置、幅を調整 ※useTemplateに引数を与えなければ100%表示がデフォルト
            $this->pdf->useTemplate($tplidx);
       
            // ショップ情報 
            $this->setShopData();
            
            // メッセージ
            $this->setMessageData();
            
            // 受注データ
            // ▼2012.08 mod by takao
            $shippingData = array_shift($arrResult);
            $this->setOrderData($shippingData, 0);
            //$this->setOrderData($arrResult[0], 0);
            // ▲2012.08 mod by takao

            // 備考欄
            $this->setEtcData();
        }

    }

    function setShopData() {
        // ショップ情報

        $objDb = new SC_Helper_DB_Ex();
        $arrInfo = $objDb->sfGetBasisData();

        $this->lfText(125, 60, $arrInfo['shop_name'], 8, 'B');          //ショップ名
        $this->lfText(125, 63, $arrInfo['law_url'], 8);          //URL
        $this->lfText(125, 68, $arrInfo['law_company'], 8);        //会社名
        $text = "〒 ".$arrInfo['law_zip01']." - ".$arrInfo['law_zip02'];
        $this->lfText(125, 71, $text, 8);  //郵便番号
        $text = $this->arrPref[$arrInfo['law_pref']].$arrInfo['law_addr01'];
        $this->lfText(125, 74, $text, 8);  //都道府県+住所1
        $this->lfText(125, 77, $arrInfo['law_addr02'], 8);          //住所2

        $text = "TEL: ".$arrInfo['law_tel01']."-".$arrInfo['law_tel02']."-".$arrInfo['law_tel03'];
        //FAX番号が存在する場合、表示する
        if (strlen($arrInfo['law_fax01']) > 0) {
            $text .= "　FAX: ".$arrInfo['law_fax01']."-".$arrInfo['law_fax02']."-".$arrInfo['law_fax03'];
        }
        $this->lfText(125, 80, $text, 8);  //TEL・FAX

        if ( strlen($arrInfo['law_email']) > 0 ) {
            $text = "Email: ".$arrInfo['law_email'];
            $this->lfText(125, 83, $text, 8);      //Email
        }

        //ロゴ画像
        // 2011.05.06 ロゴは表示しない
        //$logo_file = PDF_TEMPLATE_REALDIR . 'logo.png';
        //$this->pdf->Image($logo_file, 124, 46, 40);
    }

    function setMessageData() {
        // メッセージ
        $this->lfText(27, 70, $this->arrData['msg1'], 8);  //メッセージ1
        $this->lfText(27, 74, $this->arrData['msg2'], 8);  //メッセージ2
        $this->lfText(27, 78, $this->arrData['msg3'], 8);  //メッセージ3
        $text = "作成日: ".$this->arrData['year']."年".$this->arrData['month']."月".$this->arrData['day']."日";
        $this->lfText(158, 288, $text, 8);  //作成日
    }

    // 2011.05.06 配送先情報を表示するように変更
    function setOrderData($arrShipping = "", $multiple_flg = 1) {
        // DBから受注情報を読み込む
        $this->lfGetOrderData($this->arrData['order_id']);

        // 購入者情報
        $text = "〒 ".$this->arrDisp['order_zip01']." - ".$this->arrDisp['order_zip02'];
        $this->lfText(23, 43, $text, 10); //購入者郵便番号
        $text = $this->arrPref[$this->arrDisp['order_pref']] . $this->arrDisp['order_addr01'];
        $this->lfText(27, 47, $text, 10); //購入者都道府県+住所1
        $this->lfText(27, 51, $this->arrDisp['order_addr02'], 10); //購入者住所2
        $text = $this->arrDisp['order_name01']."　".$this->arrDisp['order_name02']."　様";
        $this->lfText(27, 63, $text, 11); //購入者氏名
        $text = "TEL: ". $this->arrDisp['order_tel01']
                 ." - ". $this->arrDisp['order_tel02']
                 ." - ". $this->arrDisp['order_tel03'];
        $this->lfText(27, 55, $text, 10); //購入者電話番号

        // ▼ お届け先情報
        // 2011.05.06 配送先情報を出力
        $this->pdf->SetFontSize(10);
        $text = "〒 ".$arrShipping['shipping_zip01']." - ".$arrShipping['shipping_zip02'];
        $this->lfText(22, 128, $text, 10); //お届け先郵便番号
        $text = $this->arrPref[$arrShipping['shipping_pref']] . $arrShipping['shipping_addr01'];
        $this->lfText(26, 132, $text, 10); //お届け先都道府県+住所1
        $this->lfText(26, 136, $arrShipping['shipping_addr02'], 10); //お届け先住所2

        // 2011.05.06 お届け先電話番号 追加
        $text = "TEL: ". $arrShipping['shipping_tel01']
                 ." - ". $arrShipping['shipping_tel02']
                 ." - ". $arrShipping['shipping_tel03'];
        $this->lfText(26, 141, $text, 9); // お届け先電話番号

        // 2011.05.06 お届け氏名 追加
        $text = $arrShipping['shipping_name01']. "　"
              . $arrShipping['shipping_name02']."　様";
        $this->lfText(26, 151, $text, 10); // お届け先氏名
        
        // ▲ お届け先情報

        $this->lfText(144, 121, SC_Utils_Ex::sfDispDBDate($this->arrDisp['create_date']), 10); //ご注文日
        $this->lfText(144, 131, $this->arrDisp['order_id'], 10); //注文番号

        $this->lfText(120, 141, "［ お支払方法 ］", 9, 'B');
        $this->lfText(145, 141, $this->arrDisp['payment_method'], 9);
        $this->lfText(120, 151, "［ お届け指定 ］", 9, 'B');
        $this->lfText(145, 151, $arrShipping['shipping_date'] . "  " . $arrShipping['shipping_time'], 9);


        $this->pdf->SetFont('SJIS', 'B', 15);
        $this->pdf->Cell(0, 10, $this->lfConvSjis($this->tpl_title), 0, 2, 'C', 0, '');  //文書タイトル（納品書・請求書）
        $this->pdf->Cell(0, 66, '', 0, 2, 'R', 0, '');
        $this->pdf->Cell(5, 0, '', 0, 0, 'R', 0, '');
        // 2011.06.06 総お支払額 変更
        //$this->pdf->Cell(67, 8, $this->lfConvSjis(number_format($this->arrDisp['payment_total'])." 円"), 0, 2, 'R', 0, '');
        //$this->pdf->Cell(0, 57, '', 0, 2, '', 0, '');
        //$this->pdf->SetFont('SJIS', '', 8);

        $this->pdf->SetFontSize(8);

        $monetary_unit = $this->lfConvSjis("円");
        $point_unit = $this->lfConvSjis('Pt');
        
        // 2011.05.12 複数配送時は処理を分ける
        if($multiple_flg == 1){

            // 購入商品情報
            for ($i = 0; $i < count($arrShipping['shipment_item']); $i++) {

                // 数量
                $data[0] = $arrShipping['shipment_item'][$i]['quantity'];

                // 単価
                //$data[1] = SC_Helper_DB_Ex::sfCalcIncTax($this->arrDisp['price'][$i]);
                $data[1] = SC_Helper_DB_Ex::sfCalcIncTax($arrShipping['shipment_item'][$i]['price']);

                // 金額(税込)
                $data[2] = $data[0] * $data[1];
                $subTotal += $data[2];

                $arrOrder[$i][0]  = $this->lfConvSjis($arrShipping['shipment_item'][$i]['product_name']." / ");
                //$arrOrder[$i][0]  = $this->lfConvSjis($this->arrDisp['product_name'][$i]." / ");
                //$arrOrder[$i][0]  = $this->lfConvSjis($this->arrDisp['product_name'][$i]." / ");

                $arrOrder[$i][0] .= $this->lfConvSjis($arrShipping['shipment_item'][$i]['product_code']." / ");
            
                if ($arrShipping['shipment_item'][$i]['classcategory_name1']) {
                    $arrOrder[$i][0] .= $this->lfConvSjis(" [ ".$arrShipping['shipment_item'][$i]['classcategory_name1']);
                    if ($arrShipping['shipment_item'][$i]['classcategory_name2'] == "") {
                        $arrOrder[$i][0] .= " ]";
                    } else {
                        $arrOrder[$i][0] .= $this->lfConvSjis(" * ".$arrShipping['shipment_item'][$i]['classcategory_name2']." ]");
                    }
                }
                $arrOrder[$i][1]  = number_format($data[0]);
                $arrOrder[$i][2]  = number_format($data[1]).$monetary_unit;
                $arrOrder[$i][3]  = number_format($data[2]).$monetary_unit;

            }
        } else {
            for ($i = 0; $i < count($this->arrDisp['quantity']); $i++) {
                // 購入数量
                $data[0] = $this->arrDisp['quantity'][$i];

                // 税込金額（単価）
                $data[1] = SC_Helper_DB_Ex::sfCalcIncTax($this->arrDisp['price'][$i]);

                // 小計（商品毎）
                $data[2] = $data[0] * $data[1];

                $arrOrder[$i][0]  = $this->lfConvSjis($this->arrDisp['product_name'][$i]." / ");
                $arrOrder[$i][0] .= $this->lfConvSjis($this->arrDisp['product_code'][$i]." / ");
                
                if ($this->arrDisp['classcategory_name1'][$i]) {
                    $arrOrder[$i][0] .= $this->lfConvSjis(" [ ".$this->arrDisp['classcategory_name1'][$i]);
                    if ($this->arrDisp['classcategory_name2'][$i] == "") {
                        $arrOrder[$i][0] .= " ]";
                    } else {
                        $arrOrder[$i][0] .= $this->lfConvSjis(" * ".$this->arrDisp['classcategory_name2'][$i]." ]");
                    }
                }
            $arrOrder[$i][1]  = number_format($data[0]);
            $arrOrder[$i][2]  = number_format($data[1]).$monetary_unit;
            $arrOrder[$i][3]  = number_format($data[2]).$monetary_unit;
            }
        }

        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = "";
        $arrOrder[$i][3] = "";

        $payment_total= 0;

        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->lfConvSjis("商品小計");
        
        if($multiple_flg == 1){
            $arrOrder[$i][3] = number_format($subTotal).$monetary_unit;
            $payment_total += $subTotal;
        } else {
            $arrOrder[$i][3] = number_format($this->arrDisp['subtotal']).$monetary_unit;
            $payment_total += $this->arrDisp['subtotal'];
        }

        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->lfConvSjis("送料");
        $arrOrder[$i][3] = number_format($arrShipping["fee"]).$monetary_unit;
        $payment_total += $arrShipping["fee"];

        /*
        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->lfConvSjis("(合計)送料");
        $arrOrder[$i][3] = number_format($this->arrDisp['deliv_fee']).$monetary_unit;
         */
        
        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->lfConvSjis("手数料");
        $arrOrder[$i][3] = number_format($this->arrDisp['charge']).$monetary_unit;
        $payment_total += $this->arrDisp['charge'];

        // 総お支払額
        //$this->pdf->Cell(67, 8, $this->lfConvSjis(number_format($this->arrDisp['payment_total'])." 円"), 0, 2, 'R', 0, '');
        $this->pdf->Cell(67, 8, $this->lfConvSjis(number_format($payment_total)." 円"), 0, 2, 'R', 0, '');
        $this->pdf->Cell(0, 57, '', 0, 2, '', 0, '');
        $this->pdf->SetFont('SJIS', '', 8);
        
        /*
        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->lfConvSjis("値引き");
        $arrOrder[$i][3] = "- ".number_format(($this->arrDisp['use_point'] * POINT_VALUE) + $this->arrDisp['discount']).$monetary_unit;
         */

        /*
        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->lfConvSjis("請求金額");
        $arrOrder[$i][3] = number_format($this->arrDisp['payment_total']).$monetary_unit;
         */
        
        $i++;
        $arrOrder[$i][0] = "";
        $arrOrder[$i][1] = "";
        $arrOrder[$i][2] = $this->lfConvSjis("総合計金額");
        $arrOrder[$i][3] = number_format($payment_total).$monetary_unit;


        /*
        // ポイント表記
        if ($this->arrData['disp_point'] && $this->arrDisp['customer_id']) {
            $i++;
            $arrOrder[$i][0] = "";
            $arrOrder[$i][1] = "";
            $arrOrder[$i][2] = "";
            $arrOrder[$i][3] = "";

            $i++;
            $arrOrder[$i][0] = "";
            $arrOrder[$i][1] = "";
            $arrOrder[$i][2] = $this->lfConvSjis("利用ポイント");
            $arrOrder[$i][3] = number_format($this->arrDisp['use_point']).$point_unit;

            $i++;
            $arrOrder[$i][0] = "";
            $arrOrder[$i][1] = "";
            $arrOrder[$i][2] = $this->lfConvSjis("加算ポイント");
            $arrOrder[$i][3] = number_format($this->arrDisp['add_point']).$point_unit;
        }
         */

        $this->pdf->FancyTable($this->label_cell, $arrOrder, $this->width_cell);

    }

    function setEtcData() {
        $this->pdf->Cell(0, 10, '', 0, 1, 'C', 0, '');
        $this->pdf->SetFontSize(9);
        $this->pdf->MultiCell(0, 6, $this->lfConvSjis("＜ 備 考 ＞"), 'T', 2, 'L', 0, '');  //備考
        $this->pdf->Ln();
        $this->pdf->SetFontSize(8);
        $this->pdf->MultiCell(0, 4, $this->lfConvSjis($this->arrData['etc1']."\n".$this->arrData['etc2']."\n".$this->arrData['etc3']), '', 2, 'L', 0, '');  //備考
    }

    function createPdf() {
        // PDFをブラウザに送信
        ob_clean();
        if ($this->pdf_download == 1) {
            if ($this->pdf->PageNo() == 1) {
                $filename = "nouhinsyo-No".$this->arrData['order_id'].".pdf";
            } else {
                $filename = "nouhinsyo.pdf";
            }
            $this->pdf->Output($this->lfConvSjis($filename), 'D');
        } else {
            $this->pdf->Output();
        }

        // 入力してPDFファイルを閉じる
        $this->pdf->Close();
    }

    // PDF_Japanese::Text へのパーサー
    function lfText($x, $y, $text, $size = 0, $style = '') {
        // 退避
        $bak_font_style = $this->pdf->FontStyle;
        $bak_font_size = $this->pdf->FontSizePt;

        $this->pdf->SetFont('', $style, $size);
        $this->pdf->Text($x, $y, $this->lfConvSjis($text));

        // 復元
        $this->pdf->SetFont('', $bak_font_style, $bak_font_size);
    }

    // 受注データの取得
    function lfGetOrderData($order_id) {
        if(SC_Utils_Ex::sfIsInt($order_id)) {
            // DBから受注情報を読み込む
            $objQuery = new SC_Query_Ex();
            $where = "order_id = ?";
            $arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
            $this->arrDisp = $arrRet[0];
            list($point) = SC_Helper_Customer_Ex::sfGetCustomerPoint($order_id, $arrRet[0]['use_point'], $arrRet[0]['add_point']);
            $this->arrDisp['point'] = $point;

            // 受注詳細データの取得
            $arrRet = $this->lfGetOrderDetail($order_id);
            $arrRet = SC_Utils_Ex::sfSwapArray($arrRet);
            $this->arrDisp = array_merge($this->arrDisp, $arrRet);

            // その他支払い情報を表示
            if($this->arrDisp["memo02"] != "") $this->arrDisp["payment_info"] = unserialize($this->arrDisp["memo02"]);
            $this->arrDisp["payment_type"] = "お支払い";
        }
    }

    // 受注詳細データの取得
    function lfGetOrderDetail($order_id) {
        $objQuery = new SC_Query_Ex();
        $col = "product_id, product_class_id, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
        $where = "order_id = ?";
        $objQuery->setOrder("order_detail_id");
        $arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
        return $arrRet;
    }

    // 文字コードSJIS変換 -> japanese.phpで使用出来る文字コードはSJIS-winのみ
    function lfConvSjis($conv_str) {
        return mb_convert_encoding($conv_str, "SJIS-win", CHAR_CODE);
    }

}
?>
