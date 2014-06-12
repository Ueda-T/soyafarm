$(document).ready(function() {
	init();
//	setTimeout("showContent()",1500);
	showContent();
	setTimeout("animation()",9600);
//	animation();
});

function init() {
	$('ul.descriptionImgList').show();
	$('ul.descriptionImgList li').css({opacity: 0});
	$('#soyamaru').hide();
	$('#soyaFamName').hide();
}

function animation() {
	$('#soyamaru').fadeIn();
	$('#soyamaru').animate({
					top:"156px"},
					1000,
					function() {
						$('#soyaFamName').fadeIn();
					}
	);
	soyaJump();
}

function showContent() {
	$('ul.descriptionImgList li')
	.css({opacity: 0})
		.each(function(i){
			$(this).delay(1600 * i).animate({opacity:1}, 1500);
		});
}

function soyaJump() {
	$('#soyamaru').animate({top:"-=30px"},1000)
				  .animate({top:"+=30px"},1000);
	setTimeout("soyaJump()",2000);
}

	//soyamaru:ランダム移動
//	$('#soyamaru')
//		.sprite({fps: 8, no_of_frames: 1})
//		.spRandom({
//			top: 30,
//			left: 200,
//			right: 500,
//			bottom: 10,
//			speed: 4000,
//			pause: 2000
//		});