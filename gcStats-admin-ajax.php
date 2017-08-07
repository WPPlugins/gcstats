<?php
    if(isset($_GET['action'])){
    	switch($_GET['action']){
    		case 'toggleFTF':{
    			$result = $wpdb->get_results("SELECT ftf FROM `".$wpdb->prefix."gcStats_waypoints` WHERE name=".$_POST['wp']."; ");
				if($result[0]->ftf == '1'){
					$ftfval = '0';
				} else {
					$ftfval = '1';
				}
				$result = $wpdb->get_results("UPDATE `".$wpdb->prefix."gcStats_waypoints` SET ftf='".$ftfval."' WHERE name=".$_POST['wp'].";");
				echo $ftfval;
    		}
    	}
    }
?>