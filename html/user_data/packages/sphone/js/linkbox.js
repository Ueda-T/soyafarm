
/*

Folks, addLoadEvent is a function made by Simon Willison that serves as a manageable window.onload replacement.  Jeremy Keith gave it a thumbs up in his book, DOM Scripting, and it's difficult to walk away from that book without incorporating it into every project.

*/
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}


/*
	Copyright Robert Nyman, http://www.robertnyman.com
	Free to use if this text is included
	
	
Here's the function that allows us to retrieve elements by their class name, something that everyone wishes were part of JavaScript to begin with.  To read more about it, visit Robert Nyman's site.


*/
function getElementsByClassName(oElm, strTagName, strClassName){
	var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/\-/g, "\\-");
	var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++){
		oElement = arrElements[i];		
		if(oRegExp.test(oElement.className)){
			arrReturnElements.push(oElement);
		}	
	}
	return (arrReturnElements)
}

/*

Below are the functions that let me add and remove classes, which I'm using to get the hover effect.  "hovering" is the class that is added or removed, and you can see in the style sheet how .hovering is defined.


*/

function addClassName(oElm, strClassName){
	var strCurrentClass = oElm.className;
	if(!new RegExp(strClassName, "i").test(strCurrentClass)){
		oElm.className = strCurrentClass + ((strCurrentClass.length > 0)? " " : "") + strClassName;
	}
}


function removeClassName(oElm, strClassName){
	var oClassToRemove = new RegExp((strClassName + "\s?"), "i");
	oElm.className = oElm.className.replace(oClassToRemove, "").replace(/^\s?|\s?$/g, "");
}


/*

Here's the part I wrote that uses the functions above to create our "link boxes".

*/


function prepareBoxes() {
	var boxes = getElementsByClassName(document, "div", "linkbox");
	for (var i=0; i<boxes.length; i++){
		var fullstories = getElementsByClassName(document, "p", "fullstory");
		for (var k=0; k<fullstories.length; k++){
			fullstories[k].parentNode.tabIndex = k+1;
			fullstories[k].style.display = "none";
			fullstories[k].parentNode.onmouseover = function() {
				addClassName(this, "hovering");
			}
			fullstories[k].parentNode.onmouseout = function() {
				removeClassName(this, "hovering");
			}
			fullstories[k].parentNode.onclick = function() {
				var destin = this.getElementsByTagName("a");
				window.location = destin[0].href;
			}
			fullstories[k].parentNode.onkeypress = fullstories[k].parentNode.onclick;
		}
	}
}
addLoadEvent(prepareBoxes);