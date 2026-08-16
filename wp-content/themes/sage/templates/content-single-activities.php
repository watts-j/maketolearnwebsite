
<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
	    <?php $terms = wp_get_post_terms($post->ID, "inventions",array("fields" => "names")); ?>
	    <?php $terms_slug = wp_get_post_terms($post->ID, "inventions",array("fields" => "slug")); ?>

     <?php /*  <h1 class="entry-title"><?php echo //$terms[0]." "; ?><?php the_title(); ?></h1> */ ?>
      <?php //get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
	   <a name="introduction"></a>
	   
      <?php the_content(); 

      $fields = CFS()->get( 'steps' );
      	if(empty($fields)){ 
		
		} 
		
		else { 
			foreach ( $fields as $field ) {
				echo "<a name='".urlencode($field['steps_title'])."'></a><h2>".$field['steps_title']."</h2>";
				echo $field['step'];
			}
		}
?>
    </div>
    <footer>
	  <div class="continue-nav">
		<div class="col-lg-3">
		  	<?php //previous_post_link('%link', TRUE, '', $taxonomy = 'inventions');?>
	  	</div>  
		<div class="col-lg-3 col-lg-offset-6"> 
			<?php //next_post_link('%link', TRUE, '', 'inventions');?>
		</div>
	  </div>
      <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
