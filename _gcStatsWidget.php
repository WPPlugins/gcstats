<?php
//require("classes/GcStatsRenderer.php");
    function gcStatsWidget_init() {
    	function gcStatsWidget($args){
    		extract($args);
			$options = get_option('widget_gcStatsWidget');
			echo $before_widget . $before_title . $options['title'] . $after_title;
			$args['width'] = $options['width'];
			$args['showImg'] = 'yes';
			echo GcStatsRenderer::getCachesByType($args);
			echo $after_widget;
    	}
		
		function gcStatsWidget_control(){
			$options = $newoptions = get_option('widget_gcStatsWidget');
			if ( isset($_POST["gcstats-widget-submit"]) ) {
				$newoptions['title'] = strip_tags(stripslashes($_POST["gcstats-widget-title"]));
				$newoptions['width'] = strip_tags(stripslashes($_POST["gcstats-widget-width"]));
			}
			if ( $options != $newoptions ) {
				$options = $newoptions;
				
				update_option('widget_gcStatsWidget', $options);
			}
			if(!is_array($options)){
				$options = array(
					'title' => 'gcStats Widget',
					'width' => '100'
				);
			}
			$title = attribute_escape($options['title']);
			$width = attribute_escape($options['width']);
			
			echo '<p><label for="gcstats-widget-title">'._e('Title:').'</label><input class="widefat" id="gcstats-widget-title" type="text" name="gcstats-widget-title" value="'.$title.'"></p>';
			echo '<p><label for="gcstats-widget-width">'._e('Width:').'</label><input class="widefat" id="gcstats-widget-width" type="text" name="gcstats-widget-width" value="'.$width.'"></p>';
			echo '<input type="hidden" id="gcstats-widget-submit" name="gcstats-widget-submit" value="1" />';
		}
		
		wp_register_sidebar_widget('widget_gcStatsWidget', 'gcStats Widget', 'gcStatsWidget',array(
			'classname' => 'widget_gcStatsWidget',
			'description' => 'Output some gcStats on your sidebar'
		));
		
		wp_register_widget_control('widget_gcStatsWidget', 'gcStats Widget', 'gcStatsWidget_control',array(
			'title' => 'gcStats Widget',
			'width' => '100'
		));
    }
	
	add_action('widgets_init', 'gcStatsWidget_init');
?>