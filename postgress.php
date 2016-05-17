<?php 

try {
	$bdcon3 = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=imortal");
	echo "OK";

} catch (Exception $e) {
	print_r($e->getMessage());
}

?>