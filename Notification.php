<?php

	(function($argv){

		$methods = array(
			"blog" => array(
				"name"			=> 	"Heroes of the storm Blogs",
				"url" 			=>	"https://heroesofthestorm.com/en-us/blog/infinite/?page=1",
				"defaultSave"	=> 	"defaultBlogTitle.txt",
				"subreddit"		=>	"heroesofthestorm",
				"analyse"		=>	function($data){
					require "phpQuery\phpQuery\phpQuery.php";
					phpQuery::newDocument($data);
					$title = pq(".news-list__item__title:eq(0)")->text();
					$title = trim($title);
					$url = trim(pq(".news-list__item__link:eq(0)")->attr("href"));
					$url = (substr($url, 0, 4) === "http") ? $url : "https://heroesofthestorm.com" . $url;
					// $url = "https://heroesofthestorm.com" . trim(pq(".news-list__item__link:eq(0)")->attr("href"));
					return array($title, $url);
				}

			),
			"newhero" => array(
				"name"			=> 	"Hero",
				"url" 			=>	"https://heroesofthestorm.com/en-us/heroes/#/",
				"defaultSave"	=> 	"defaultHeroTitle.txt",
				"subreddit"		=>	"heroesofthestorm",
				"analyse"		=>	function($data){
					require "phpQuery\phpQuery\phpQuery.php";
					$html = phpQuery::newDocument($data);
					$js = $html["script"]->eq(17)->text();

					var_dump(pq);
					die();
					// $hero = explode("//<![CDATA[", $hero)[1];
					// $hero = explode("window.heroes = ", $hero)[1];
					// $hero = explode("//]]>", $hero)[0];
					// $hero = explode(";", $hero)[0];
					// $hero = json_decode($hero, true);
					// $title = $hero[0]["name"] . "  - Heroes of the Storm"; 
					$url = "https://us.battle.net/heroes/en/heroes/" . $hero[0]["slug"] . "/";
					return array($title, $url);
				}

			),
			"video" => array(
				"name"			=> "Hereos of the storm video",
				"url"			=> "https://content.googleapis.com/youtube/v3/playlistItems?playlistId=UUpVdq9gLew6E76BmfB2GJ0w&maxResults=1&part=snippet%2CcontentDetails&key=AIzaSyB3DGsubXgMH-lMB0SRk7k4V809GAScUjo",
				"defaultSave" 	=> "defaultVideoTitle.txt",
				"subreddit"		=>	"heroesofthestorm",
				"analyse" 		=> function($data){
					$data = json_decode($data, true);
					$title = $data["items"]["0"]["snippet"]["title"];
					$url =  "https://www.youtube.com/watch?v=" .  $data["items"]["0"]["snippet"]["resourceId"]["videoId"];
					return array($title, $url);
				},
			),
			"owvideo" => array(
				"name"			=> "Overwatch Video",
				"url"			=> "https://content.googleapis.com/youtube/v3/playlistItems?playlistId=UUlOf1XXinvZsy4wKPAkro2A&maxResults=1&part=snippet%2CcontentDetails&key=AIzaSyCuExp0xyhKIHl54qacVD1BTUBxJYJ3lA0",
				"defaultSave" 	=> "defaultOWVideoTitle.txt",
				"subreddit"		=>	"overwatch",
				"analyse" 		=> function($data){
					$data = json_decode($data, true);
					$title = $data["items"]["0"]["snippet"]["title"];
					$url =  "https://www.youtube.com/watch?v=" .  $data["items"]["0"]["snippet"]["resourceId"]["videoId"];
					return array($title, $url);
				},
			),
			"owpatch" => array(
				"name"			=> "Overwatch Patch",
				"url"			=> "https://us.forums.blizzard.com/en/overwatch/c/announcements/l/latest.json",
				"defaultSave" 	=> "defaultOWPatchTitle.txt",
				"subreddit"		=>	"overwatch",
				"analyse" 		=> function($data){
					$data = json_decode($data, true);
					$title = $data["topic_list"]["topics"][0]["title"];
					$url =  "https://us.forums.blizzard.com/en/overwatch/t/" . $data["topic_list"]["topics"][0]["slug"] . "/" . $data["topic_list"]["topics"][0]["id"];
					return array($title, $url);
				},
			),
			"TEMPLATE" => array( //Index keywork for entry commandline argument
				"name"			=> "template", //The name for the instance
				"url"			=> "https://www.jamiephan.net", //Data source URL
				"defaultSave" 	=> "defaultTemplateTitle.txt", //The file to save the title. Most of the time is default{Name}Title.txt
				"subreddit"		=> "jamiephan", //The subreddit name for submission
				"analyse"		=> function($data){ //function to analyse the website. $data is the HTML of the URL above

					// Some analyse code here
					require "phpQuery\phpQuery\phpQuery.php"; //You can also require external file lib
					phpQuery::newDocument($data);
					$title = trim(pq("title")->text());
					$url = trim(pq("a[href^=http]:eq(0)")->attr("href"));

					return array($title, $url); //Return Array with first Title string and second URL of the actual content to post on reddit

				}
			)

		);


		$method = $argv[1] ?? "blog";

		cli_set_process_title("Detecting New " . $methods[$method]["name"]);

		if (!isset($methods[$method])) {
			echo "==========================" . "\n\r";
			echo "[" . time() . "] " . "ERROR: INVALID METHOD." . "\n\r";
			die();
		}

		if(!is_writable(__DIR__) || !is_readable(__DIR__)) {
				echo "==========================" . "\n\r";
				echo "[" . time() . "] " . "ERROR: FAILED TO READ OR WRITE TO CURRENT DIRECTORY, CHECK YOUR WRITE PERMISSION AT " . __DIR . "." .  "\n\r";
				die();
		}

		if (!file_exists($methods[$method]["defaultSave"])) {
			file_put_contents($methods[$method]["defaultSave"], "");
		}

		if(!is_readable($methods[$method]["defaultSave"]) || !is_writable($methods[$method]["defaultSave"])) {
				echo "==========================" . "\n\r";
				echo "[" . time() . "] " . "ERROR: FAILED TO READ " . $methods[$method]["defaultSave"] . "." .  "\n\r";
				die();
		}

		// $data = @file_get_contents($methods[$method]["url"]);
	
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $methods[$method]["url"]); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $data = curl_exec($ch);
        // echo $data;

		if($data === false) {
			echo "==========================" . "\n\r";
			echo "[" . time() . "] " . "ERROR: NETWORK ERROR: " .  curl_error($ch) . "\n\r";
			die();
		}
        curl_close($ch);
		

		$oldTitle = file_get_contents($methods[$method]["defaultSave"]);

		$analyse = $methods[$method]["analyse"]($data);

		$title = $analyse[0];
		$url = $analyse[1];

		if ($title == $oldTitle) {

			echo "==========================" . "\n\r";
			echo "[" . time() . "] " . $title . "\n\r";
			echo $url . "\n\r";

		} else {

			file_put_contents($methods[$method]["defaultSave"], $title);

			echo "==========================" . "\n\r";
			echo "NEW " . $methods[$method]["name"] . " DETECTED: " . $title . "\n\r";
			echo "Opened Reddit submission page with filled title and url. With the new " . $methods[$method]["name"] . ".". "\n\r";


			shell_exec("start $url");

			$title = urlencode($title);
			$url = urlencode($url);
			shell_exec("start https://www.reddit.com/r/" . $methods[$method]["subreddit"] . "/submit?title=$title^&url=$url");
			shell_exec("voice.exe -f \"New ". $methods[$method]["name"] ." Detected!\"");
			shell_exec("nircmd.exe trayballoon \"New " . $methods[$method]["name"] . " detected\" \"$title\" \"imageres.dll,-81\" 10");

		}

	})($argv);
?>