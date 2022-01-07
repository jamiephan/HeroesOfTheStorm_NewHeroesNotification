<?php 




// $data = file_get_contents("http://us.battle.net/heroes/en/blog/");


	$OldTitle = file_get_contents("defaultVidTitle.txt");

	$data = file_get_contents("https://content.googleapis.com/youtube/v3/playlistItems?playlistId=UUpVdq9gLew6E76BmfB2GJ0w&maxResults=25&part=snippet%2CcontentDetails&key=AIzaSyCuExp0xyhKIHl54qacVD1BTUBxJYJ3lA0");
	$data = json_decode($data, true);

	$title = $data["items"]["0"]["snippet"]["title"];

	$url =  "https://www.youtube.com/watch?v=" .  $data["items"]["0"]["snippet"]["resourceId"]["videoId"];


	if ($title == $OldTitle) {
		echo "=============" . "\n\r";
		echo "[" . time() . "] " . $title;
		echo "\n\r";
		echo $url . "\n\r";
	} else {

		file_put_contents('defaultVidTitle.txt', $title);
		echo "=============" . "\n\r";
		echo "!!!NEW VIDEO DETECTED!!!" . "\n\r";
		echo "OPENED REDDIT SUBMITTION PAGE AND YOUTUBE URL" . "\n\r";

		shell_exec("nircmd.exe trayballoon \"New Video Detected\" \"$title\" \"imageres.dll,-81\" 10");
		shell_exec("start $url");
		$title = urlencode($title);
		$url = urlencode($url);
		$cmd = "start https://www.reddit.com/r/heroesofthestorm/submit?title=$title^&url=$url";
		shell_exec($cmd);

	}



 ?>