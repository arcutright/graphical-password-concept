<?php
	// user is requesting initial image
	if(isset($_POST['login'])){
		$raw = json_decode($_POST['login']);
		// verify post is well-formed
		if((array)$raw === $raw && sizeof($raw) == 1){
			$username = $raw[0];
			// use username to determine first image
			$imgArray = glob('chunks/chunk1/*.*');
			$index = abs(hexdec(substr(hash('crc32', $username), 0, 16)) % sizeof($imgArray));
			$initialImage = $imgArray[$index];
			// echo out image url
			echo json_encode($initialImage);
		}
		clearstatcache();
	}

	// user is requesting the next image
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
			$chunk = dirname($currentImageURL);
			$imgArray = [];
			// verify current image
			if(imageExists($currentImageURL) && $imgArray = glob($chunk.'/*.*')){
				// add to chain (+encrypt)
				$dataChainOut  = hash('sha256', $dataChainIn.$currentImageURL.implode('',$chosenGridPosition));
				// calculate next image
				$index = abs(hexdec(substr($dataChainOut, 5, 16)) % sizeof($imgArray));
				$nextImageURL = $imgArray[$index];
				// prevent immediate duplicates (avoid potential confusion)
				$deterministicAdjustment = sizeof($imgArray)/4;
				while($nextImageURL == $currentImageURL){
					// adjust by a fixed amount so that passwords are generated the same
					// way every time (otherwise, the password can't authenticate)
					$index = abs(hexdec(substr($dataChainOut, 5, 16) + $deterministicAdjustment) 
						% sizeof($imgArray));
					$nextImageURL = $imgArray[$index];
					$deterministicAdjustment += 1;
				}
			}
			else
				header("HTTP/1.0 500 Internal Server Error");
			// respond with [chain, next image url]
			echo json_encode([$dataChainOut, $nextImageURL]);
		}
		clearstatcache();
	}

	// user is requesting final authentication (sending final click point)
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
				// for demo though, it will just echo out the final password
			}
			else
				header("HTTP/1.0 500 Internal Server Error");
			// this is for demo purposes only (otherwise it would authenticate the user)
			echo json_encode($dataChainOut);
		}
		clearstatcache();
	}

	// ideally, this would ensure the url pointed to an image
	// and not just a file on the server, but we found this
	// to be more complicated than it was worth
	function imageExists($url){
		return file_exists($url);
	}
?>