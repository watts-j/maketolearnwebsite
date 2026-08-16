<?php // No templates/page-header here: the homepage shows its content without a page-title heading. ?>
<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/content', 'page'); ?>
<?php endwhile; ?>
