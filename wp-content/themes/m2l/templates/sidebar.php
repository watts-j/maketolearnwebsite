<?php if (get_post_type() == 'activities') { 
		$fields = CFS()->get( 'steps' );
	$props = CFS()->get_field_info();
	$steps_title = CFS()->get('steps_title',array( 'format' => 'input' ));

	if(empty($fields)){
		 
	}	
	
	else { 
	?>
	<div class="well sidebar-nav">
	<?php $terms = wp_get_post_terms($post->ID, "inventions",array("fields" => "names")); ?>
	<h2><?php echo $terms[0]; ?></h2>
	<ul class="nav nav-pills nav-stacked">
        <li><a href="#intro" class="gotostep"><h4>Introduction</h4></a></li>
	<?php
	  foreach ( $fields as $field ) {
	  	echo "<li><a href='#".urlencode($field['steps_title'])."' class='gotostep'><h4>".$field['steps_title']."</h4></a></li>";
	  	}
	 }
?>
	</ul>	  
	</div>	
<?php } ?>
<?php dynamic_sidebar('sidebar-primary'); ?>
<a href="#" onclick="window.scrollTo(0, 0)" class="top_btn">Top</a>