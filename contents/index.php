<html>
<title>Rule Engine</title>
	<head>
		<?php require_once("../rule_engine_processor/sql_connect.php");	?>
		
		<link rel="stylesheet" type="text/css" href="../css/themes/bootstrap/easyui.css">
		<link rel="stylesheet" type="text/css" href="../css/themes/icon.css">
		<link rel="stylesheet" href="../css/main.css" />
		
		<script type="text/javascript" src="../js/jquery-2.1.0.min.js"></script>
		<script type="text/javascript" src="../js/jquery.easyui.min.js"></script>
		<script type="text/javascript" src="../js/ruleEngineProcess.js"></script>
	</head>
	<body>
		<div class="wrapper_prim">
			<div class='cont_fields'>
				<h1>Welcome for a happy shopping experience!!</h1>
			</div>
			<div id='selectMainSection' class='cont_fields'>
				<div title='Choose Product'>
					<?php require_once ('choose_product_section.php'); ?>
				</div>
				<div title='View Rules'>
					<?php require_once ('view_rules_section.php'); ?>
				</div>
			</div>
		</div>
	</body>
</html>
