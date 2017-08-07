<div class="wrap">
	<h2>gcStats - Error-Log</h2>
	Here you will find some Informations about failed Database-Actions.<br />
	<table>
		<tbody>
<?php
	$result = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."gcStats_error_log`");
	if(sizeof($result > 0)){
		foreach($result as $key => $value){
			echo '<tr><td>'.$value->message.'</td><td>'.$value->timestamp.'</td></tr>';
		}
	} else {
		echo "<tr><td>No Errors in ErrorLog at the moment</td></tr>";
	}
?>
		</tbody>
	</table>
</div>