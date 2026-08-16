<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

?>

<!doctype html>
<html <?php language_attributes(); ?>>
  <?php get_template_part('templates/head'); ?>
  <body <?php body_class('home'); ?>>
    <!--[if IE]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
      </div>
    <![endif]-->
      <div class="masthead">
    <?php
      do_action('get_header');
      get_template_part('templates/header');
    ?>
    <!-- Main Navigation -->
	<div class="nav">
	    <div class="container">
		    <nav id="site-navigation" class="nav-primary main-navigation" role="navigation">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'eightwords' ); ?></button>
             
		      <?php
		      if (has_nav_menu('primary_navigation')) :
		        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'm2l-nav']);
		      endif;
		      ?>
		    </nav>
	    </div>
    </div>
    <!-- /end main navigation -->
      </div>
      <nav class="secondary-menu"></nav>
	
   <div class="container">
     <div class="content row">
       
        <main class="main">
          <?php 
            $args = array(
                'post_type' => 'post',
                'order' => 'DESC',
                        'posts_per_page' => 1,
            );
            $blog_qu = new WP_Query( $args );
                    while ($blog_qu->have_posts())  {
                $blog_qu->the_post(); 
                ?>
          
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          	<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
            <div class="text"><?php the_content(); ?></div>
          </article>
                        
          <?php }
               wp_reset_postdata(); ?>
				</main><!-- /.main -->
          
        <?php get_sidebar( 'primary' ); ?>
       
        </div><!-- /.content row -->
    </div><!-- /.container -->    
   <!-- /.content -->

    </div><!-- /.wrap -->
    <?php
      do_action('get_footer');
      get_template_part('templates/footer');
      wp_footer();
    ?>
  </body>

</html>


<?php
/*
<div style="background: white; padding: 8px; border: 1px solid #666">
             <h3>Join the MakeToLearn Mailing List</h3>
              <?php echo do_shortcode('[ctct form="1331"]'); ?>
            </div>
*/