<?php
/*
*Template Name: Original Home
*/

?>

<?php get_header(); ?>

  <?php roots_content_before(); ?>
        <?php 
          $recentlyviewed = eas_recently_viewed_works(0, true); 

        ?>
<div class="sliderwrapper">
    <div class="slider">
      <ul class="slideritems">
        <?php
          foreach ($recentlyviewed as $a) {
            echo '<li><div class="relative">';
            eas_figure_tag($a, 'slider-thumb');
            echo '</div></li>';
          }
        ?>
      </ul>
    </div>
  </div>
    <div id="content">
    <?php roots_main_before(); ?>
      <div id="main" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'home'); ?>
        <?php roots_loop_after(); ?>
      
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>
