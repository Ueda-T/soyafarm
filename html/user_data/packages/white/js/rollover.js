function Rollovers(){
  var conf = {
    className : 'btn',
    postfix : '-on'
  };
  var imgNodeList = getElementsByClassName(conf.className);
  var node;
  for (var i=0, len=imgNodeList.length; i<len; i++) {
    node = imgNodeList[i];
    node.originalSrc = node.src;
    node.rolloverSrc = node.originalSrc.replace(/(\.gif|\.jpg|\.png)/, conf.postfix+"$1");
    preloadImage(node.rolloverSrc);
    node.onmouseover = function(){
      this.src = this.rolloverSrc;
    };
    node.onmouseout = function(){
      this.src = this.originalSrc;
    };
  }
};
//�N���X���ɂ��G�������g�m�[�h�z��擾
function getElementsByClassName(name){
  var elements = [];
  var allElements = document.getElementsByTagName('*');
  for (var i=0, len=allElements.length; i<len; i++) {
    if (allElements[i].className == name){
      elements.push(allElements[i]);
    }
  }
  return elements;
}
//�v�����[�h
preloadedImages = [];
function preloadImage(url){
	var p = preloadedImages;
	var l = p.length;
	p[l] = new Image();
	p[l].src = url;
}