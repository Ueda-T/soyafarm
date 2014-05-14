<?php

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 
 */
class LC_Page_GetProdNameByCd extends LC_Page_Ex {

    /**
     * 
     */
    function process() {
	$result = "該当商品なし";

        if ($s = $this->getProductNameByGoodsNo($_GET['productCode'])) {
	    GC_Utils_Ex::gfPrintLog($s);
            $result = $s;
        }

	header('Access-Control-Allow-Origin: *');
	echo json_encode($result);
	exit;
    }

    /**
     * 
     */
    function getProductNameByGoodsNo($goodsNo) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

	$goodsNo = addslashes($goodsNo);

	$sql =<<<__EOS
SELECT
    concat(p.name, " ", IFNULL(cl.name, '')) AS name
   ,pc.teiki_flg
FROM
    dtb_products_class AS pc
INNER JOIN
    dtb_products AS p
    ON p.product_id = pc.product_id
    AND p.del_flg = 0
    AND p.status = 1
    AND date_format(now(), '%Y%m%d') <=
        date_format(ifnull(p.sale_end_date, now()), '%Y%m%d')
    AND date_format(now(), '%Y%m%d') >=
        date_format(ifnull(p.disp_start_date, now()), '%Y%m%d')
LEFT JOIN
    dtb_class_combination AS cc
    ON cc.class_combination_id = pc.class_combination_id
LEFT JOIN
    dtb_classcategory AS cl
    ON cl.classcategory_id = cc.classcategory_id
WHERE
    pc.product_code = '{$goodsNo}'
AND pc.del_flg = 0
AND ifnull(pc.stock, 1) > 0
__EOS;

	GC_Utils_Ex::gfPrintLog($sql);
        $r = $objQuery->getAll($sql);
        if ($r[0]['name']) {
            return trim($r[0]['name']) . "|" . $r[0]['teiki_flg'];
        }

        return null;
    }
}
?>
