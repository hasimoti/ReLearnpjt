<?php
/*!
@file contents_db.php
@brief 
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

////////////////////////////////////
//以下、DBクラス使用例

//--------------------------------------------------------------------------------------
///	問題クラス
//--------------------------------------------------------------------------------------
class cquestion extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	すべての個数を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@return	個数
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_count($debug){
		//プレースホルダつき
		$query = <<< END_BLOCK
select
count(*)
from
questions
where
1
END_BLOCK;
		//空のデータ
		$prep_arr = array();
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		if($row = $this->fetch_assoc()){
			//取得した個数を返す
			return $row['count(*)'];
		}
		else{
			return 0;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	指定された範囲の配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$from	抽出開始行
	@param[in]	$limit	抽出数
	@return	配列（2次元配列になる）
	*/
	//--------------------------------------------------------------------------------------
	public function get_all($debug,$from,$limit){
		$arr = array();
		//プレースホルダつき
		$query = <<< END_BLOCK
select
*
from
questions
where
1
order by
question_id asc
limit :from, :limit
END_BLOCK;
		$prep_arr = array(
				':from' => (int)$from,
				':limit' => (int)$limit);
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		//順次取り出す
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		//取得した配列を返す
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	指定されたIDの配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$id		ID
	@return	配列（1次元配列になる）空の場合はfalse
	*/
	//--------------------------------------------------------------------------------------
	public function get_tgt($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//falseを返す
			return false;
		}
		//プレースホルダつき
		$query = <<< END_BLOCK
select
*
from
questions
where
question_id = :question_id
END_BLOCK;
		$prep_arr = array(
				':question_id' => (int)$id
		);
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	フルーツクラス
//--------------------------------------------------------------------------------------
class cfruits extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	すべての個数を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@return	個数
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_count($debug){
		//プレースホルダつき
		$query = <<< END_BLOCK
select
count(*)
from
fruits
where
1
END_BLOCK;
		//空のデータ
		$prep_arr = array();
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		if($row = $this->fetch_assoc()){
			//取得した個数を返す
			return $row['count(*)'];
		}
		else{
			return 0;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	すべての配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@return	配列（2次元配列になる）
	*/
	//--------------------------------------------------------------------------------------
	public function get_all($debug){
		$arr = array();
		//プレースホルダつき
		$query = <<< END_BLOCK
select
*
from
fruits
where
1
order by
fruits_id asc
END_BLOCK;
		//空のデータ
		$prep_arr = array();
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		//順次取り出す
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		//取得した配列を返す
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	指定されたIDの配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$id		ID
	@return	配列（1次元配列になる）空の場合はfalse
	*/
	//--------------------------------------------------------------------------------------
	public function get_tgt($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//falseを返す
			return false;
		}
		//プレースホルダつき
		$query = <<< END_BLOCK
select
*
from
fruits
where
fruits_id = :fruits_id
END_BLOCK;
		$prep_arr = array(
				':fruits_id' => (int)$id
		);
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
/// 講座クラス
//--------------------------------------------------------------------------------------
class ccourse extends crecord {
    //--------------------------------------------------------------------------------------
    /*!
    @brief  コンストラクタ
    */
    //--------------------------------------------------------------------------------------
    public function __construct() {
        //親クラスのコンストラクタを呼ぶ
        parent::__construct();
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief  すべての個数を得る
    @param[in]  $debug  デバッグ出力をするかどうか
    @return 個数
    */
    //--------------------------------------------------------------------------------------
    public function get_all_count($debug=false){
        //プレースホルダつき
        $query = <<< END_BLOCK
select
count(*)
from
course
where
1
END_BLOCK;
        //空のデータ
        $prep_arr = array();
        //親クラスのselect_query()メンバ関数を呼ぶ
        $this->select_query(
            $debug,         //デバッグ表示するかどうか
            $query,         //プレースホルダつきSQL
            $prep_arr       //データの配列
        );
        if($row = $this->fetch_assoc()){
            //取得した個数を返す
            return $row['count(*)'];
        }
        else{
            return 0;
        }
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief  すべての配列を得る
    @param[in]  $debug  デバッグ出力をするかどうか
    @return 配列（2次元配列になる）
    */
    //--------------------------------------------------------------------------------------
    public function get_all($debug){
        $arr = array();
        //プレースホルダつき
        $query = <<< END_BLOCK
select
*
from
course
where
1
order by
course_id asc
END_BLOCK;
        //空のデータ
        $prep_arr = array();
        //親クラスのselect_query()メンバ関数を呼ぶ
        $this->select_query(
            $debug,         //デバッグ表示するかどうか
            $query,         //プレースホルダつきSQL
            $prep_arr       //データの配列
        );
        //順次取り出す
        while($row = $this->fetch_assoc()){
            $arr[] = $row;
        }
        //取得した配列を返す
        return $arr;
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief  指定されたIDの配列を得る
    @param[in]  $debug  デバッグ出力をするかどうか
    @param[in]  $id     ID
    @return 配列（1次元配列になる）空の場合はfalse
    */
    //--------------------------------------------------------------------------------------
    public function get_tgt($debug,$id){
        if(!cutil::is_number($id)
        ||  $id < 1){
            //falseを返す
            return false;
        }
        //プレースホルダつき
        $query = <<< END_BLOCK
select
*
from
course
where
course_id = :course_id
END_BLOCK;
        $prep_arr = array(
                ':course_id' => (int)$id
        );
        //親クラスのselect_query()メンバ関数を呼ぶ
        $this->select_query(
            $debug,         //デバッグ表示するかどうか
            $query,         //プレースホルダつきSQL
            $prep_arr       //データの配列
        );
        return $this->fetch_assoc();
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief  デストラクタ
    */
    //--------------------------------------------------------------------------------------
    public function __destruct(){
        //親クラスのデストラクタを呼ぶ
        parent::__destruct();
    }
//--------------------------------------------------------------------------------------
    /*!
  検索機能
    */
    //--------------------------------------------------------------------------------------
public function search($debug, $keyword) {
    $arr = array();
    $query = <<<END_BLOCK
SELECT *
FROM course
WHERE course_name LIKE :keyword
   OR category LIKE :keyword
ORDER BY course_id DESC
END_BLOCK;

    $prep_arr = array(
        ':keyword' => '%' . $keyword . '%'
    );

    $this->select_query($debug, $query, $prep_arr);

    while($row = $this->fetch_assoc()) {
        $arr[] = $row;
    }

    return $arr;
}



}


//--------------------------------------------------------------------------------------
/// テストクラス
//--------------------------------------------------------------------------------------
class ctest extends crecord {
    //--------------------------------------------------------------------------------------
    /*!
    @brief  コンストラクタ
    */
    //--------------------------------------------------------------------------------------
    public function __construct() {
        //親クラスのコンストラクタを呼ぶ
        parent::__construct();
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief  すべての個数を得る
    @param[in]  $debug  デバッグ出力をするかどうか
    @return 個数
    */
    //--------------------------------------------------------------------------------------
    public function get_all_count($debug=false){
        //プレースホルダつき
        $query = <<< END_BLOCK
select
count(*)
from
tests
where
1
END_BLOCK;
        //空のデータ
        $prep_arr = array();
        //親クラスのselect_query()メンバ関数を呼ぶ
        $this->select_query(
            $debug,         //デバッグ表示するかどうか
            $query,         //プレースホルダつきSQL
            $prep_arr       //データの配列
        );
        if($row = $this->fetch_assoc()){
            //取得した個数を返す
            return $row['count(*)'];
        }
        else{
            return 0;
        }
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief  すべての配列を得る
    @param[in]  $debug  デバッグ出力をするかどうか
    @return 配列（2次元配列になる）
    */
    //--------------------------------------------------------------------------------------
    public function get_all($debug){
        $arr = array();
        //プレースホルダつき
        $query = <<< END_BLOCK
select
*
from
tests
where
1
order by
test_id asc
END_BLOCK;
        //空のデータ
        $prep_arr = array();
        //親クラスのselect_query()メンバ関数を呼ぶ
        $this->select_query(
            $debug,         //デバッグ表示するかどうか
            $query,         //プレースホルダつきSQL
            $prep_arr       //データの配列
        );
        //順次取り出す
        while($row = $this->fetch_assoc()){
            $arr[] = $row;
        }
        //取得した配列を返す
        return $arr;
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief  指定されたIDの配列を得る
    @param[in]  $debug  デバッグ出力をするかどうか
    @param[in]  $id     ID
    @return 配列（1次元配列になる）空の場合はfalse
    */
    //--------------------------------------------------------------------------------------
    public function get_tgt($debug,$id){
        if(!cutil::is_number($id)
        ||  $id < 1){
            //falseを返す
            return false;
        }
        //プレースホルダつき
        $query = <<< END_BLOCK
select
*
from
tests
where
test_id = :test_id
END_BLOCK;
        $prep_arr = array(
                ':test_id' => (int)$id
        );
        //親クラスのselect_query()メンバ関数を呼ぶ
        $this->select_query(
            $debug,         //デバッグ表示するかどうか
            $query,         //プレースホルダつきSQL
            $prep_arr       //データの配列
        );
        return $this->fetch_assoc();
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief  デストラクタ
    */
    //--------------------------------------------------------------------------------------
    public function __destruct(){
        //親クラスのデストラクタを呼ぶ
        parent::__destruct();
    }



}


//--------------------------------------------------------------------------------------
///	メンバークラス
//--------------------------------------------------------------------------------------
class cmember extends crecord {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	すべての個数を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@return	個数
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_count($debug){
		//プレースホルダつき
		$query = <<< END_BLOCK
select
count(*)
from
member,question
where
member.member_question_id = question.question_id
END_BLOCK;
		//空のデータ
		$prep_arr = array();
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		if($row = $this->fetch_assoc()){
			//取得した個数を返す
			return $row['count(*)'];
		}
		else{
			return 0;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	指定された範囲の配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$from	抽出開始行
	@param[in]	$limit	抽出数
	@return	配列（2次元配列になる）
	*/
	//--------------------------------------------------------------------------------------
	public function get_all($debug,$from,$limit){
		$arr = array();
		//プレースホルダつき
		$query = <<< END_BLOCK
select
member.*,question.*
from
member,question
where
member.member_question_id = question.question_id
order by
member.member_id asc
limit :from, :limit
END_BLOCK;
		$prep_arr = array(
				':from' => (int)$from,
				':limit' => (int)$limit);
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		//順次取り出す
		while($row = $this->fetch_assoc()){
			$arr[] = $row;
		}
		//取得した配列を返す
		return $arr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	指定されたIDの配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$id		ID
	@return	配列（1次元配列になる）空の場合はfalse
	*/
	//--------------------------------------------------------------------------------------
	public function get_tgt($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//falseを返す
			return false;
		}
		//プレースホルダつき
		$query = <<< END_BLOCK
select
member.*,question.*
from
member,question
where
member.member_question_id = question.question_id
and
member.member_id = :member_id
END_BLOCK;
		$prep_arr = array(
				':member_id' => (int)$id
		);
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		return $this->fetch_assoc();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	フルーツとのマッチする配列を得る
	@param[in]	$debug	デバッグ出力をするかどうか
	@param[in]	$id		ID
	@return	配列（2次元配列になる）
	*/
	//--------------------------------------------------------------------------------------
	public function get_all_fruits_match($debug,$id){
		if(!cutil::is_number($id)
		||  $id < 1){
			//falseを返す
			return false;
		}
		//プレースホルダつき
		$query = <<< END_BLOCK
select
*
from
fruits_match
where
member_id = :member_id
order by
fruits_id asc
END_BLOCK;
		$prep_arr = array(
				':member_id' => (int)$id
		);
		//親クラスのselect_query()メンバ関数を呼ぶ
		$this->select_query(
			$debug,			//デバッグ表示するかどうか
			$query,			//プレースホルダつきSQL
			$prep_arr		//データの配列
		);
		//順次取り出す
		while($row = $this->fetch_assoc()){
			$arr[] = $row['fruits_id'];
		}
		//取得した配列を返す
		return $arr;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}


