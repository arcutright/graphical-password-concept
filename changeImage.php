<?php

	$chunkSize = 100; // images per chunk (ideally at least gridX*gridY)
	$chunksDir = 'chunks';

	if(isset($_POST['login'])){
		$raw = json_decode($_POST['login']);
		// verify post is well-formed
		if((array)$raw === $raw && sizeof($raw) == 2){
			$username = $raw[0];
			$mouseEntropy = $raw[1];
			// lookup starting image in database
			$imageChunkDirectory = ''; // obviously, replace me
			$initialImage = 'chunks/chunk1/aVh7NEH.jpg';
			// $initialImage = database_fetch($username, 'image');
			// if unavailable (user not in database)
			/* obviously, this code is for testing ONLY. 
			   if user is not in database, return empty
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
			$currentImageURL = $raw[1];
			$chosenGridPosition = $raw[2];
			// verify current image
			if(imageExists($currentImageURL)){
				$chunk = dirname($currentImageURL);
				// calculate next image, add to chain (+encrypt?)
				$dataChainOut = '?';
				$nextImageURL = '?';
			}
			else
				header("HTTP/1.0 500 Internal Server Error");
			// respond with [chain, next image url]
			echo json_encode([$dataChainOut, $nextImageURL]);
		}
		clearstatcache();
	}

	if(isset($_POST['checkLogin'])){
		$raw = json_decode($_POST['checkLogin']);
		// verify posted data is well-formed
		if((array)$raw === $raw && sizeof($raw) == 2){
			$username = $raw[0];
			$dataChain = $raw[1];
			// verify username/datachain
			// if(check_database($username, $dataChain))
			echo json_encode('login success');
			//else echo json_encode('login fail');
		}
		clearstatcache();
	}


	function urlExists($url){
		$headers = get_headers($url);
		return stripos($headers[0],"200 OK") ? true : false;
	}

	function imageExists($url){
		return is_file($url);
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