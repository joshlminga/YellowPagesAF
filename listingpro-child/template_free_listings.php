<?php 
/**
 * Template name: Free Listings
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */

$plan_id = 0;
if ( $post = get_page_by_path( 'free', OBJECT, 'price_plan' ) ){
    $plan_id = $post->ID;
}
	$type = 'listing';
	global $paged;
	// $args=array(
	// 	'post_type' => $type,
	// 	'post_status' => 'publish',
	// 	'paged'       => $paged,
	// 	'meta_query' => array(
	//         'relation' => 'AND',
	//         // 'relation' => 'OR',
	//         array(
	//             'key' => 'lp_' . strtolower(THEMENAME) . '_options',
	//             'value' => '"Plan_id"',
	//             'compare' => 'LIKE',
	//         ),
	//         array(
	//             'key' => 'lp_' . strtolower(THEMENAME) . '_options',
	//             'value' => '"'.$plan_id.'"',
	//             'compare' => 'LIKE',
	//         ),
	//     )
	// );
	$args = array(
		'post_type' => $type,
		'post_status' => 'publish',
        'posts_per_page' => -1,
		'paged'       => $paged,
        'meta_key' => 'plan_id',
        'meta_value' => array($plan_id,0),
        'meta_compare' => 'IN',
        'fields' => 'ids',
    );	
	$my_query = null;
	$my_query = new WP_Query($args);
get_header(); 
?>
	<section>
		<div class="container page-container margin-top-80">
			<div class="row lp-list-page-grid" id="content-grids" >
				<?php
					if( $my_query->have_posts() ) {
						while ($my_query->have_posts()) : $my_query->the_post(); 
						$post_id = get_the_ID();
						
				// $checkIfRecurriong = lp_listing_has_subscriptn($post_id);
				// $plan_id = listing_get_metabox_by_ID('Plan_id', $post_id);
				// $plan_title = get_the_title($plan_id);
				// $plan_price = listing_get_metabox_by_ID('plan_price', $listing_id);
				
				get_template_part( 'listing-loop_free' );
				?>
				
			<?php
				endwhile;
                echo listingpro_pagination();
            } else {
                ?>
                <div class="text-center margin-top-80 margin-bottom-100">
                    <h2><?php esc_html_e('No Results', 'listingpro'); ?></h2>
                    <p><?php esc_html_e('Sorry! There are no free listings at the moment.', 'listingpro'); ?></p>
                    <p><?php esc_html_e('Try checking another time.', 'listingpro'); ?>
                    </p>
                </div>
            <?php } ?>
				<div class="md-overlay"></div>
			</div>
		</div>
	</section>


<?php get_footer(); ?>