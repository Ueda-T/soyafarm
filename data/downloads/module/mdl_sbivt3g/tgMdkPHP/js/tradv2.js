/**
 * Version     : 1.2.0.0
 * Created     : 2011.04.01
 * Modified    : 2013.10.28
*/

//*********************************
//* 初期設定
//*********************************
// ASIタグ設定
var asi_id  = 'D10900';
var asi_url = location.protocol + '//js.revsci.net/gateway/gw.js?csid=' + asi_id;
loadAsiJs( asi_url, true );


/**
* setPcHash()
* PCサイト向けURL変換パラメータ設定
* 
* param : None 
* return: def_hash (array: URL変換パラメータ)
*/
function setPcHash() {

    var def_hash = new Array();
    def_hash[0]  = { "old_key" : "", "new_key" : "tag",    "val" : "scr"}
    def_hash[1]  = { "old_key" : "", "new_key" : "req",    "val" : "xo"}
    def_hash[2]  = { "old_key" : "", "new_key" : "cat",    "val" : "trAd.pc"}
    def_hash[3]  = { "old_key" : "", "new_key" : "format", "val" : "trAd"}

    return def_hash;

}


/**
* setSpHash()
* スマートフォンサイト向けURL変換パラメータ設定
* 
* param : None 
* return: def_hash (array: URL変換パラメータ)
*/
function setSpHash() {

    var def_hash = new Array();
    def_hash[0]  = { "old_key" : "", "new_key" : "tag",    "val" : "scr"}
    def_hash[1]  = { "old_key" : "", "new_key" : "req",    "val" : "xo"}
    def_hash[2]  = { "old_key" : "", "new_key" : "cat",    "val" : "trAd.sp"}
    def_hash[3]  = { "old_key" : "", "new_key" : "format", "val" : "trAd"}
    
    return def_hash;

}


//*********************************
//* 広告タグ 関連
//*********************************

/**
 * isAdEnable( tran_str )
 * 取引コードから特定の文字列を検索する。
 * 
 * param : tran_str (string : 取引コード)
 * return: boolean
 */
function isAdEnable( tran_str ) {

    // 初期設定
    var needle = 'H001';  // 検索キー
    
    // 取引コード文字列から検索キーを検索する。
    if ( tran_str.search(needle) != -1 ) {
        return true;
    } else {
        return false;
    }

}


/**
 * replaceCB( card_str )
 * 特定のカード名をカードブランド名に変換する。
 * 
 * param : card_str   (string : カード名)
 * return: card_brand (string : カードブランド名)
 */
function replaceCB( card_str ) {

    // 初期設定
    // 検索キー、カードブランド名
    var repVal = {
            "DINERS" : "DINERS_CB",
            "AMEX"   : "AMEX_CB"
          };
    
    var card_brand = ""
    
    // 検索キーに一致したカードブランド名をセットする。
    for ( key in repVal ) {
        if ( card_str == key ) {
            card_brand = repVal[key];
        }
    }

    // 検索キーに一致しなかった場合、カード名をセットする。
    if ( card_brand == "" ) {
        var card_brand = card_str;
    }
    
    return card_brand;

}


/**
 * getParamUrl( url_str )
 * url文字列からパラメータ部分を抽出する。
 *
 * param : url_str (string : url)
 * return: hash    (array  : urlパラメータ配列)
 */
function getParamURL( url_str ) {

    // 初期設定
    var hash     = new Array();
    var key_hash = {
            "brand"  : "trAd-brand",
            "sz"     : "",
            "grade"  : "trAd-grade",
            "class"  : "trAd-class",
            "amount" : "trAd-amount",
            "ord"    : ""
          };
    
    
    // 正規表現によるurlホスト部定義
    var regxp = /https?:\/\/[0-9a-zA-Z,;:~&=@_'%?+-/$.!*()]+\//i;
    
    // url文字列からパラメーター部を抽出する。
    var url_param_str = url_str.replace( regxp, '' );
    
    // パラメーター部を区切り文字(;)で分解する。
    var url_param_arr = url_param_str.split( ";" );
    
    // 分解されたパラメータを配列に格納する。
    for ( var i = 0; i < url_param_arr.length; i++ ) {
    
        if ( url_param_arr[i].search( "=" ) == '-1' ) {
            // 加盟店コードの値を抽出する。
            hash[i] = { old_key : "", new_key : "trAd-mid", val : url_param_arr[i] };
            
        } else {
            // 分解されたパラメータを区切り文字(=)で分解し、キーと値に別ける。
            var split_param = url_param_arr[i].split( "=" );
            
            // カードブランド名を置換する。
            if ( split_param[0] == 'brand' ) {
                split_param[1] = replaceCB( split_param[1] );
            }
            
            hash[i] = { old_key : split_param[0], new_key : key_hash[split_param[0]], val : split_param[1] };

        }

    }
    
    return hash;

}


/**
 * advTag( def_hash, tran_str, url_str, asi_flag )
 * Advantage広告リクエストタグを書出す。
 * 
 * param : def_hash (array   : URL変換パラメータ)
 *         tran_str (string  : 取引コード)
 *         url_str  (string  : url)
 *         asi_flag (boolean : ASIフラグ)
 * return: null
 */
function advTag( def_hash, tran_str, url_str, asi_flag ) {

    // url文字列からパラメーター配列を作成する。
    var hash = getParamURL( url_str );
    
    // 広告表示制御
    if ( isAdEnable(tran_str) == true ) {

        // Advantage広告タグ作成リクエストURL        
        var new_url_base  = location.protocol + '//web-jp.ad-v.jp/adam/detect?';
        var new_url_param = "";
        
        // パラメーター配列を結合する。
        var cmb_hash = def_hash.concat( hash );
        
        // パラメータ配列を展開し、パラメータ文字列を作成する。
        for ( var i = 0; i < cmb_hash.length; i++ ) {
            if ( cmb_hash[i].new_key != '' ) {
                new_url_param += cmb_hash[i].new_key + '=' + cmb_hash[i].val + '&';
            }
        }
        
        // 最後の1文字を除去する。
        new_url_param = new_url_param.substr( 0, new_url_param.length-1 );
        
        // Advantage広告タグ作成リクエストURLとパラメータ文字列を結合する。
        var new_url = new_url_base + new_url_param;
        
        // Advantage広告リクエストタグを書出す。
        document.write( '<script type="text/javascript" src="' + new_url + '"></' + 'script>' );
        
    }

    // ASIの呼出し制御
    execAsi( hash, url_str, asi_flag );

}


/**
 * loadAsiJs( src, asi_flag )
 * asi外部ファイルを取得の為のscriptタグを出力する。
 * 
 * param : src      (string  : 外部ファイルurl)
 *         asi_flag (boolean : ASIフラグ)
 * return: none
 */
function loadAsiJs( src, asi_flag ) {

    // ASIの書出しフラグをセットする。
    var isAsiEnable = new Boolean( asi_flag );
    
    if ( isAsiEnable == true ) {
        document.write( '<script type="text/javascript" src="' + src + '"></' + 'script>' );
    }

}


/**
 * execAsi( url_str, asi_flag )
 * ASIタグを実行する。
 * 
 * param : hash     (array   : urlパラメータ配列)
 *         url_str  (string  : url)
 *         asi_flag (boolean : ASIフラグ)
 * return: none
 */
function execAsi( hash, url_str, asi_flag ) {

    // ASIの書出しフラグをセットする。
    var isAsiEnable = new Boolean( asi_flag );
    
    if ( isAsiEnable == true ) {

        if ( hash == null) {
            // url文字列からパラメーター配列を作成する。
            var hash = getParamURL( url_str );
        }

        var showAsi = function() {
            document.write( '<script type="text/javascript">' );
            document.write( asi_id + '.DM_addEncToLoc("brand" , "' + hash[1].val + '");' );
            document.write( asi_id + '.DM_addEncToLoc("grade" , "' + hash[3].val + '");' );
            document.write( asi_id + '.DM_addEncToLoc("class" , "' + hash[4].val + '");' );
            document.write( asi_id + '.DM_addEncToLoc("amount", "' + hash[5].val + '");' );
            document.write( asi_id + '.DM_addEncToLoc("mid"   , "' + hash[0].val + '");' );
            document.write( asi_id + '.DM_tag();' );
            document.write( '</' + 'script>' );
        };

        // ASIタグを書出す。
        showAsi();

    }

}


/**
 * showAdv( tran_str, url_str )
 * Advantage広告リクエストタグをASIタグと共に書出す。(PCサイト向け)
 * 
 * param : tran_str (string  : 取引コード)
 *         url_str  (string  : url)
 * return: null
 */
function showAdv( tran_str, url_str ) {

    var def_hash = setPcHash();
    advTag( def_hash, tran_str, url_str, false );

}

function showAdv4Mpi( url_str ) {

    var def_hash = setPcHash();
    var tran_str = "";
    if (url_str != "") {
        tran_str = "H001";
    }
    advTag( def_hash, tran_str, url_str, false);
}


/**
 * showAdv4SP(tran_str, url_str)
 * Advantage広告リクエストタグをASIタグと共に書出す。(SmartPhoneサイト向け)
 * 
 * param : tran_str (string  : 取引コード)
 *         url_str  (string  : url)
 * return: null
 */
function showAdv4SP( tran_str, url_str ) {

    var def_hash = setSpHash();
    advTag( def_hash, tran_str, url_str, false );

}


/**
 * advTagAsiOFF( tran_str, url_str )
 * Advantage広告リクエストタグをのみを書出す。(PCサイト向け)
 * 
 * param : tran_str (string : 取引コード)
 *         url_str  (string : url)
 * return: null
 */
function advTagAsiOFF( tran_str, url_str ) {

    var def_hash = setPcHash();
    advTag(def_hash, tran_str, url_str, false );

}


/**
 * advTagAsiOFF( tran_str, url_str )
 * Advantage広告リクエストタグのみを書出す。(SmartPhoneサイト向け)
 * 
 * param : tran_str (string : 取引コード)
 *         url_str  (string : url)
 * return: null
 */
function advTagAsiOff4SP( tran_str, url_str ) {

    var def_hash = setSpHash();
    advTag(def_hash, tran_str, url_str, false );

}