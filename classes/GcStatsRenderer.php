<?php
class GcStatsRenderer
{
    function getMaxDist($atts)
    {
        global $wpdb;
        extract(shortcode_atts( array (
	        'lat'=>'48.0',
	        'lon'=>'8.0',
	        'accountname'=>''
        ), $atts));
        $sql = "SELECT latitude, longitude, name, urlname FROM `".$wpdb->prefix."gcStats_waypoints`";
        if ($accountname != '')
        {
            $sql .= " AND accountname = '".$accountname."'";
        }
        $sql .= ";";
        $result = $wpdb->get_results($sql);
        $max = array ('dist'=>0);
        foreach ($result as $key=>$value)
        {
            $tempDist = self::getDistanceBetween($lat, $lon, $value->latitude, $value->longitude);
            if ($tempDist > $max->dist)
            {
                $max = $value;
                $max->dist = $tempDist;
            }
        }
        return '<a href="http://coord.info/'.$max->name.'" title="'.htmlentities($max->urlname).'" target="_blank">'.$max->name.' ('.round($max->dist, 1).' km)</a>';
    }

    function getDistanceBetween($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 = deg2rad(floatval($lat1));
        $lat2 = deg2rad(floatval($lat2));
        $lon1 = deg2rad(floatval($lon1));
        $lon2 = deg2rad(floatval($lon2));

        $w = acos(
        sin($lat1)*sin($lat2)
        +cos($lat1)*cos($lat2)*cos(($lon2-$lon1)));
        $s = $w*6370;
        return $s;
    }

    function _createPercentageBar($pct, $text, $width)
    {
        $html = '<div style="height:20px;width:'.$width.'px;">';
        $html .= '<div style="height:20px;width:'.$width.'px;background-color:grey;position:absolute;text-align:left;z-index:0;">';
        $html .= '<div style="color:white;height:20px;width:'.$pct.'%;background-color:green;text-align:center;">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div style="height:20px;width:'.$width.'px;color:white;text-align:center;position:absolute;z-index:1;">';
        $html .= $text;
        $html .= '</div></div>';

        return $html;
    }

    function getLastFounds($atts)
    {
        global $wpdb;
        extract(shortcode_atts( array (
	        'number'=>'1',
			'showNames' => '0',
	        'accountname'=>''
        ), $atts));

        $sql = "SELECT * FROM `".$wpdb->prefix."gcStats_waypoints` ";
        if ($accountname != '')
        {
            $sql .= " WHERE accountname = '".$accountname."'";
        }
        $sql .= " ORDER BY time DESC LIMIT ".$number.";";
        $result = $wpdb->get_results($sql);
        if (intval($number) > 1)
        {
            $html = '<table></tbody>';
            foreach ($result as $key=>$value)
            {
                $timeString = new DateTime($value->time);
                $timeString = $timeString->format('d.m.Y');
                $html .= '<tr><td><a href="http://coord.info/'.$value->name.'" target="_blank" title="'.htmlentities($value->urlname).'">'.$value->name;
				if($showNames != '0'){
					$html .= '&nbsp;('.htmlentities($value->urlname).')</a>';
				}
				$html .= '</td><td>'.$timeString.'</td></tr>';
            }
            $html .= '</tbody></table>';
            return $html;
        } else
        {
            return '<a href="http://coord.info/'.$result[0]->name.'" target="_blank">'.$result[0]->name.'</a>';
        }
    }

    function getCountFTF($atts)
    {
        global $wpdb;
		extract(shortcode_atts( array (
	        'accountname'=>''
        ), $atts));

        $sql = "SELECT count(id) as cnt FROM `".$wpdb->prefix."gcStats_waypoints` WHERE ftf='1'";
        if ($accountname != '')
        {
            $sql .= " AND accountname = '".$accountname."'";
        }
		$sql .= ";";
        $result = $wpdb->get_results($sql);
        return $result[0]->cnt;
    }

    function getCountFoundCaches($atts)
    {
        global $wpdb;
		extract(shortcode_atts( array (
	        'accountname'=>''
        ), $atts));

        $sql = "SELECT COUNT(*) AS cnt FROM `".$wpdb->prefix."gcStats_waypoints`";
		if ($accountname != '')
        {
            $sql .= " AND accountname = '".$accountname."'";
        }
		$sql .= ";";
        $result = $wpdb->get_results($sql);
        if ($result[0]->cnt)
        {
            return $result[0]->cnt;
        } else
        {
            return 0;
        }
    }

    function getCachesByTypeFlash($atts)
    {
        global $wpdb;
        extract(shortcode_atts( array (
	        'bgcolor'=>'#FFFFFF',
	        'height'=>'200',
	        'width'=>'350',
	        'title'=>'Caches By Type',
	        'accountname'=>''
        ), $atts));

        $sql = "SELECT COUNT(*) AS cnt, gsCacheType AS type FROM `".$wpdb->prefix."gcStats_waypoints` ";
		if ($accountname != '')
        {
            $sql .= " WHERE accountname = '".$accountname."'";
        }
		$sql .= "GROUP BY gsCacheType;";
        $result = $wpdb->get_results($sql);
        $tmpValues = array ();
        foreach ($result as $key=>$value)
        {
            array_push($tmpValues, array ("value"=>intval($value->cnt), "label"=>$value->type));
        }

        $outData = array (
	        "elements"=> array (
		        array (
			        "type"=>"pie",
			        "colours"=> array ("#d01f3c", "#356aa0", "#C79810", "#F78E1F", "#AAAAAA", "#DDDDDD"),
			        "values"=>$tmpValues,
			        "animate"=>array(array("type"=>"fade"),array("type"=>"bounce", "distance"=>5))
		        )
        	),
	        "title"=> array (
	        	"text"=>$title
        	),
        	"bg_colour"=>$bgcolor
        );

        $html = '<script type="text/javascript">
				swfobject.embedSWF(
					"'.WP_PLUGIN_URL.'/gcstats/ofc/open-flash-chart.swf", 
					"chart_cachetypes", 
					"'.$width.'", 
					"'.$height.'", 
					"9.0.0",
					"expressInstall.swf",
					{"get-data":function(){return "'.addslashes(json_encode($outData)).'";}}
				);
				</script>
				<div id="chart_cachetypes"></div>
				';
        return $html;
    }

    function getCachesByType($atts)
    {
        global $wpdb;
        extract(shortcode_atts( array (
	        'useflash'=>'no',
	        'width'=>'200',
	        'showImg'=>'no',
			'accountname'=>''
        ), $atts));
        if ($useflash === 'yes')
        {
            return self::getCachesByTypeFlash($atts);
        }

		$sql = "SELECT COUNT(*) AS cnt, gsCacheType AS type FROM `".$wpdb->prefix."gcStats_waypoints` ";
		if ($accountname != '')
        {
            $sql .= " WHERE accountname = '".$accountname."'";
        }
		$sql .= "GROUP BY gsCacheType ORDER BY cnt;";
        $result = $wpdb->get_results($sql);

        $total = self::getCountFoundCaches($atts);

        $html = '<table><tbody>';
        foreach ($result as $key=>$value)
        {
            $pct = 100/$total*$value->cnt;
            if ($showImg === 'no')
            {
                $html .= '<tr><td>'.$value->type.'</td><td>'.self::_createPercentageBar(round($pct, 1), $value->cnt.' ('.round($pct, 1).'%)', $width).'</td></tr>';
            } else
            {
                $html .= '<tr><td><img src="'.WP_PLUGIN_URL.'/gcstats/img/container/'.strtolower(str_replace(' ', '', $value->type)).'.gif" height="20" title="'.$value->type.'"></img></td><td>'.self::_createPercentageBar(round($pct, 1), $value->cnt.' ('.round($pct, 1).'%)', $width).'</td></tr>';
            }
        }
        $html .= '</tbody></table>';
        return $html;
    }

    function getCachesByContainerFlash($atts)
    {
        global $wpdb;
        extract(shortcode_atts( array (
	        'bgcolor'=>'#FFFFFF',
	        'height'=>'200',
	        'width'=>'350',
	        'title'=>'Caches By Container'
        ), $atts));
		
		$sql = "SELECT COUNT(*) AS cnt, gsCacheContainer AS container FROM `".$wpdb->prefix."gcStats_waypoints` GROUP BY gsCacheContainer;";
        $result = $wpdb->get_results($sql);

        $tmpValues = array ();
        foreach ($result as $key=>$value)
        {
            array_push($tmpValues, array ("value"=>intval($value->cnt), "label"=>$value->container));
        }

        $outData = array (
	        "elements"=> array (
		        array (
			        "type"=>"pie",
			        "colours"=> array ("#d01f3c", "#356aa0", "#C79810", "#F78E1F", "#AAAAAA", "#DDDDDD"),
			        "values"=>$tmpValues,
			        "animate"=>array(array("type"=>"fade"),array("type"=>"bounce", "distance"=>5))
		        )
	        ),
	        "title"=> array (
	        "text"=>$title
	        ),
	        "bg_colour"=>$bgcolor
        );

        $html = '<script type="text/javascript">
			swfobject.embedSWF(
				"'.WP_PLUGIN_URL.'/gcstats/ofc/open-flash-chart.swf", 
				"chart_cachecontainer", 
				"'.$width.'", 
				"'.$height.'", 
				"9.0.0",
				"expressInstall.swf",
				{"get-data":function(){return "'.addslashes(json_encode($outData)).'";}}
			);
			</script>
			<div id="chart_cachecontainer"></div>
			';
        return $html;
    }

    function getCachesByContainer($atts)
    {
        global $wpdb;
        extract(shortcode_atts( array (
        'useflash'=>'no'
        ), $atts));
        if ($useflash === 'yes')
        {
            return self::getCachesByContainerFlash($atts);
        }
		
		$sql = "SELECT COUNT(*) AS cnt, gsCacheContainer AS container FROM `".$wpdb->prefix."gcStats_waypoints` GROUP BY gsCacheContainer ORDER BY cnt;";
        $result = $wpdb->get_results($sql);

        $total = self::getCountFoundCaches($atts);
        $html = '<table><tbody>';
        foreach ($result as $key=>$value)
        {
            $pct = 100/$total*$value->cnt;
            $html .= '<tr><td>'.$value->container.'</td><td>'.self::_createPercentageBar(round($pct, 1), $value->cnt.' ('.round($pct, 1).'%)', 200).'</td></tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
	
    function getCachesByWeekdayFlash($atts)
    {
        global $wpdb;
        extract(shortcode_atts( array (
	        'bgcolor'=>'#FFFFFF',
	        'height'=>'200',
	        'width'=>'350',
	        'title'=>'Caches By Weekdays'
        ), $atts));
        $result = $wpdb->get_results("SELECT time FROM `".$wpdb->prefix."gcStats_waypoints`;");
        $arrDays = array ();
        foreach ($result as $key=>$value)
        {
            $timeString = new DateTime($value->time);
            $timeString = $timeString->format('D');
            $arrDays[$timeString]++;
        }
        $tmpValues = array ();
        foreach ($arrDays as $key=>$value)
        {
            array_push($tmpValues, array ("value"=>intval($value), "label"=>$key));
        }
    
        $outData = array (
	        "elements"=> array (
		        array (
			        "type"=>"pie",
			        "colours"=> array ("#d01f3c", "#356aa0", "#C79810", "#F78E1F", "#AAAAAA", "#DDDDDD"),
				    "values"=>$tmpValues,
				    "animate"=>array(array("type"=>"fade"),array("type"=>"bounce", "distance"=>5))
			    )
		    ),
		    "title"=> array (
		        "text"=>$title
	        ),
	        "bg_colour"=>$bgcolor
        );
    
        $html = '<script type="text/javascript">
    			swfobject.embedSWF(
    				"'.WP_PLUGIN_URL.'/gcstats/ofc/open-flash-chart.swf", 
    				"chart_cacheweekdays", 
    				"'.$width.'", 
    				"'.$height.'", 
    				"9.0.0",
    				"expressInstall.swf",
    				{"get-data":function(){return "'.addslashes(json_encode($outData)).'";}}
    			);
    			</script>
    			<div id="chart_cacheweekdays"></div>
    			';
        return $html;
    }
  
    function getCachesByWeekday($atts)
    {
        global $wpdb;
        extract(shortcode_atts( array (
	        'useflash'=>'no',
	        'top'=>'no'
        ), $atts));
        if ($useflash === 'yes')
        {
            return self::getCachesByWeekdayFlash($atts);
        }
        $result = $wpdb->get_results("SELECT time FROM `".$wpdb->prefix."gcStats_waypoints`;");
        $html = '<table></tbody>';
        $arrDays = array ();
        foreach ($result as $key=>$value)
        {
            $timeString = new DateTime($value->time);
            $timeString = $timeString->format('D');
            $arrDays[$timeString]++;
        }
        arsort($arrDays);
        $total = self::getCountFoundCaches($atts);
    
        foreach ($arrDays as $key=>$value)
        {
            $pct = 100/$total*$value;
            $html .= '<tr><td>'.$key.'</td><td>'.self::_createPercentageBar(round($pct, 1), $value.' ('.round($pct, 1).'%)', 200).'</td></tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
	
    function getDiffTerrMatrix($atts)
    {
        global $wpdb;
    
        $result = $wpdb->get_results("
    	SELECT COUNT(*) AS cnt, gsDifficulty, gsTerrain FROM `".$wpdb->prefix."gcStats_waypoints` 
    	GROUP BY gsDifficulty, gsTerrain
    	ORDER BY gsDifficulty, gsTerrain;
    	");
    
        $html = '<table><tr><td></td><td>Terrain</td></tr><tr><td>Difficulty</td><td>';
		$html .= '<table style="border:1px solid black;" border="1" cellpadding="0" cellspacing="0"><tbody><tr><td style="width:30px;background-color:grey;color:white;"></td>';
        for ($i = 1; $i <= 5; $i += 0.5)
        {
            $html .= '<td style="width:30px;background-color:grey;color:white;">'.$i.'</td>';
        }
        $html .= '</tr>';
        $challengeArray = array ();
        for ($i = 1; $i <= 5; $i += 0.5)
        {
            for ($j = 1; $j <= 5; $j += 0.5)
            {
                $challengeArray[(string)$i][(string)$j] = '0';
            }
        }
        foreach ($result as $key=>$value)
        {
            $challengeArray[$value->gsDifficulty][$value->gsTerrain] = $value->cnt;
        }
    
        for ($i = 1; $i <= 5; $i += 0.5)
        {
            $html .= '<tr><td style="width:30px;background-color:grey;color:white;">'.$i.'</td>';
            for ($j = 1; $j <= 5; $j += 0.5)
            {
                $html .= '<td ';
                if ($challengeArray[(string)$i][(string)$j] == "0")
                {
                    $html .= 'style="background-color:red;" >';
                } else
                {
                    $html .= 'style="background-color:green;" >';
                }
                $html .= $challengeArray[(string)$i][(string)$j];
                $html .= '</td>';
            }
            $html .= "</tr>";
        }
        $html .= '</tbody></table></td></tr></table>';
        return $html;
    }
}

?>
