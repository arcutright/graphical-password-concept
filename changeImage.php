<?php
	// show errors
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	$chunksDir = 'chunks';

	if(isset($_POST['login'])){
		$raw = json_decode($_POST['login']);
		// verify post is well-formed
		if((array)$raw === $raw && sizeof($raw) == 2){
			$username = $raw[0];
			$mouseEntropy = $raw[1];
			// lookup starting image in database
			$imageChunkDirectory = ''; // obviously, replace me
			$initialImage = 'chunks/chunk1/wallpaper5.jpg';
			// $initialImage = database_fetch($username, 'image');
			// if unavailable (user not in database)
			/* obviously, this code is for testing ONLY. 
			   if user is not in database, return empty
			   (use some other POST for creating accounts)
			// select chunk
			$dir = $_SERVER['DOCUMENT_ROOT'].'/'.$chunksDir;
			$index = $mouseEntropy % numDirs($dir);
			$imageChunkDirectory = glob($dir.'/*.*')[$index];
			// select image
			$initialImage = rand_array(glob($imageChunkDirectory));
			database_store($username, 'image', $image);
			*/
			echo json_encode($initialImage);
		}
		clearstatcache();
	}

	if(isset($_POST['changeImage'])){
		$raw = json_decode($_POST['changeImage']);
		// verify posted data is well-formed
		if((array)$raw === $raw && sizeof($raw) == 3){
			$dataChainIn = $raw[0];
			$currentImageURL = urldecode($raw[1]);
			$chosenGridPosition = $raw[2];
			// get substring after the http://.../
			$index = strpos($currentImageURL, '//');
			$index = strpos($currentImageURL, '/', $index+2);
			$currentImageURL = substr($currentImageURL, $index+1);
			// initialize variables
			$dataChainOut = '?';
			$nextImageURL = '?';
			// verify current image
			if(imageExists($currentImageURL)){
				$chunk = dirname($currentImageURL);
				// calculate next image, add to chain (+encrypt)
				$dataChainOut  = hash('sha256', $dataChainIn.$currentImageURL.implode('',$chosenGridPosition));
				global $chunkSize;
				if($nextImageURL = glob($chunk.'/*.*')){
					$index = abs(hexdec(substr($dataChainOut, 0, 16)) % sizeof($nextImageURL));
					$nextImageURL = $nextImageURL[$index];
				}
				else
					header("HTTP/1.0 500 Internal Server Error");
			}
			else
				header("HTTP/1.0 500 Internal Server Error");
			// respond with [chain, next image url]
			echo json_encode([$dataChainOut, $nextImageURL]);
		}
		clearstatcache();
	}

	// obviously, modified for demo purposes
	if(isset($_POST['checkLogin'])){
		$raw = json_decode($_POST['checkLogin']);
		// verify posted data is well-formed
		if((array)$raw === $raw && sizeof($raw) == 4){
			$dataChainIn = $raw[0];
			$currentImageURL = urldecode($raw[1]);
			$chosenGridPosition = $raw[2];
			$username = $raw[3];
			// get substring after the http://.../
			$index = strpos($currentImageURL, '//');
			$index = strpos($currentImageURL, '/', $index+2);
			$currentImageURL = substr($currentImageURL, $index+1);
			// initialize variables
			$dataChainOut = '?';
			// verify current image
			if(imageExists($currentImageURL)){
				$chunk = dirname($currentImageURL);
				// calculate next image, add to chain (+encrypt)
				$dataChainOut = hash('sha256', $dataChainIn.$currentImageURL.implode('',$chosenGridPosition));
				// this is where we would look up username,password in database
				// for demo though, it will just echo out the final username,password
			}
			else
				header("HTTP/1.0 500 Internal Server Error");
			// this is for demo purposes only (otherwise it would authenticate the user)
			echo json_encode($dataChainOut);
		}
		clearstatcache();
	}

	function urlExists($url){
		$headers = get_headers($url);
		return stripos($headers[0],"200 OK") ? true : false;
	}

	function imageExists($url){
		return file_exists($url);
	}

	function numFiles($dir){
		if(is_dir($dir) && $handle = opendir($dir)){
			$numfiles = 0;
		    while ($entry = readdir($handle)){
		        if ($entry != "." && $entry != ".."){
		            if (!is_dir($entry))
		                $numfiles++;
		        }
		    }
		    closedir($handle);
		    return $numfiles;
		}
		return 0;
	}

	function numDirs($dir){
		if(is_dir($dir) && $handle = opendir($dir)){
			$numdirs = 0;
		    while ($entry = readdir($handle)){
		        if ($entry != "." && $entry != ".."){
		            if (is_dir($entry))
		                $numdirs++;
		        }
		    }
		    closedir($handle);
		    return $numdirs;
		}
		return 0;
	}
?>