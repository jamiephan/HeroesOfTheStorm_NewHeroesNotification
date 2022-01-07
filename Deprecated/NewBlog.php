<?php 






$data = file_get_contents("http://us.battle.net/heroes/en/blog/");

	require "phpQuery\phpQuery\phpQuery.php";

	$OldTitle = file_get_contents("defaultBlogTitle.txt");


	phpQuery::newDocument($data);

	$title = pq(".news-list:eq(1)")->find(".news-list__item__title:eq(0)")->text();

	$title = trim($title);

	$url = "https://us.battle.net" . trim(pq(".news-list:eq(1)")->find(".news-list__item__title:eq(0)")->find("a")->attr("href")
);

	if ($title == $OldTitle) {
		echo "=============" . "\n\r";
		echo "[" . time() . "] " . $title;
		echo "\n\r";
		echo $url . "\n\r";
	} else {

		file_put_contents('defaultBlogTitle.txt', $title);
		echo "=============" . "\n\r";
		echo "!!!NEW BLOG DETECTED!!!" . "\n\r";
		echo "OPENED REDDIT SUBMITTION PAGE AND BLOG URL" . "\n\r";
		shell_exec("nircmd.exe trayballoon \"New Blog Detected\" \"$title\" \"imageres.dll,-81\" 10");
		shell_exec("start $url");
		$title = urlencode($title);
		$url = urlencode($url);
		$cmd = "start https://www.reddit.com/r/heroesofthestorm/submit?title=$title^&url=$url";
		shell_exec($cmd);

	}



 ?>