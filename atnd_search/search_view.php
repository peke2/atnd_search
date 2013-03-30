<html>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>ATND BETAイベント検索</title>
<body>

<center>
<form action="./search_result.php">
	<table>
		<tr>
		<th>検索文字列</th><td><input type="text" name="keywords"></td>
		</tr>
		<tr>
		<th>検索年月(YYYYMM)</th><td><input type="text" name="ym" value=<?php echo '"'.date("Ym").'"'?>></td>
		</tr>
	</table>
	<input type="submit" value="検索"><br>
	<input type="hidden" name="start" value="0">
</form>
</center>

</body>

</html>
