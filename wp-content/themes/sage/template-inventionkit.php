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
		$inventions = get_terms('inventions', 'orderby=id');	
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
                    );
		?>		
	  <div class="col-sm-6 col-md-4">
	    <div class="thumbnail">
	      <img class="aligncenter" <?php if($invention->description == null) { echo "data-src='holder.js/360x215?text=Inventions'"; } else { echo " src='".$invention->description."'"; } ?>" />
	      <div class="caption">
	        <h3><a href="<?php echo get_permalink($posts_array[0]->ID); ?>"><?php echo $invention->name; ?></a></h3>
	
	      </div>
	    </div>
	  </div>
<?php	} ?>
	   </div>
	</div>		
