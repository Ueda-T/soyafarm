// フォームの未入力時と入力後に背景色を追加
$(function(){
	$("input[type=text],input[type=password],select,option,textarea").each(function(){
		if ($(this).val()) {
			$(this).removeClass("valNone").addClass("selected");
		} else {
			$(this).removeClass("selected").addClass("valNone");
		}
		$(this).focus(function() {
			$(this).removeClass("valNone").addClass("selected");
		})
		.blur(function() {
			if ($(this).val()) {
				$(this).removeClass("valNone").addClass("selected");
			} else {
				$(this).removeClass("selected").addClass("valNone");
			}
		})
		.change(function() {
			if($(this).val()) {
				$(this).removeClass("valNone").addClass("selected");
			} else {
				$(this).removeClass("selected").addClass("valNone");
			}
		});
	});
});
