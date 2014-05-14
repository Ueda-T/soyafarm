<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once DATA_REALDIR . 'module/gdthumb.php';

/**
 * リサイズイメージ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_ResizeImage.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_ResizeImage extends LC_Page_Ex {

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
        $objThumb = new gdthumb();

        $file = NO_IMAGE_REALDIR;

        // NO_IMAGE_REALDIR以外のファイル名が渡された場合、ファイル名のチェックを行う
        if (strlen($_GET['image']) >= 1 && $_GET['image'] !== NO_IMAGE_REALDIR) {

            // ファイル名が正しく、ファイルが存在する場合だけ、$fileを設定
            if (!$this->lfCheckFileName()) {
                GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php $_GET[\'image\']=' . $_GET['image']);
            }
            else if (file_exists(IMAGE_SAVE_REALDIR . $_GET['image'])) {
                $file = IMAGE_SAVE_REALDIR . $_GET['image'];
            }
        }

        $objThumb->Main($file, $_GET['width'], $_GET['height'], "", true);
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
     * ファイル名の形式をチェック.
     *
     * @return boolean 正常な形式:true 不正な形式:false
     */
    function lfCheckFileName() {
        //$pattern = '|^[0-9]+_[0-9a-z]+\.[a-z]{3}$|';
        $pattern = '|\./|';
        $file    = trim($_GET['image']);
	// 脆弱性パッチ対応
        //if ( preg_match_all($pattern, $file, $matches) ) {
	if (!preg_match("/^[[:alnum:]_\.-]+$/i", $file)) {
            return false;
        } else {
            return true;
        }
    }
}
?>
