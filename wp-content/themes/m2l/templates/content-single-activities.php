<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
	    <?php $terms = wp_get_post_terms($post->ID, "inventions",array("fields" => "names")); ?>
	    <?php $terms_slug = wp_get_post_terms($post->ID, "inventions",array("fields" => "slug")); ?>

     <?php /*  <h1 class="entry-title"><?php echo //$terms[0]." "; ?><?php the_title(); ?></h1> */ ?>
      <?php //get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
	   <div class="step" id="intro">
	   <a name="introduction"></a>
      <?php the_content(); 

$fields = CFS()->get( 'steps' );
       
$diff = get_field("difficulty", $post->ID);
$time = get_field("estimated_time", $post->ID);
$ismake = get_field("is_make_page", $post->ID);
 
if( $ismake ) {
?>
	<div class="meta-info">
	<div class="meta-column">
	<?php 
		if($diff) {
			echo '<div class="diff"><b>Difficulty:</b> '.$diff.'</div>';
		} ?>
	<?php 
		$temp = count($fields);
		if($temp) {
			echo '<div class="numsteps"><b>Steps:</b> '.$temp.'</div>';
		} ?>
	<?php 
		if($time) {
			echo '<div class="est"><b>Time:</b> '.$time.' min</div>';
		} ?>
	</div>
	<div class="meta-column"> 
		<?php $temp = get_field("tools", $post->ID);
		if($temp) {
			echo '<div class="tools"><b>Tools</b> <br/>'.$temp.'</div>';
		} ?>
	</div>
	<div class="meta-column"> 
		<?php $temp = get_field("parts", $post->ID);
		if($temp) {
			echo '<div class="parts"><b>Parts</b> <br/>'.$temp.'</div>';
		} ?>
	</div>
	<div class="meta-column"> 
		<?php $temp = get_field("cad_file", $post->ID);
		if($temp) {
			echo '<div class="cad"><b>CAD:</b><br/>';?>
			<a href="<?php echo $temp['url']; ?>"><?php echo $temp['title']; ?></a></div>
		<?php } ?>
	</div>
	<div class="meta-column"> 
		<?php $temp = get_field("resource_link", $post->ID, false);
		if($temp) {
			echo '<div class="resources"><b>Resources:</b><br/>';?>
			<ul>
			<?php foreach( $temp as $link ): ?>
			<li>
				<a href="<?php echo get_the_permalink($link); ?>"><?php echo get_the_title($link); ?></a>
			</li>
			<?php endforeach; ?>
			</ul>

		<?php 	 } ?>
	</div>


	</div>
<?php } ?>
	</div>
</div>
<?php      
      	if(empty($fields)){ 
		
		} 
		
		else { 
			foreach ( $fields as $field ) {
                $thestep = urlencode($field['steps_title']);
				echo "<div class=\"step\" id=\"".$thestep."\"><a name='".urlencode($field['steps_title'])."'></a><h2>".$field['steps_title']."</h2>";
				echo $field['step']."</div>";
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