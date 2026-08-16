<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

?>

<!doctype html>
<html <?php language_attributes(); ?>>
  <?php get_template_part('templates/head'); ?>
  <body <?php body_class(); ?>>
    <!--[if IE]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
      </div>
    <![endif]-->
    <?php
      do_action('get_header');
      get_template_part('templates/header');
    ?>
    <!-- Main Navigation -->
	<div class="nav">
	    <div class="container">
		    <nav class="nav-primary navbar-default">
		      <?php
		      if (has_nav_menu('primary_navigation')) :
		        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav nav-pills nav-justified']);
		      endif;
		      ?>
		    </nav>
	    </div>
    </div>
    <!-- /end main navigation -->
	<?php if (get_post_type() == 'activities') { ?>
			 	<?php  $terms = get_the_terms( $post->ID , 'inventions' );
				$args = array(
				'post_type' => 'activities',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'tax_query' => array(
					array(
						'taxonomy' => 'inventions',
						'field'    => 'slug',
						'terms'    => $terms[0]->slug,
						),
					),
				);
				$current_post_id = get_the_ID();
				$nav_qu = new WP_Query( $args );
				if($nav_qu->have_posts()) {
					if(get_post_field('menu_order',$current_post_id) == 0)  { 
						
					} else { 
	?>
    <!-- Main Content -->
    <div class="wrap" role="document">
	     <div class="nav" style="margin-top:30px;">
		    <nav class="nav-secondary navbar-default">
			    <ul id="menu-inventions" class="nav nav-pills nav-justified">
					<?php
					while ($nav_qu->have_posts())  :
						$nav_qu->the_post(); 
						if($nav_qu->current_post==0) { 
						}
						else {
						?>
					<li id="post-<?php the_ID();  ?>" <?php if($current_post_id === get_the_ID()) { post_class('current-menu-item'); } else { post_class(); } ?>><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
				<?php	
					  } 
					  endwhile;
				} 
			 ?>  
		    </nav>
    	</div>
    	<?php 
	    	}
	    wp_reset_postdata();
	 } 
	    ?>
   <div class="container">
    <div class="content row">
	     <?php if (Setup\display_sidebar()) : ?>
          <aside class="sidebar">
            <?php include Wrapper\sidebar_path(); ?>
          </aside><!-- /.sidebar -->
        <?php endif; ?>  
        <main class="main">
          <?php include Wrapper\template_path(); ?>
        </main><!-- /.main -->
    </div>
      </div><!-- /.content -->
    </div><!-- /.wrap -->
    <?php
      do_action('get_footer');
      get_template_part('templates/footer');
      wp_footer();
    ?>
  </body>

</html>
