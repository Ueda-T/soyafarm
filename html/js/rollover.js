// フェードロールオーバー

(function($){
	$.fadeRollover = function(option){
		var conf = $.extend({
			selector: '.img_over',
			attachStr: '_on',
			fadeTime: 300
		}, option)

		var targetImgs = $(conf.selector).not('[src*=' + conf.attachStr + '.]');
		targetImgs.each(function(){
			this.rollOverImg = new Image();
			this.rollOverImg.src = $(this).attr('src').replace(new RegExp('(\.gif|\.jpg|\.png)$'), conf.attachStr + '$1');
			$(this.rollOverImg).css({position: 'absolute', opacity: 0});
			$(this).before(this.rollOverImg);
			$(this.rollOverImg).hover(function(){
				$(this).animate({opacity: 1}, {duration: conf.fadeTime, queue: false});
			},
			function(){
				$(this).animate({opacity: 0}, {duration: conf.fadeTime, queue: false});
			});
		});
	};
})(jQuery);

//透明フェードロールオーバー

$(function() {
	var nav = $('.alpha_over');
	nav.hover(
		function(){
			$(this).fadeTo(100,0.5);
		},
		function () {
			$(this).fadeTo(800,1);
		}
	);
});


//フラッシュロールオーバー

$(function(){

	$(".flash_over").mouseover(function(){

		$(this).css("opacity","0.2").css("filter","alpha(opacity=20)").fadeTo("slow",0.9);

		}).mouseout(function(){

			$(this).fadeTo("fast",1.0);

		});
});


//テキストフラッシュオーバー

$(function(){
   $(".text_flash").mouseover(function(){
      $(this).css("opacity", "0.3");
      $(this).css("filter", "alpha(opacity=30)");
      $(this).fadeTo("slow", 1.0);
   });
});
