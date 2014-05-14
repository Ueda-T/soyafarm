$(function() {
    /*
     * メールアドレス
     */
    var _mailaddr = function(e) {
	var re = /^(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+))*)|(?:\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\])))$/;
	    var addr = $(this).val();

	    if (addr) {
                if (re.test(addr) == false) {
                    $("#" + e.data.relative).css('display', 'inline');
                } else {
                    $("#" + e.data.relative).css('display', 'none');
		}
            }
        };
	$('#email').on("keyup", {relative: "email-navi"}, _mailaddr);
	$('#email02').on("keyup", {relative: "email02-navi"}, _mailaddr);
        $("#email-navi").css('display', 'none');
        $("#email02-navi").css('display', 'none');

	/*
	 * 以下パスワード強度チェック
	 */
        $('.password-container').pschecker({onPasswordValidate: validatePassword, onPasswordMatch: matchPassword});

        var submitbutton = $('.submit-button');
        var errorBox = $('.error');
        errorBox.css('visibility', 'hidden');
        submitbutton.attr("disabled", "disabled");

        //this function will handle onPasswordValidate callback, which mererly checks the password against minimum length
        function validatePassword(isValid) {
            if (!isValid) {
                errorBox.css('visibility', 'visible');
            } else {
                errorBox.css('visibility', 'hidden');
            }
        }

        //this function will be called when both passwords match
        function matchPassword(isMatched) {
            if (isMatched) {
                submitbutton.addClass('unlocked').removeClass('locked');
                submitbutton.removeAttr("disabled", "disabled");
            } else {
                submitbutton.attr("disabled", "disabled");
                submitbutton.addClass('locked').removeClass('unlocked');
            }
        }

	/*
	 * 全半角を考慮し文字列のバイト数を求める
	 */
	function strlen(s) {
	    var length = 0;
	    s = escape(s);
            for (i = 0; i < s.length; ++i, ++length) {
                if (s.charAt(i) == "%") {
                    if (s.charAt(++i) == "u") {
                       i += 3;
                       ++length;
                    }
                    ++i;
                }
            }
            return length;
        }

	function setlimit() {
            // 選択された都道府県の文字列長を調べる
	    var len = strlen($('#pref option:selected').text());
            // ガイダンス文字が選択された場合
	    if (8 < len) {
                len = 0;
	    }
            $("#addr1").attr("maxlength", 40 - len);
        }

	$('#pref').change(function() {
	    setlimit();
	    $('#addr1').keyup();
        });

	$("#other_addr_flg").change(function() {
		if ($("#other_addr_flg").is(":checked")) {
			$("#other_addr_box").show();
		} else {
			$("#other_addr_box").hide();
		}
	});


	/*
	 * 住所簡易入力
	 */
        var _lookup = function() {
	    var zip = $("#zip").val();
	    if (zip) {
	        $.ajax({
                    url: get_zip_addr,
                    data: {
		        zip:zip
		    },
		    success: function(result) {
		        addrs = result.split("||");
			naddr = addrs.length;
                        $("#addr1").remove();

			if (naddr > 1) {
			    $("#addr1-div").html('<select name="addr01" id="addr01">');

 			    for (var i = 0; i < naddr; ++i) {
                                e = addrs[i].split("|");
                                if (e.length < 3) {
			            continue;
                                }
                                $("#pref").val(e[0]);
                                s = e[1] + e[2];
		                $("#addr01").append('<option label="' + s + '" value="' + s + '">' + s + '</option>');
			    }
			} else {
			    $("#addr1-div").html('<input type="text" name="addr01" id="addr01" class="box300" value=""; ime-mode: active;>');
                            e = addrs[0].split("|");
			    if (e.length < 3) {
			        return;
                            }
			    $("#pref").val(e[0]);
			    $("#addr01").val(e[1] + e[2]);
			    $("#addr01").removeClass("valNone").addClass("selected");
			    // 住所１の入力文字数を制限
			    setlimit();
			}
		    },
		    error: function(data) {
		        alert("error.");
		    },
	        });
	    }
	};
	$('#zip').on("blur", _lookup);
	$('#easy').on("click", _lookup);

	/*
	 * 住所欄の文字数制限警告表示
	 */
        var _strlimit = function(e) {
	    if ($(this).attr("maxlength") < strlen($(this).val())) {
                $("#" + e.data.relative).css('display', 'inline');
            } else {
                $("#" + e.data.relative).css('display', 'none');
            }
        }
        $('#addr1').on("keyup", {relative: "addr1-navi"}, _strlimit);
        $('#addr2').on("keyup", {relative: "addr2-navi"}, _strlimit);
        $("#addr1-navi").css('display', 'none');
        $("#addr2-navi").css('display', 'none');
        $('#addr1').keyup();
        $('#addr2').keyup();

	/*
	 * 番地なし
	 */
        var _houseno = function(e) {
            var s = "";
            if ($(this).prop('checked')) {
                s = "番地なし";
            }
            $("#" + e.data.relative).val(s);
        };
	$('#house_no').on("change", {relative: "addr2"}, _houseno);

	/*
	 * メールマガジン
	 */
        // 初期表示
        if ($("input:radio[name='mailmaga_flg']:checked").val() == 0) {
            $('#notReceive').fadeIn("slow");
        }
        var _mailmag = function() {
            if ($('#mailmaga_flg_0').prop('checked')) {
                $('#notReceive').fadeIn("slow");
            } else {
                $('#notReceive').fadeOut();
            }
        };
	$('#mailmaga_flg_0, #mailmaga_flg_1').on("change", _mailmag);

	/*
	 * アンケート
	 */
        var _questionary = function() {
            if ($('#questionary').val() < 50) {
                $("#other").attr("disabled", "disabled");
                $("#otherMessage").text('');
            } else {
                $("#other").removeAttr("disabled");
       		var s = $('#questionary option:selected').text() + "を選択された場合、何でお知りになりましたか？";
                $("#otherMessage").text(s);
            }
        };
	$('#questionary').on("change", _questionary);

	/*
	 * フリガナ自動入力の設定
	 */
        $.fn.autoKana('#userName', '#userFurigana', {katakana:true});
        $.fn.autoKana('#userNameSei', '#userFuriganaSei', {katakana:true});
        $.fn.autoKana('#userNameMei', '#userFuriganaMei', {katakana:true});
        $.fn.autoKana('#userNameSeiShip', '#userFuriganaSeiShip', {katakana:true});
        $.fn.autoKana('#userNameMeiShip', '#userFuriganaMeiShip', {katakana:true});

	/*
	 * 自動フィールド移動の設定
	 */
        $('#zip').autotab_magic().autotab_filter({format: 'custom', pattern: '[^0-9-]'});
        $('#tel').autotab_magic().autotab_filter({format: 'custom', pattern: '[^0-9-]'});
        $('#shipping_zip').autotab_magic().autotab_filter({format: 'custom', pattern: '[^0-9-]'});
        $('#shipping_tel').autotab_magic().autotab_filter({format: 'custom', pattern: '[^0-9-]'});

	/*
	 * お届け先用
	 */
	function setlimitShipping() {
            // 選択された都道府県の文字列長を調べる
	    var len = strlen($('#shipping_pref option:selected').text());
            // ガイダンス文字が選択された場合
	    if (8 < len) {
                len = 0;
	    }
            $("#shipping_addr01").attr("maxlength", 40 - len);
        }

	$('#shipping_pref').change(function() {
	    setlimitShipping();
	    $('#shipping_addr01').keyup();
        });

	/*
	 * 住所簡易入力
	 */
        var _lookupShip = function() {
	    var zip = $("#shipping_zip").val();
	    if (zip) {
	        $.ajax({
                    url: get_zip_addr,
                    data: {
		        zip:zip
		    },
		    success: function(result) {
		        addrs = result.split("||");
			length = addrs.length;
                        $("#shipping_addr1").remove();

			if (length > 1) {
			    $("#shipping-addr1-div").html('<select name="shipping_addr01" id="shipping_addr01">');

 			    for (var i = 0; i < length; ++i) {
                                e = addrs[i].split("|");
                                if (e.length < 3) {
			            continue;
                                }
                                $("#shipping_pref").val(e[0]);
                                s = e[1] + e[2];
		                $("#shipping_addr01").append('<option label="' + s + '" value="' + s + '">' + s + '</option>');
			    }
			} else {
			    $("#shipping-addr1-div").html('<input type="text" name="shipping_addr01" id="shipping_addr01" class="box300" value=""; ime-mode: active;>');
                            e = addrs[0].split("|");
			    if (e.length < 3) {
			        return;
                            }
			    $("#shipping_pref").val(e[0]);
			    $("#shipping_addr01").val(e[1] + e[2]);
			    $("#shipping_addr01").removeClass("valNone").addClass("selected");
			    // 住所１の入力文字数を制限
			    setlimitShipping();
			}
		    },
		    error: function(data) {
		        alert("error.");
		    },
	        });
	    }
	};
	$('#shipping_zip').on("blur", _lookupShip);
	$('#shipping_easy').on("click", _lookup);

	/*
	 * 住所欄の文字数制限警告表示
	 */
        var _strlimitShip = function(e) {
	    if ($(this).attr("maxlength") < strlen($(this).val())) {
                $("#" + e.data.relative).css('display', 'inline');
            } else {
                $("#" + e.data.relative).css('display', 'none');
            }
        }
        $('#shipping_addr01').on("keyup", {relative: "shipping_addr1-navi"}, _strlimitShip);
        $('#shipping_addr02').on("keyup", {relative: "shipping_addr2-navi"}, _strlimitShip);
        $("#shipping_addr1-navi").css('display', 'none');
        $("#shipping_addr2-navi").css('display', 'none');
        $('#shipping_addr01').keyup();
        $('#shipping_addr02').keyup();

	/*
	 * 番地なし
	 */
        var _housenoShip = function(e) {
            var s = "";
            if ($(this).prop('checked')) {
                s = "番地なし";
            }
            $("#" + e.data.relative).val(s);
        };
	$('#shipping_house_no').on("change", {relative: "shipping_addr02"}, _housenoShip);





    }
);
