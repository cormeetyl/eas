<?php
/*
Template Name: Open Call Submissions
*/
?>

<?php 

function oc_submissions($page = 0, $per_page = 10, $sort = 'recent') {
  global $wpdb;
  $contest = true;
  $contestname = 'opencall';
  if ($page == 0) $page = 1;
  $contest_query = '';
  if ($contest) {
    $contest_query = $wpdb->prepare(
      "
      join wp_postmeta pm
      on p.ID = pm.post_id
      and pm.meta_key = 'contest'
      and pm.meta_value = %s
      where p.post_status = 'draft' 
      ", $contestname
    );

    $user_type_query = "";
  } else {
    $contest_query = "where p.post_status = 'publish' ";
    $user_type_query = "              join (
                select distinct m2.*
                from wp_usermeta m2
                join wp_usermeta m3
                on m2.user_id = m3.user_id
                where m2.meta_key = 'wp_capabilities'
                and m2.meta_value like 'a:1:{s:6:\"artist\";s:1:\"1\";}'
                and m3.meta_key = 'verified'
                and m3.meta_value = 1
              ) as m
            on u.ID = m.user_id";
  }
    
    $query = $wpdb->prepare("
            select distinct u.ID
              from $wpdb->users u
              join wp_usermeta um ON um.user_id = u.ID
              join (
                select p.* 
                from wp_posts p
                ".$contest_query."
                and p.post_type = 'artwork'
                order by p.post_date desc  
              ) as a
              on u.ID = a.post_author
              ".$user_type_query."
          where um.meta_key = 'nickname'
          ORDER BY
          ".($sort == 'name' ? "um.meta_value ASC" : "a.post_date DESC")."
          limit %d offset %d
          ", $per_page, ($page-1)*$per_page);
      $author_ids = $wpdb->get_results($query);
      foreach ($author_ids as $author) {
        echo oc_display_user($author->ID, true, $contestname);
      }
}

?>

<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop','page'); ?>
        <?php roots_loop_after(); ?>

        <div class= "pg">
        Sort by: <?php if(!isset($_GET['sort'])): ?><strong>
                  <?php else: ?><a class="gray3" href="?sort=recent"><?php endif; ?>
                    Recently Updated 
                  <?php if(!isset($_GET['sort'])): ?></strong>
                 <?php else: ?></a><?php endif; ?>
                |
                 <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name'): ?><strong>
                    <?php else: ?><a class="gray3" href="?sort=name"><?php endif; ?>
                      Name
                    <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name'): ?></strong>
                  <?php else: ?></a><?php endif; ?>
        </div>
        <?php eas_page_links('opencall/submissions'); ?>

          <div id="artists_grid">
            <?php
              if(isset($_GET['sort']) && $_GET['sort'] == 'name') { 
                oc_submissions(get_query_var('paged'), 10, 'name');
              } else { 
                oc_submissions(get_query_var('paged'), 10, 'recent');
              }
            ?>
            <br class="clear" />
          </div>

        <?php eas_page_links('opencall/submissions'); ?>

      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>

<?php get_footer(); ?>

