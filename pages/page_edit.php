<div class="wrap">
	<script type="text/javascript">
		jQuery(document).ready( function($){
			$("span.inline a").click(function (){
				var This = this;
				$.post(
					'<?php bloginfo('wpurl');?>/wp-admin/admin-ajax.php',
					{
						action:'gcStats_toggleFTF',
						wp:this.getAttribute('wp')
					},
					function(data){
						if (data == '1') {
							$(This).html('Unset FTF');
							$("#colFTF_"+This.getAttribute('wp')).html('Yes');
						} else {
							$(This).html('Set FTF');
							$("#colFTF_"+This.getAttribute('wp')).html('No');
						}
					}
				);
				return false;
			});
		});
	</script>
    <div id="icon-edit-pages" class="icon32"><br /></div>
<h2>gcStats - Edit</h2>
	<br />
	<?php
	if($_GET['action'] == 'updateCJRatings' && $_GET['page'] == 'gcStats_edit'){
		gcStats_getCronCJRating();
	}
	?>
	<form action="admin.php?page=gcStats_edit&action=updateCJRatings" method="post">
		<input type="submit" value="Update CacheJudge-Ratings" class="button-secondary action" onclick="this.disable();"><br />
		This is an experimental feature.
	</form>
	<br />
<?php
if($_GET['action'] == 'edit' && $_GET['page'] == 'gcStats_edit'){
	$resultWP = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."gcStats_waypoints` WHERE ID=".$_GET['waypoint'].";");
?>
	<form action="admin.php?page=gcStats_edit&action=save_edit" method="post">
		<table>
			<tbody>
				<tr><td>Name</td><td><input type="text" name="name" id="name" value="<?php echo $resultWP[0]->name;?>" /></td></tr>
				<tr><td>Description</td><td><textarea name="desc" id="desc" ><?php echo $resultWP[0]->description;?></textarea></td></tr>
				<tr><td>Time</td><td><input type="text" name="time" id="time" value="<?php echo $resultWP[0]->time;?>" /></td></tr>
				<tr><td>FTF</td><td><input type="checkbox" name="ftf" id="ftf" <?php if($resultWP[0]->ftf == "1"){echo 'checked="checked"';} ?> /></td></tr>
				<tr><td>Container</td><td><input type="text" name="gsCacheContainer" id="gsCacheContainer" value="<?php echo $resultWP[0]->gsCacheContainer;?>" /></td></tr>
				<tr><td></td><td><input type="submit" name="btnUpdate" id="btnUpdate" value="Update" /></td></tr>
			</tbody>
		</table>
	</form>
<?php
?>
<?php
} else {
	if($_GET['action'] == 'save_edit' && $_GET['page'] == 'gcStats_edit'){
		
	}
?>
	<table cellpadding="0" cellspacing="0" border="0" class="widefat">
		<thead>
			<tr>
				<th style="width:100px;">Name</th>
				<th style="width:250px;">Description</th>
				<th style="width:100px;">Time</th>
				<th style="width:100px;">FTF</th>
				<th style="width:100px;">Account</th>
				<th style="width:100px;">AVG CacheJudge.com Rating</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th style="width:100px;">Name</th>
				<th style="width:250px;">Description</th>
				<th style="width:100px;">Time</th>
				<th style="width:100px;">FTF</th>
				<th style="width:100px;">Account</th>
				<th style="width:100px;">AVG CacheJudge.com Rating</th>
			</tr>
		</tfoot>
		<tbody>
<?php
	$sql = "SELECT * FROM `".$wpdb->prefix."gcStats_waypoints`";
	if(isset($_GET['accountname'])){
		$sql .= " WHERE accountname='".$_GET['accountname']."' ";
	}
	$sql .= ";";
	$resultAllWP = $wpdb->get_results($sql);
	foreach($resultAllWP as $key => $value){
		$editlink = 'admin.php?page=gcStats_edit&action=edit&waypoint='.$value->id;
		echo '<tr class="alternate iedit">';
		echo '<td class="post-title page-title column-title"><strong><a class="row-title" href="'.$editlink.'" title="Edit">'.$value->name.'</a></strong>';
		echo '<div class="row-actions"><span class="edit"><a href="'.$editlink.'">edit</a></span> | ';
		echo '<span class="inline"><a href="admin-ajax.php" class="editinline" wp="'.$value->name.'">';
		if($value->ftf == '1'){
			echo 'Unset FTF';
		} else {
			echo 'Set FTF';
		}
		echo '</a></span></div>';
		echo '</td>'; 
		echo '<td>'.$value->description.'</td><td>'.$value->time.'</td>';
		echo '<td id="colFTF_'.$value->name.'">';
		if($value->ftf == '1'){
			echo 'Yes';
		} else {
			echo 'No';
		}
		echo '</td>';
		echo '<td>'.$value->accountname.'</td>';
		echo '<td>'.$value->CJRatingAVG ;
		if( strlen($value->CJDetailLink) > 0 ){
			echo '&nbsp;<a href="'.$value->CJDetailLink.'" target="_blank">Details</a>';
		}
		echo '</td>';
		echo '</tr>';
	}
?>
	</tbody></table>
<?php
	}
?>
</div>
