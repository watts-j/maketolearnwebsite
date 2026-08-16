<?php
/**
 * Template Name: InventionKits Listing Template
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'page'); ?>
<?php endwhile; ?>
	<div class="invention-listing">	
		<div class="row">
<?php
        $args = array (
            'post_type' => 'kitgroup'
        );
        $kitgroup_query = new WP_Query($args);
            
        // The Loop
        if ( $kitgroup_query->have_posts() ) {
            while ( $kitgroup_query->have_posts() ) {
                echo '<div class="kit-group">';
                $kitgroup_query->the_post();
                echo '<h3>' . get_the_title() . '</h3>';
                $theinventions = get_the_terms(get_the_ID(), 'inventions');
                echo '<div class="back"></div><div class="kitlist">';
              	$sortedinventions = [];
                foreach ( $theinventions as $rawinv ) {
                  $the_order = get_field("order", "inventions_".$rawinv->term_id);
                  $sortedinventions[$the_order] = $rawinv;
                }
              	ksort($sortedinventions, SORT_NUMERIC );
              	foreach ($sortedinventions as $inv) {
                    $posts_array = get_posts( 
                        array(
	                        'showposts' => 1,
                            'post_type' => 'activities',
                            'orderby' => 'menu_order',
                            'order' => 'ASC',
                            'tax_query' => array(
                                array(
                                'taxonomy' => $inv->taxonomy,
                                'field' => 'slug',
                                'terms' => $inv->slug,
                                )
                            )
                        )
                    ); ?>        
            
            
                     <div class="kit"><a href="<?php echo get_permalink($posts_array[0]->ID); ?>">
                        <?php $the_img = get_field("image_url", "inventions_".$inv->term_id);
                        if($the_img) { ?>
                            <img src="<?php echo $the_img ?>" />
                        <?php } ?>
                        <h4><?php echo $inv->name; ?></h4>
                         </a>
                    </div>
                    
                    
                <?php }
                //print_r($inventions);
                echo '</div><div class="forward"></div></div>';
            }
            /* Restore original Post Data */
            wp_reset_postdata();
        } else {
            echo "Sorry, no Kit Groups found";
        }
		/*$inventions = get_terms('inventions', 'orderby=id');	
		foreach ($inventions as $invention) { 
			$invention_link = get_term_link($invention);
			if (is_wp_error($invention_link)) { 
				continue;
			}
			$posts_array = get_posts( 
                        array(
	                        'showposts' => 1,
                            'post_type' => 'activities',
                            'orderby' => 'menu_order',
                            'order' => 'ASC',
                            'tax_query' => array(
                                array(
                                'taxonomy' => $invention->taxonomy,
                                'field' => 'slug',
                                'terms' => $invention->slug,
                                )
                            )
                        )
                    );*/
	/*	?>		
	  <div class="col-sm-6 col-md-4">
	    <div class="thumbnail">
	      <img class="aligncenter" data-src="holder.js/360x215?text=Inventions" />
	      <div class="caption">
	        <h3><a href="<?php echo get_permalink($posts_array[0]->ID); ?>"><?php echo $invention->name; ?></a></h3>
	
	      </div>
	    </div>
	  </div>
<?php	} */?>
	   </div>
	</div>		
