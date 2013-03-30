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

		function	search($keywords, $ym, $start)
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
			$query = $this->getQuery($keywords, $ym, $start);
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


		function	getQuery($keywords, $ym, $start)
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

	$search_result = $atnd->search($keywords, $ym, $start);

	$entries = json_decode($search_result, TRUE);

	var_dump($entries);


	echo '<pre>';

	var_dump( $atnd->getKeywordQuery($keywords) );

	var_dump($_GET);
	$query = $atnd->getQuery($keywords, $ym, $start);
	echo $query."\r";
	var_dump($search_result);
	echo '</pre>';

?>