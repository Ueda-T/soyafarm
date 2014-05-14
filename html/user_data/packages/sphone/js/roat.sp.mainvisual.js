;jQuery(function($){
	// スライドの間隔
	var slideDuration = 5000;

	
	var $container = $('#spMainVisual');
	var $wrapper = $('#spMainVisualMainWrapper');
	var $mainVisual = $('#spMainVisualMain');
	var $tabList = $('#spMainVisualTab');

	var strUA = navigator.userAgent.toLowerCase(),
		webkitUA = ['iphone','android','ipad'],
		runLayout = false,
		currentX = 0,
		maxX = 0,
		diffX = 0,
		slideLock = 1,
		slideTimer = {},
		slideCount = 0,
		orientationChangeDelay = 0;




		
	for (var i = 0; i < webkitUA.length; i ++) { 
		if(strUA.indexOf(webkitUA[i],0) != -1){
			runLayout = true;
			if(webkitUA[i]==='android') {
				orientationChangeDelay = 400
			}
			if(webkitUA[i]==='iphone') { 
				orientationChangeDelay=0
			}
		}
	}


	
//	if(runLayout !== true) {
//			return;
//	}
		
		
		
	function orientationChangeCore(){
		clearTimeout(slideTimer);
		setTimeout(function(){
			resize();
			slideTimer = setTimeout(rotation, slideDuration);
//			var styles = getComputedStyle($('.moveWrap').get(0));
//			if (styles) {
//					$('.resizable').css('width', styles.width);
//					$('.slideMask').css('height',$('.move').outerHeight())
//						.css('width',styles.width-1);
//					
//					maxX = Number($('.flickSlideContainer li.slideUnit').length-1)
//						* Number(styles.width.replace('px','')) * -1;
//					
//					$('div.flickSlideContainer ul.move').get(0).style.webkitTransform='translate3d(0,0,0)';
//					currentX = 0;
//					slideCount = 0;
//					
//					updateTab();
//					slideTimer = setTimeout(rotation, slideDuration);
//			} else {
//			}
		}, orientationChangeDelay);
	}
	function resize() {
		var w = $(window).width();
		if (w < 640) {
			$container.css('zoom', w / 640);
		}
	}

	
	function rotation(){
		clearTimeout(slideTimer);
		if (slideLock === 0) {
			var slideUnitWidth = $mainVisual.children().outerWidth();
			$mainVisual.get(0).style.webkitTransition = '-webkit-transform 0.6s ease-out';
			diffX = - 151;
			if (currentX === maxX) {
				$mainVisual.get(0).style.webkitTransform = 'translate3d(0, 0, 0)';
				currentX = 0;
				slideCount = 0;
				updateTab();
			} else {
				currentX = currentX - slideUnitWidth;
				$mainVisual.get(0).style.webkitTransform = 'translate3d(' + currentX + 'px, 0, 0)';
				slideCount ++;
				updateTab();
			}
		}
		slideLock = 0;
		slideTimer = setTimeout(rotation, slideDuration);
	}

	
	
	function updateTab() {
		$tabList.find('img').each(function(index){
			var src = $(this).attr('src');
			if (index == slideCount) {
				$(this).attr('src', src.replace(/(_act)?\.gif$/, '_act.gif'));
			} else {
				$(this).attr('src', src.replace(/_act\.gif$/, '.gif'));
			}
		});
		
	}
	

		
	
	
	


	var startX = 0,startY = 0, moveX = 0;
	function touchHandler(e) {
		
		var slideUnitWidth = $mainVisual.children().outerWidth();
		var touch = e.originalEvent.touches[0];
		if (e.type == "touchstart") {
			clearTimeout(slideTimer);
			startX = touch.pageX;
			startY = touch.pageY;
			startTime = (new Date()).getTime();
		} else if (e.type == "touchmove") {	
			diffX = touch.pageX - startX;
			diffY = touch.pageY - startY;
			// マルチタッチ
			if (e.originalEvent.touches.length > 1) {
				// ピンチで拡大縮小を妨げないためになにもしない
			} else if (Math.abs(diffX) - Math.abs(diffY) > 0) {
				e.preventDefault();
				moveX = Number(currentX + diffX);
				$mainVisual.css('-webkit-transition', 'none');
				$mainVisual.get(0).style.webkitTransform = 'translate3d( ' + moveX + 'px, 0, 0)';
			}
		} else if (e.type=="touchend") {
			var endTime = (new Date()).getTime();
			var diffTime = endTime-startTime;
			if (diffTime < 300) {
				$mainVisual.get(0).style.webkitTransition = '-webkit-transform 0.5s ease-out';
			} else {
				$mainVisual.get(0).style.webkitTransition = '-webkit-transform 0.6s ease-out';
			}
			if (diffX>150 || (diffX > 60 && diffTime < 400 && orientationChangeDelay === 0)) {
				if (currentX == 0) {
					$mainVisual.get(0).style.webkitTransform = 'translate3d(0, 0, 0)';
				} else {
					currentX = currentX + slideUnitWidth;
					$mainVisual.get(0).style.webkitTransform = 'translate3d(' + currentX + 'px, 0, 0)';
					slideCount --;
					updateTab();
				}
			} else if (diffX < -150 || (diffX < -60 && diffTime < 400 && orientationChangeDelay === 0)) {
				if (currentX === maxX) {
					$mainVisual.get(0).style.webkitTransform = 'translate3d(' + maxX + 'px, 0, 0)';
				} else {
					currentX = currentX - slideUnitWidth;
					$mainVisual.get(0).style.webkitTransform = 'translate3d(' + currentX + 'px, 0, 0)';
					slideCount ++;
					updateTab();
				}
			} else {
				if (currentX === 0) {
					$mainVisual.get(0).style.webkitTransform = 'translate3d(0, 0, 0)';
				} else if (currentX === maxX) {
					$mainVisual.get(0).style.webkitTransform = 'translate3d(' + maxX + 'px, 0, 0)';
				} else {
					$mainVisual.get(0).style.webkitTransform = 'translate3d(' + currentX + 'px, 0, 0)';
				}
			}
			slideTimer = setTimeout(rotation, slideDuration);
			slideLock = 0;
		}
	}


	// 初期化
	function initialize() {
		// サイズをあわせて
		resize();
		maxX = Number($mainVisual.children().size() - 1) * 640 * -1;
		
		// 表示
		$container.show();
		// タブを切り替え
		updateTab();
		// タイマー開始
		slideTimer = setTimeout(rotation,slideDuration);
		
		// タッチ処理
		$mainVisual.bind('touchstart', {type: 'start'}, touchHandler);
		$mainVisual.bind('touchmove', {type:'move'}, touchHandler);
		$mainVisual.bind('touchend', {type:'end'}, touchHandler);
		
		// タブクリック処理
		$tabList.find('a').click(function(){
			var index = $tabList.children().index($(this).parent());
			if (index != slideCount) {
				var slideUnitWidth = 640;
				slideLock = 1;
				clearTimeout(slideTimer);
				$mainVisual.get(0).style.webkitTransition = '-webkit-transform 0.6s ease-out';
				
			
				currentX = index * slideUnitWidth * -1;
				$mainVisual.get(0).style.webkitTransform = 'translate3d(' + currentX + 'px, 0, 0)';
				slideCount = index;
				updateTab();
				slideTimer = setTimeout(rotation, slideDuration);
				slideLock = 0;
				
			}
			return false;
		});
		
		
		// 回転イベント処理を設定
		window.addEventListener("orientationchange", function(){
			if (runLayout !== true){
				return;
			}
			switch(window.orientation){
				case 0:
					orientationChangeCore();
					break;
				case 90:
					orientationChangeCore();
					break;
				case-90:
					orientationChangeCore();
					break;
			}
		},false);
	
	}
	
	
	
	// 画像を先読み
	var $dummyDiv = $('<div />').css('display', 'none');
	$tabList.find('img').each(function(){
		var src = $(this).attr('src').replace(/\.gif$/, '_act.gif');
		$dummyDiv.append($('<img />').attr('src', src));
	});
	$('body').append($dummyDiv);
	$dummyDiv.imagesLoaded(function(){
		initialize();
	});
	
});
	
	