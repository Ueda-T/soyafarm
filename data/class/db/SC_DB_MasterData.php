<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * マスターデータを扱うクラス.
 *
 * プルダウン等で使用するマスターデータを扱う.
 * マスターデータは, DB に格納されているが, パフォーマンスを得るため,
 * 初回のみ DBへアクセスし, データを定義したキャッシュファイルを生成する.
 *
 * マスターデータのテーブルは, 下記のようなカラムが必要がある.
 * 1. キーとなる文字列
 * 2. 表示文字列
 * 3. 表示順
 * 上記カラムのデータ型は特に指定しないが, 1 と 2 は常に string 型となる.
 *
 * マスターデータがキャッシュされると, key => value 形式の配列として使用できる.
 * マスターデータのキャッシュは, MASTER_DATA_REALDIR/マスターデータ名.php というファイルが生成される.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_DB_MasterData.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_DB_MasterData {

    // {{{ properties

    /** SC_Query インスタンス */
    var $objQuery;

    /** デフォルトのテーブルカラム名 */
    var $columns = array('id', 'name', 'rank', 'remarks');

    // }}}
    // {{{ functions

    /**
     * マスターデータを取得する.
     *
     * 以下の順序でマスターデータを取得する.
     * 1. MASTER_DATA_REALDIR にマスターデータキャッシュが存在しない場合、
     *    DBからマスターデータを取得して、マスターデータキャッシュを生成する。
     * 2. マスターデータキャッシュを読み込み、変数に格納し返す。
     *
     * 返り値は, key => value 形式の配列である.
     *
     * @param string $name マスターデータ名
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @return array マスターデータ
     */
    function getMasterData($name, $columns = array()) {

        $columns = $this->getDefaultColumnName($columns);

        $filepath = MASTER_DATA_REALDIR . $name . '.serial';

        if (!file_exists($filepath)) {
            // キャッシュ生成
            $this->createCache($name, $columns);
        }

        // キャッシュを読み込み
        $masterData = unserialize(file_get_contents($filepath));

        return $masterData;
    }

    /**
     * マスターデータをDBに追加する.
     *
     * 引数 $masterData をマスターデータとしてDBに追加し,
     * キャッシュを生成する.
     * 既存のキャッシュが存在する場合は上書きする.
     * $masterData は key => value 形式の配列である必要がある.
     *
     * @param string $name マスターデータ名
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @param array $masterData マスターデータ
     * @param bool $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスターデータの登録数
     */
    function registMasterData($name, $columns, $masterData, $autoCommit = true) {

        $columns = $this->getDefaultColumnName($columns);

        $this->objQuery = new SC_Query_Ex();
        if ($autoCommit) {
            $this->objQuery->begin();
        }
        $i = 0;
        foreach ($masterData as $key => $val) {
            $sqlVal = array($columns[0] => (string) $key,
                            $columns[1] => (string) $val,
                            $columns[2] => (string) $i);
            $this->objQuery->insert($name, $sqlVal);
            $i++;
        }
        if ($autoCommit) {
            $this->objQuery->commit();
        }
        return $i;
    }

    /**
     * マスターデータを更新する.
     *
     * 引数 $masterData の値でマスターデータを更新する.
     * $masterData は key => value 形式の配列である必要がある.
     *
     * @param string $name マスターデータ名
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @param array $masterData マスターデータ
     * @param bool $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスターデータの更新数
     */
    function updateMasterData($name, $columns, $masterData, $autoCommit = true) {

        $columns = $this->getDefaultColumnName($columns);

        $this->objQuery = new SC_Query_Ex();
        if ($autoCommit) {
            $this->objQuery->begin();
        }

        // 指定のデータを更新
        $i = 0;
        foreach ($masterData as $key => $val) {
            $sqlVal = array($columns[1] => $val);
            $this->objQuery->update($name, $sqlVal, $columns[0] . " = " .  SC_Utils_Ex::sfQuoteSmart($key));
            $i++;
        }
        if ($autoCommit) {
            $this->objQuery->commit();
        }
        return $i;
    }

    /**
     * マスターデータを追加する.
     *
     * 引数 $masterData の値でマスターデータを更新する.
     * $masterData は key => value 形式の配列である必要がある.
     *
     * @param string $name マスターデータ名
     * @param string $key キー名
     * @param string $comment コメント
     * @param bool $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスターデータの更新数
     */
    function insertMasterData($name, $key, $value, $comment, $autoCommit = true) {

        $columns = $this->getDefaultColumnName();

        $this->objQuery = new SC_Query_Ex();
        if ($autoCommit) {
            $this->objQuery->begin();
        }

        // 指定のデータを追加
        $sqlVal[$columns[0]] = $key;
        $sqlVal[$columns[1]] = $value;
        $sqlVal[$columns[2]] = $this->objQuery->max($columns[2], $name) + 1;
        $sqlVal[$columns[3]] = $comment;
        $this->objQuery->insert($name, $sqlVal);

        if ($autoCommit) {
            $this->objQuery->commit();
        }
        return 1;
    }

    /**
     * マスターデータを削除する.
     *
     * 引数 $name のマスターデータを削除し,
     * キャッシュも削除する.
     *
     * @param string $name マスターデータ名
     * @param bool $autoCommit トランザクションを自動的に commit する場合 true
     * @return integer マスターデータの削除数
     */
    function deleteMasterData($name, $autoCommit = true) {
        $this->objQuery = new SC_Query_Ex();
        if ($autoCommit) {
            $this->objQuery->begin();
        }

        // DB の内容とキャッシュをクリア
        $result = $this->objQuery->delete($name);
        $this->clearCache($name);

        if ($autoCommit) {
            $this->objQuery->commit();
        }
        return $result;
    }

    /**
     * マスターデータのキャッシュを消去する.
     *
     * @param string $name マスターデータ名
     * @return bool 消去した場合 true
     */
    function clearCache($name) {
        $masterDataFile = MASTER_DATA_REALDIR . $name . ".php";
        if (is_file($masterDataFile)) {
            unlink($masterDataFile);
        }
        $masterDataFile = MASTER_DATA_REALDIR . $name . ".serial";
        if (is_file($masterDataFile)) {
            unlink($masterDataFile);
        }
    }

    /**
     * マスターデータのキャッシュを生成する.
     *
     * 引数 $name のマスターデータキャッシュを生成する.
     * 既存のキャッシュが存在する場合は上書きする.
     *
     * 引数 $isDefine が true の場合は, 定数を生成する.
     * 定数コメントを生成する場合は, $commentColumn を指定する.
     *
     * @param string $name マスターデータ名
     * @param array $masterData マスターデータ
     * @param bool $isDefine 定数を生成する場合 true
     * @param array $commentColumn [0] => キー, [1] => コメント文字列,
                                   [2] => 表示順 を表すカラム名を格納した配列
     * @return bool キャッシュの生成に成功した場合 true
     */
    function createCache($name, $columns = array(), $isDefine = false,
                         $commentColumn = array()) {

        // マスターデータを取得
        $masterData = $this->getDbMasterData($name, $columns);

        // マスターデータを文字列にする
        // 定数を生成する場合
        if ($isDefine) {
            $path = MASTER_DATA_REALDIR . $name . '.php';

            $data = "<?php\n";
            // 定数コメントを生成する場合
            if (!empty($commentColumn)) {
                $data .= $this->getMasterDataAsDefine($masterData,
                                 $this->getDbMasterData($name, $commentColumn));
            } else {
                $data .= $this->getMasterDataAsDefine($masterData);
            }
            $data .=  "?>\n";

        // 配列を生成する場合
        } else {
            $path = MASTER_DATA_REALDIR . $name . '.serial';
            $data = serialize($masterData);
        }

        // ファイルを書き出しモードで開く
        $handle = fopen($path, 'w');
        if (!$handle) {
            return false;
        }
        // ファイルの内容を書き出す.
        if (fwrite($handle, $data) === false) {
            return false;
        }
        return true;
    }

    function array2colstr($columns) {
	$length = count($columns);
	$s = "";

	for ($i = 0; $i < $length; ++$i) {
	    $s .= $columns[$i];
	    if ($i < ($length - 1)) {
		$s .= ", ";
	    }
	}

	return $s;
    }

    function getDbMasterDataNatural($name, $columns = array()) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $columns = $this->getDefaultColumnName($columns);
	$collist = $this->array2colstr($columns);
        $data = $objQuery->select($collist, $name);

        foreach ($data as $e) {
	    for ($i = 1; $i < count($columns); ++$i) {
		$result[$e[$columns[0]]][$columns[$i]] = $e[$columns[$i]];
	    }
        }
	return $result;
    }

    /**
     * DBからマスターデータを取得する.
     *
     * キャッシュの有無に関係なく, DBからマスターデータを検索し, 取得する.
     *
     * 返り値は, key => value 形式の配列である.
     *
     * @param string $name マスターデータ名
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @return array マスターデータ
     */
    function getDbMasterData($name, $columns = array()) {
        $columns = $this->getDefaultColumnName($columns);
        $this->objQuery = new SC_Query_Ex();

        if (isset($columns[2]) && strlen($columns[2]) >= 1) {
            $this->objQuery->setOrder($columns[2]);
        }

        $results = $this->objQuery->select
	    ($columns[0] . ", " . $columns[1], $name);

        // 結果を key => value 形式に格納
        $masterData = array();
        foreach ($results as $result) {
            $masterData[$result[$columns[0]]] = $result[$columns[1]];
        }

        return $masterData;
    }

    // }}}
    // {{{ private functions

    /**
     * デフォルトのカラム名の配列を返す.
     *
     * 引数 $columns が空の場合, デフォルトのカラム名の配列を返す.
     * 空でない場合は, 引数の値をそのまま返す.
     *
     * @param array $columns [0] => キー, [1] => 表示文字列, [2] => 表示順
     *                        を表すカラム名を格納した配列
     * @return array カラム名を格納した配列
     */
    function getDefaultColumnName($columns = array()) {

        if (!empty($columns)) {
            return $columns;
        }

	return $this->columns;
    }

    /**
     * マスターデータの配列を定数定義の文字列として出力する.
     *
     * @access private
     * @param array $masterData マスターデータの配列
     * @param array $comments コメントの配列
     * @return string 定数定義の文字列
     */
    function getMasterDataAsDefine($masterData, $comments = array()) {
        $data = "";
        foreach ($masterData as $key => $val) {
            if (!empty($comments[$key])) {
                $data .= "/** " . $comments[$key] . " */\n";
            }
            $data .= "define('" . $key . "', " . $val . ");\n";
        }
        return $data;
    }

    /**
     * マスタテーブル情報を配列で取得.
     * キー:id、値:nameで取得
     * @access private
     * @param  $table マスタテーブル名
     *
     * @return array $arrMtbTable キー:id 値:name の配列
     */
    function getMtbTable($table) {
        $objQuery = new SC_Query_Ex();

        // 初期値定義
        $arrData = array();
        $arrMtbTable = array();

        $sql = <<<EOS
SELECT * FROM $table 
EOS;
        $arrData = $objQuery->getAll($sql);

        foreach ($arrData as $key => $arrVal) {
            // キーをID,値をnameで格納
            $arrMtbTable[$arrVal["id"]] = $arrVal["name"];
        }
        return $arrMtbTable;
    }
}
?>
