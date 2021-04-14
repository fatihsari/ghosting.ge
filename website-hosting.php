<?php

	# WHMCS PAGE DATA:

		// Page Title
		$page_title 		= "Website Hosting";

		// Template File
		$template_file 		= "website-hosting";

	# BUILD PAGE:
	
		define("CLIENTAREA",true);
		require("init.php");
		$ca = new WHMCS_ClientArea();
		$ca->setPageTitle("".$page_title."");
		$ca->initPage();
		$ca->assign('pagetype', custom);
		$ca->setTemplate(''.$template_file.'');
		$ca->output();
	
?>