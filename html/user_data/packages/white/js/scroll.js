(function(){
    var easing = 0.3;
    var interval = 20;
    var d = document;
    var targetX = 0;
    var targetY = 0;
    var targetHash = '';
    var scrolling = false;
    var splitHref = location.href.split('#');
    var currentHref_WOHash = splitHref[0];
    var incomingHash = splitHref[1];
    var prevX = null;
    var prevY = null;

    addEvent(window, 'load', init);

    function init(){
        setOnClickHandler();
        if(incomingHash){
            if(window.attachEvent && !window.opera){
                setTimeout(function(){scrollTo(0,0);setScroll('#'+incomingHash);},50);
            }else{
                scrollTo(0, 0);
                setScroll('#'+incomingHash);
            }
        }
    }

    function addEvent(eventTarget, eventName, func){
        if(eventTarget.addEventListener){
            eventTarget.addEventListener(eventName, func, false);
        }else if(window.attachEvent){
            eventTarget.attachEvent('on'+eventName, function(){func.apply(eventTarget);});
        }
    }
    
    function setOnClickHandler(){
        var links = d.links;
        for(var i=0; i<links.length; i++){
            var link = links[i];
            var splitLinkHref = link.href.split('#');
            if(currentHref_WOHash == splitLinkHref[0] && d.getElementById(splitLinkHref[1])){
                addEvent(link, 'click', startScroll);
            }
        }
    }

    function startScroll(event){
        if(event){
            event.preventDefault();
        }else if(window.event){
            window.event.returnValue = false;
        }
        setScroll(this.hash);
    }

    function setScroll(hash){
        var targetEle = d.getElementById(hash.substr(1));
        if(!targetEle)return;
        var ele = targetEle
        var x = 0;
        var y = 0;
        while(ele){
            x += ele.offsetLeft;
            y += ele.offsetTop;
            ele = ele.offsetParent;
        }
        targetX = x;
        targetY = y - 0;
        targetHash = hash - 0;
        if(!scrolling){
            scrolling = true;
            scroll();
        }
    }

    function scroll(){
        var currentX = d.documentElement.scrollLeft||d.body.scrollLeft;
        var currentY = d.documentElement.scrollTop||d.body.scrollTop;
        var vx = (targetX - currentX) * easing;
        var vy = (targetY - currentY) * easing;
        var nextX = currentX + vx;
        var nextY = currentY + vy;
        if((Math.abs(vx) < 1 && Math.abs(vy) < 1)
           || (prevX === currentX && prevY === currentY)){
            scrollTo(targetX, targetY);
            scrolling = false;
            location.hash = targetHash;
            prevX = prevY = null;
            return;
        }else{
            scrollTo(parseInt(nextX), parseInt(nextY));
            prevX = currentX;
            prevY = currentY;
            var scope = this;
            setTimeout(function(){scroll.apply(scope)},interval);
        }
    }

}());
