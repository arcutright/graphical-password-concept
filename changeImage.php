<?php
	if(isset($_POST['postData'])){
		echo 'post data::: '.$_POST;
		//$dataChain
		//$clickPosition
	}
	else if(!empty($_POST)){
		echo $_POST;
	}
?>