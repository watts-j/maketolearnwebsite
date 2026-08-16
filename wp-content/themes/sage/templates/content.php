<article <?php post_class(); ?>>
  <header>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php //get_template_part('templates/entry-meta'); ?>
    
  </header>
  <div class="entry-summary">
	  
    <?php the_content(); ?>
    <?php 
      $fields = CFS()->get( 'steps' );
      
	  foreach ( $fields as $field ) {
	  	echo "<h2>".$field['steps_title']."<a name='".$field['steps_title']."'></a></h2>";
	  	echo $field['step'];
	} 
	?>
  </div>
</article>
