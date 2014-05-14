/**
* ���[���I�[�o�[�֐����C�u����
* 2008/04/06 ver.1.00 �V�K�쐬
* 2008/04/22 ver.2.00 IMG�^�O��A�^�O�͂܂Ȃ��Ă����삷��悤�ɕύX�B
* @version 2.00
*/

/**
* ���[���I�[�o�[�N���X
* @access public
*/
function RollOver() {
	this.strDefaultImageSuffix = "0";  //�f�t�H���g�摜�̐ڔ���
	this.strRollOverImageSuffix = "1"; //���[���I�[�o�[�摜�̐ڔ���
	this.strTargetClassName = "swap";  //���[���I�[�o�[�Ώۂ̉摜�̃N���X��
}

/**
* ���[���I�[�o�[�N���X�̃v���g�^�C�v
*/
RollOver.prototype = {
	/**
	* �f�[�^�̃��[�h
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
	* �f�[�^���[�h�̂��߂̍ċA����(ver.2.00���ċA���Ȃ��Ȃ�܂���)
	* @access private
	* @param object objNode �m�[�h�I�u�W�F�N�g
	* @param string strType �^�C�v
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

			//�g���q�擾
			var objRegex4Extention = new RegExp("\.[a-zA-Z0-9]+?$");
			var strExtension = "" + objRegex4Extention.exec(strImageSrc);

			var strDefaultImageSuffixUS = (this.strDefaultImageSuffix != "") ? "_" + this.strDefaultImageSuffix : "";

			if((new RegExp(strDefaultImageSuffixUS + strExtension + "$")).test(strImageSrc)) {

				//�ݒ肵���v���t�B�b�N�X�����摜�������ꍇ�A���[���I�[�o�[�摜��ݒ肷��B
				var strRollOverImgSrc =
					strImageSrc.substr(0, strImageSrc.length - strDefaultImageSuffixUS.length - strExtension.length) +
					"_" + this.strRollOverImageSuffix + strExtension;

				//�v�����[�h����
				var objDefaultImage4Swap = new Image();
				objDefaultImage4Swap.src = strRollOverImgSrc;
				var objRollOverImage4Swap = new Image();
				objRollOverImage4Swap.src = strImageSrc;

				//�C�x���g�ݒ�
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
	* �f�t�H���g�摜�̐ڔ����̐ݒ�
	* @access public
	* @param string
	* @return void
	*/
	setDefaultImageSuffix : function(strDefaultImageSuffix) {
		this.strDefaultImageSuffix = strDefaultImageSuffix;
	},

	/**
	* �f�t�H���g�摜�̐ڔ����̎擾
	* @access public
	* @return string
	*/
	getDefaultImageSuffix : function() {
		return this.strDefaultImageSuffix;
	},

	/**
	* ���[���I�[�o�[�摜�̐ڔ����̐ݒ�
	* @access public
	* @param string
	* @return void
	*/
	setRollOverImageSuffix : function(strRollOverImageSuffix) {
		this.strRollOverImageSuffix = strRollOverImageSuffix;
	},

	/**
	* ���[���I�[�o�[�摜�̐ڔ����̎擾
	* @access public
	* @return void
	*/
	getRollOverImageSuffix : function() {
		return this.strRollOverImageSuffix;
	},

	/**
	* ���[���I�[�o�[�Ώۂ̉摜�̃N���X���̐ݒ�
	* @access public
	* @param string
	* @return void
	*/
	setTargetClassName : function(strTargetClassName) {
		this.strTargetClassName = strTargetClassName;
	},

	/**
	* ���[���I�[�o�[�Ώۂ̉摜�̃N���X���̎擾
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