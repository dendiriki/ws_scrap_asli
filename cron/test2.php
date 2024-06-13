<html>
	<head>
		
	</head>
	<body>
		<form action="#" method="post">
			<p>Input <input type="text" name="wsnum"></p>
			<p>Output <input type="text" name="wsnum_to"></p>
			<p><button type="submit" name="submit">Duplicate</button></p>
		</form>
		<?php
		if(!empty($_POST["wsnum"])) {
			$wsnum = $_POST["wsnum"];
			$wsnum_to = $_POST["wsnum_to"];
			require '../config.php';
			require '../classes/Inspection.php';
			$class = new Inspection();
			$data_hdr = $class->getHeaderPure($wsnum);
			unset($get_inspection);
			$data_hdr2 = $data_hdr;
			$data_hdr2["WSNUM"] = $wsnum_to;
			$save = $class->saveHeader($data_hdr2);
			print_r($save);
		}
		?>
	</body>
</html>
