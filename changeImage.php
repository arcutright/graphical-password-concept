<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if(isset($_POST['postData'])){
		$raw = json_decode($_POST['postData']);
		// verify posted data is well-formed
		if((array)$raw === $raw && sizeof($raw) == 3){
			$dataChainIn = $raw[0];
			$currentImageURL = $raw[1];
			$chosenGridPosition = $raw[2];
			// verify current image
			if(url_exists($currentImageURL)){
				// calculate next image, add to chain (+encrypt?)
				$dataChainOut = '?';
				$nextImageURL = '?';
			}
			else
				header("HTTP/1.0 500 Internal Server Error");
			// respond with [chain, next image url]
			echo json_encode([$dataChainOut, $nextImageURL]);
		}

	}

	function url_exists($url){
	   $headers = get_headers($url);
	   return stripos($headers[0],"200 OK") ? true : false;
	}
?>