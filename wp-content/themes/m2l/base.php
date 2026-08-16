<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

$makepages = [];

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
      <div class="masthead">
    <?php
      do_action('get_header');
      get_template_part('templates/header');
    ?>
    <!-- Main Navigation -->
	<div class="nav">
	    <div class="container">
		    <nav id="site-navigation" class="nav-primary main-navigation" role="navigation">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'maketolearn' ); ?></button>
             
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
	<?php if (get_post_type() == 'activities') { ?>
			 	<?php  $terms = get_the_terms( $post->ID , 'inventions' );
				$haveamake = false;
				$conglom = false;
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
					
	?>
    <!-- Main Content -->
    <div class="wrap" role="document">
        <nav class="secondary-menu">
			    <ul id="menu-inventions" class="nav container">
					<?php
					while ($nav_qu->have_posts())  {
						$nav_qu->the_post();
						$conglom = get_field("conglomerate", $nav_qu->ID);          
	        $makepages = get_field("makes_to_conglomerate", $nav_qu->ID);
						
if(!$conglom) {
						?>
					<li id="post-<?php the_ID();  ?>" <?php if($current_post_id === get_the_ID()) { post_class('current-menu-item'); } else { post_class(); } ?>><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
				<?php	} 
            
            if ($makepages) { ?>
						<li id="post-<?php the_ID();  ?>" <?php if($current_post_id === get_the_ID()) { post_class('current-menu-item'); } else { post_class(); } ?>><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">Make</a></li>
					<?php }
				} 
			 ?>  
		    </ul></nav>
    	</div>
    	<?php 
	    	}
	    wp_reset_postdata();
	 } else {
        echo '<nav class="secondary-menu"></nav>';
    }

	
	$conglom = get_field("conglomerate", get_the_ID());
	$makepages = get_field("makes_to_conglomerate", $nav_qu->ID);
  $conglom_parent = get_field("conglomerate_parent", $nav_qu->ID);
	if($conglom) {
        echo '<div class="wrap" role="document"><nav class="tertiary-menu"><ul class="nav container" id="menu-make">';
    if(!$makepages) { 
    	$makepages = get_field("makes_to_conglomerate", $conglom_parent->ID);
    }
			foreach($makepages as $mpage) { ?>
			<li id="post-<?php the_ID();  ?>" <?php if($current_post_id === $mpage->ID) { post_class('current-menu-item'); } else { post_class(); } ?>><a href="<?php echo get_permalink($mpage->ID); ?>" title="<?php the_title_attribute( array( 'post' => $mpage->ID)); ?>"><?php echo get_the_title($mpage->ID); ?></a></li>
		
		<?php }
		echo '</ul></nav></div>';
    
	}

	    ?>
   <div class="container">
    <?php
        $url = "";
    echo '<ul id="crumbs">';
$the_url = strtok($_SERVER['REQUEST_URI'], '?');
    $parts = explode("/", $the_url);
        //print_r($parts);
    echo '<li><a href="/">Home</a></li>';
    $terms ='';
    foreach ($parts as $key => $dir) {
        //echo $dir;
        switch ($dir) {
            case "inventions-kits": 
                $label = "Invention-kits"; 
                $dir = "invention-kits"; 
                $terms = wp_get_post_terms($current_post_id, 'inventions');
                break;
            default: $label = ucwords($dir); break;
        }
            if ($dir == "") {
                //nothing
            } else {
            $url .= "/".$dir; 
                echo '<li> &raquo; <a href="'.$url.'">'.$label.'</a></li>';
            }
        
        if($terms) {
             $posts_array = get_posts( 
                        array(
	                        'showposts' => 1,
                            'post_type' => 'activities',
                            'orderby' => 'menu_order',
                            'order' => 'ASC',
                            'tax_query' => array(
                                array(
                                'taxonomy' => $terms[0]->taxonomy,
                                'field' => 'slug',
                                'terms' => $terms[0]->slug,
                                )
                            )
                        )
                    );
                echo '<li> &raquo; <a href="'.get_permalink($posts_array[0]->ID).'">'.$terms[0]->name.'</a></li>';
                echo '<li> &raquo; <a href="'.$the_url.'">'.get_the_title().'</a></li>';

            break;
        }
    }
    echo "</ul>";
    ?>
    <div class="content row">
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