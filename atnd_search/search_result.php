<?php

	//	検索結果

	define("ATND_FORMAT_JSON", "json");
	class	Atnd
	{
		private	$format;

		function	__construct()
		{
			$this->setFormat(ATND_FORMAT_JSON);
		}

		function	setFormat($format)
		{
			$this->format = $format;
		}

		function	search($keywords, $ym, $start, $count_per_page)
		{
			$ch = curl_init();
			$base_url = 'http://api.atnd.org/events/';
			$url = $base_url;

			//	結果を表示せずに受け取る
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		/*
			$queries = array();

			if( !empty($keywords) )
			{
				$queries[] = array('keyword_or' => $keywords);
			}

			if( !empty($ym) )
			{
				$queries[] = array('ym' => $ym);
			}

			if( !empty($queries) )
			{
				$query = http_build_query($queries);
				$url = $url . '?' . $query;
			}
		*/
			$query = $this->getQuery($keywords, $ym, $start, $count_per_page);
			if( !empty($query) )
			{
				$url = $url . '?' . $query;
			}
			curl_setopt($ch, CURLOPT_URL, $url);

		//	var_dump($url);

			$result = curl_exec($ch);

			curl_close($ch);

			return	$result;
		}


		/*
			キーワードの区切りの「,」をURLエンコードに含めないために処理を分ける
		*/
		function	getKeywordQuery($keywords)
		{
			if( !empty($keywords) )
			{
				$words = explode(',', $keywords);
				$param = '';
				foreach($words as $index=>$word)
				{
					if( $index > 0 )	$param .= ',';
					$param .= urlencode($word);
				}
				if( !empty($param) )
				{
					return	'keyword_or='. $param;
				}

				return	null;
			}
			
		}


		/*
			パラメータからクエリーを取得する
		*/
		function	getQuery($keywords, $ym, $start, $count_per_page)
		{
			$queries = array();
			$query = null;

			/*
			if( !empty($keywords) )
			{
				$queries['keyword_or'] = $keywords;
			}
			*/

			if( !empty($ym) )
			{
				$queries['ym'] = $ym;
			}

			if( !empty($start) )
			{
				$queries['start'] = $start;
			}

			if( !empty($count_per_page) )
			{
				$queries['count'] = $count_per_page;
			}

			$queries['format'] = $this->format;

			$query = http_build_query($queries);

			//	検索文字列の区切り「,」はURLエンコードされないようにしておく
			$keywords_param = $this->getKeywordQuery($keywords);
			if( !empty($query) && !empty($keywords_param) )
			{
				$query .= '&' . $keywords_param;
			}

			return	$query;
		}


	}

	$atnd = new Atnd();

	$keywords = null;
	$ym = null;
	$page = null;

	if( isset($_GET['keywords']) )	$keywords = $_GET['keywords'];
	if( isset($_GET['ym']) )		$ym       = $_GET['ym'];
	if( isset($_GET['start']) )		$start    = $_GET['start'];

	//	ページ当たりのイベント数
	$count_per_page = 20;

	$search_result = $atnd->search($keywords, $ym, $start, $count_per_page);

	$event_info = json_decode($search_result, TRUE);

	//var_dump($event_info);

/*
	echo '<pre>';
	var_dump( $atnd->getKeywordQuery($keywords) );
	var_dump($_GET);
	$query = $atnd->getQuery($keywords, $ym, $start);
	echo $query."\r";
	var_dump($search_result);
	echo '</pre>';
*/

	$events = $event_info['events'];
	$max_count = $event_info['results_available'];
	$currente_url = 'http://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
	//var_dump($_SERVER['HTTP_HOST']);
	//var_dump($currente_url);

	require_once('search_result_view.php');

?>