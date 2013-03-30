<html>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">

<body>
<?php
	/*
		クエリーの文字列からクエリーの連想配列を取得
	*/
	function	getQueries($query)
	{
		//	各パラメータごとに分割
		$params = explode('&', $query);
		$queries = array();
		//	キーと数値で分割
		foreach($params as $param)
		{
			$values = explode('=', $param);
			$queries[$values[0]] = $values[1];
		}
		return	$queries;
	}

	/*
		ページのリンクをセット
	*/
	function	setPageLink($max_count, $start, $count_per_page, $currente_url)
	{
		//	最大ページ
		$max_page = floor($max_count / $count_per_page);

		//	現在のページ
		$current_page = floor($start / $count_per_page);

		for($i=0; $i<$max_page; $i++)
		{
			if( $i == $current_page )
			{
				//	現在のページにはリンクを張らない
				print("$i");
			}
			else
			{
				//	現在のURLのクエリ部分を流用
				$params = parse_url($currente_url);
			//	$queries = explode('&', $params['query']);
				$queries = getQueries( $params['query'] );
				if( isset($queries['start']) )
				{
					//	リンク先のページの先頭を示すカウントに置き換える
					$queries['start'] = $i * $count_per_page;
				}
				if( isset($queries['keywords']) )
				{
					$queries['keywords'] = urldecode($queries['keywords']);
				}
				//var_dump($queries);
				$query = http_build_query($queries);
				$url = 'http://'. $params['host'] . $params['path']. '?'. $query;
				printf("<a href='%s'>%d</a>", $url, $i);
			}
			print("  ");
		}
	}


	function	setInfos($events)
	{
		echo	"<table border='2'>";
		echo	"<tr>";
		echo	"<th>タイトル</th>";
		echo	"<th>キャッチ</th>";
		echo	"<th>開始日時</th>";
		echo	"<th>イベントURL</th>";
	//	echo	"<th>概要</th>";
		echo	"</tr>";
		echo	"\n";

		$week = array('日','月','火','水','木','金','土');

		foreach($events as $event)
		{
			$time = strtotime($event['started_at']);
			$start_date = date("Y-m-d", $time). "(". $week[intval(date("w", $time))] . ") ".date("H:i", $time);
			echo	"<tr>";
			echo	"<td>".$event['title']."</td>";
			echo	"<td>".$event['catch']."</td>";
			echo	"<td>".$start_date."</td>";
			echo	"<td><a target='_blank' href='".$event['event_url']."'>URL</a></td>";
	//		echo	"<td>".$event['description']."</td>";
			echo	"</tr>";
			echo	"\n";
		}
		echo	"</table>\n";
	}

	printf("イベント数 : %d<br>", $max_count);

	echo	"<br>";
	//	リンク先のページを表示
	setPageLink($max_count, $start, $count_per_page, $currente_url);
	echo	"<br>";

	setInfos($events);


?>
</body>

</html>
