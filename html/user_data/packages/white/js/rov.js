/**
* ロールオーバー関数ライブラリ
* 2008/04/06 ver.1.00 新規作成
* 2008/04/22 ver.2.00 IMGタグをAタグ囲まなくても動作するように変更。
* @version 2.00
*/

/**
* ロールオーバークラス
* @access public
*/
function RollOver() {
	this.strDefaultImageSuffix = "0";  //デフォルト画像の接尾辞
	this.strRollOverImageSuffix = "1"; //ロールオーバー画像の接尾辞
	this.strTargetClassName = "swap";  //ロールオーバー対象の画像のクラス名
}

/**
* ロールオーバークラスのプロトタイプ
*/
RollOver.prototype = {
	/**
	* データのロード
	* @access public
	* @return void
	*/
	load : function() {

		var arrImages = document.getElementsByTagName("IMG");
		for(var i=0; i<arrImages.length; i++) {
			var objImage = arrImages[i];
			this.__recursion(objImage, "");
		}

		var arrInput = document.getElementsByTagName("INPUT");
		for(var i=0; i<arrInput.length; i++) {
			var objInput = arrInput[i];
			if(objInput.type.toUpperCase() == "IMAGE") {
				this.__recursion(objInput, "");
			}
		}

	},

	/**
	* データロードのための再帰処理(ver.2.00より再帰しなくなりました)
	* @access private
	* @param object objNode ノードオブジェクト
	* @param string strType タイプ
	* @return void
	*/
	__recursion : function(objNode, strType) {

		var isOK = false;
		if(this.strTargetClassName == "") {
			isOK = true;
		} else {
			if(objNode.className == this.strTargetClassName) {
				isOK = true;
			}
		}

		if(isOK) {
			var objImage = objNode;
			var strImageSrc = objImage.src;

			//拡張子取得
			var objRegex4Extention = new RegExp("\.[a-zA-Z0-9]+?$");
			var strExtension = "" + objRegex4Extention.exec(strImageSrc);

			var strDefaultImageSuffixUS = (this.strDefaultImageSuffix != "") ? "_" + this.strDefaultImageSuffix : "";

			if((new RegExp(strDefaultImageSuffixUS + strExtension + "$")).test(strImageSrc)) {

				//設定したプレフィックスを持つ画像だった場合、ロールオーバー画像を設定する。
				var strRollOverImgSrc =
					strImageSrc.substr(0, strImageSrc.length - strDefaultImageSuffixUS.length - strExtension.length) +
					"_" + this.strRollOverImageSuffix + strExtension;

				//プリロード処理
				var objDefaultImage4Swap = new Image();
				objDefaultImage4Swap.src = strRollOverImgSrc;
				var objRollOverImage4Swap = new Image();
				objRollOverImage4Swap.src = strImageSrc;

				//イベント設定
				objImage.onmouseover = function() {
					objImage.src = strRollOverImgSrc;
				}
				objImage.onmouseout = function() {
					objImage.src = strImageSrc;
				}
			}
		}
	},

	/**
	* デフォルト画像の接尾辞の設定
	* @access public
	* @param string
	* @return void
	*/
	setDefaultImageSuffix : function(strDefaultImageSuffix) {
		this.strDefaultImageSuffix = strDefaultImageSuffix;
	},

	/**
	* デフォルト画像の接尾辞の取得
	* @access public
	* @return string
	*/
	getDefaultImageSuffix : function() {
		return this.strDefaultImageSuffix;
	},

	/**
	* ロールオーバー画像の接尾辞の設定
	* @access public
	* @param string
	* @return void
	*/
	setRollOverImageSuffix : function(strRollOverImageSuffix) {
		this.strRollOverImageSuffix = strRollOverImageSuffix;
	},

	/**
	* ロールオーバー画像の接尾辞の取得
	* @access public
	* @return void
	*/
	getRollOverImageSuffix : function() {
		return this.strRollOverImageSuffix;
	},

	/**
	* ロールオーバー対象の画像のクラス名の設定
	* @access public
	* @param string
	* @return void
	*/
	setTargetClassName : function(strTargetClassName) {
		this.strTargetClassName = strTargetClassName;
	},

	/**
	* ロールオーバー対象の画像のクラス名の取得
	* @access public
	* @return void
	*/
	getTargetClassName : function() {
		return this.strTargetClassName;
	}
};
//
function rOver(){
	var objRollOver = new RollOver();
	objRollOver.setTargetClassName ("swp");
	objRollOver.setDefaultImageSuffix("");
	objRollOver.setRollOverImageSuffix("ov");
	objRollOver.load();
}

//