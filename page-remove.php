<?php

/**
 * Auto cron job with moving offset to 
 * manipulate the posts/ products using WP loop
 * Template Name: AutoCronJob
 * @package WordPress
 * @author     Patrik
 */

wp_no_robots();
get_header();


//! here we set what is the offset
define("OFFSET_SIZE", 1000);

reset_offset();
print_offset();
// do not forget to reset the offset on the first run

// WP_Query
$product_query = new WP_Query(
   array(
      // less is more- due to performance
      'posts_per_page' => 1000,
      'offset' => get_offset(),
      'orderby'    => 'name',
      'order'      => 'ASC',
      'post_type' => 'product'
   )
);

increase_offset(OFFSET_SIZE);

if ($product_query->have_posts()) :
   while ($product_query->have_posts()) : $product_query->the_post();
   //! your function here
   //! e. g. remove_duplicates(get_the_title($post));
   endwhile;
   wp_reset_postdata();
endif;


/**
 * Check if option exists in the database
 * @param string   $name  Name of the offset
 * @param boolean  $site_wide If an option is used on whole site
 */
function option_exists($name, $site_wide = false)
{
   global $wpdb;
   return $wpdb->query("SELECT * FROM " . ($site_wide ? $wpdb->base_prefix : $wpdb->prefix) . "options WHERE option_name ='$name' LIMIT 1");
}

/**
 * Increase offset by the amount
 * @param int $amount  An amount to increase offset by
 */
function increase_offset($amount)
{
   $offset = 0;

   // If option does not exists add to the DB else
   // get the value from DB
   if (!option_exists("offset")) {
      echo 'adding option offset to the DV';
      add_option('offset', $offset, '', 'yes');
   } else {
      $offset = get_option('offset');
      // Increase offset
      $offset += $amount;
      // Update DB
      update_option('offset', $offset);
   }
}

/**
 * Reset the offset to the 0
 */
function reset_offset()
{
   if (option_exists("offset")) {
      update_option('offset', 0);
   } else {
      echo 'option does not exist';
   }
}

/**
 * Prints the offset
 */
function print_offset()
{
   echo get_option('offset');
}

/**
 * Get offset option
 */
function get_offset()
{
   return get_option('offset');
}



get_footer();
