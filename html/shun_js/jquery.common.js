$(function(){
	$("body").css("font-size",$.cookie('fsize'));
	if($.cookie('fimg') == 'true'){
		$(".navi1 .font img")[0].src = '../shun_img/common/font_small_on.gif';
		$(".navi1 .font img")[1].src = '../shun_img/common/font_big.gif';
	} else {
		$(".navi1 .font img")[0].src = '../shun_img/common/font_small.gif';
		$(".navi1 .font img")[1].src = '../shun_img/common/font_big_on.gif';
	}
});
function font(size,img){
	$("body").css("font-size",size);
	if(img  == 'true'){
		$(".navi1 .font img")[0].src = '../shun_img/common/font_small_on.gif';
		$(".navi1 .font img")[1].src = '../shun_img/common/font_big.gif';
	} else {
		$(".navi1 .font img")[0].src = '../shun_img/common/font_small.gif';
		$(".navi1 .font img")[1].src = '../shun_img/common/font_big_on.gif';
	}
	$.cookie("fsize",size,{expires:30,path:'/'});//※1
	$.cookie("fimg",img,{expires:30,path:'/'});//※1
}



jQuery(document).ready(function($){
// ============================================================================
// 画像のロールオーバー START
// ロールオーバー時の画像名の後ろに「_on」を付ける（例：menu_on.gif）
// PING は IE PNG Fix と競合するので不可


	// 基本（img）
	$('#logo a img').rollover();
	$('#naviH a img').rollover();
	$('#sub .banner a img').rollover();
	$('.top_catch a img').rollover();
	$('#main .banner a img').rollover();
	$('#main #biz #contact a img').rollover();
	$('.review a img').rollover();
	$('.infoTable p.btn a img').rollover();
	$('a.btn img').rollover();

	// inputもいける
	$('form .back input:image').rollover();
	$('form .next input:image').rollover();
	$('form.cart input:image').rollover();
	$('.list form input:image').rollover();
	$('.btn input:image').rollover();

	// 複数指定も楽々
	// $('div#nav a img, form input:image').rollover();
	
	// 引数で _on の部分を指定できる
	// $('div#nav a img').rollover('_over');
});



$(function() {	
// ============================================================================
// 商品一覧
    $('#category .list form:nth-child(4n)').after('<br class="clear" />');
});

$(function() {	
// ============================================================================
// 商品詳細
    $('#goods .item .txt .cart .none').wrap('<div class="stock"></div>');
    $('#goods .item .txt .cart .little').wrap('<div class="stock"></div>');
    $('#category .list .none').after('<br />');
    $('#category .list .little').after('<br />');
});


$(function() {
// ============================================================================
// フォームのフォーカス時
	$('input, textarea, select').focus(function(){
		$(this).addClass("over");
	}).blur(function(){
		$(this).removeClass("over");
	});
});

$(function(){
// ============================================================================
// 検索キーワードの初期値
	$("#sub .search input:text").val("検索キーワード")
	   .css("color","#b1aba4");
	$("#sub .search input:text").focus(function(){
		if(this.value == "検索キーワード"){
			$(this).val("").css("color","#db1aba4");
		}
	});
	$("#sub .search input:text").blur(function(){
		if(this.value == ""){
			$(this).val("検索キーワード")
			     .css("color","#db1aba4");
		}
		if(this.value != "検索キーワード"){
			$(this).css("color","#000");
		}
	});
});

