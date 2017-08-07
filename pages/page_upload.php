<div class="wrap">
    <h2>gcStats - Upload GPX-File</h2>
	Here you can upload your "MyFinds"-GPX-File.
    <form action="" enctype="multipart/form-data" method="post">
        <input type="file" name="gpxfile"><p class="submit">
            <input type="submit" name="submit" class="button-primary" value="upload" />
        </p>
    </form>
	<?php 
		if(isset($gpxDoc)){
			echo '<i>'; 
			echo 'imported gpx-file with '.$gpxDoc->countWayPoints().' WayPoints <br />';
			echo 'Account: '.$gpxDoc->getAccountname().'<br />';
			echo '.gpx-File was created at: '.$gpxDoc->getTime().'<br />';
			echo $gpxDoc->newRecords.' new WayPoints imported.<br />';
			echo '</i>';
		}
	?>
</div>