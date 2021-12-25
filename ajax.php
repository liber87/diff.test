<?php
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		include_once './diff.class.php';
		if ((isset($_POST['text1'])) && (isset($_POST['text1']))){					
			$diff = new Diff($_POST['text1'],$_POST['text2'],$_POST['compare']);
			if ($_POST['compare']=='nr') echo $diff->getTable();
			else echo $diff->getProposals();
			exit();
		}	
	}
