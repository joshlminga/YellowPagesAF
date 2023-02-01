<?php
	
	
/**
 * Adds new shortcode "ayp_video_embed" and registers it to
 * the Visual Composer plugin
 *
 */
if ( ! class_exists( 'ayp_shortcode' ) ) {

    class ayp_shortcode {

        /**
         * Main constructor
         */
        public function __construct() {

            // Registers the shortcode in WordPress
            add_shortcode( 'ayp_video_embed', __CLASS__ . '::output_video' );
            add_shortcode( 'ayp_categories_embed', __CLASS__ . '::output_categories' );
            add_shortcode( 'ayp_slider_thumbnail', __CLASS__ . '::output_slider_thumbnail' );
            add_shortcode( 'ayp_package_listings', __CLASS__ . '::output_package_listings' );

            // Map shortcode to WPBakery so you can access it in the builder
            if ( function_exists( 'vc_lean_map' ) ) {
                vc_lean_map( 'ayp_video_embed', __CLASS__ . '::map_video' );
                vc_lean_map( 'ayp_categories_embed', __CLASS__ . '::map_categories' );
                vc_lean_map( 'ayp_slider_thumbnail', __CLASS__ . '::map_slider_thumbnail' );
                vc_lean_map( 'ayp_package_listings', __CLASS__ . '::map_package_listings' );
            }

        }

        /**
         * Shortcode output_video
         */
        public static function output_package_listings( $atts, $content = null  ) {
           {

                ob_start();
                $el_id = 'listing_grids_by_id';

                if ($el_id == 'listing_grids' || $el_id == 'claimed_listings_grids' || $el_id == 'listing_grids_by_id' || $el_id == 'listing_grids_with_coupons' || $el_id == 'listing_options') :
                    echo '<div class="padding-top-40 padding-bottom-40 clearfix">';
                endif;
                // if (isset($atts['posts_ids'])) {
                //     $posts_ids = $atts['posts_ids'];
                // } else {
                    $posts_ids = '';
                // }
                if (isset($atts['listing_layout'])) {
                    $listing_layout = $atts['listing_layout'];
                } else {
                    $listing_layout = '';
                }
                if (isset($atts['listing_grid_style'])) {
                    $listing_grid_style = $atts['listing_grid_style'];
                } else {
                    $listing_grid_style = 'grid_view1';
                }
                if (isset($atts['listing_list_style'])) {
                    $listing_list_style = $atts['listing_list_style'];
                } else {
                    $listing_list_style = '';
                }
                if (isset($atts['grid3_button_text'])) {
                    $grid3_button_text = $atts['grid3_button_text'];
                } else {
                    $grid3_button_text = '';
                }
                if (isset($atts['grid3_button_link'])) {
                    $grid3_button_link = $atts['grid3_button_link'];
                } else {
                    $grid3_button_link = '';
                }
                if (isset($atts['number_posts'])) {
                    $number_posts = $atts['number_posts'];
                } else {
                    $number_posts = 3;
                }

                $output = null;

                $listing_package = 'free';
                if (isset($atts['listing_package'])) {
                    $listing_package = $atts['listing_package'];
                }
                $plan_id = 0;
                if ( $package_post = get_page_by_path( $listing_package, OBJECT, 'price_plan' ) ){
                    $plan_id = $package_post->ID;
                }

                $type = 'listing';
                $posts_ids_arr  =   array();
                if (strpos($posts_ids, ',') !== false) {
                    $posts_ids_arr  =   explode(',', $posts_ids);
                    $number_posts   = '-1';
                } elseif (!empty($posts_ids)) {
                    $posts_ids_arr[]    =   $posts_ids;
                    $number_posts   = '-1';
                }


                $listing_orderby = 'id';
                if (isset($atts['listing_orderby'])) {
                    $listing_orderby = $atts['listing_orderby'];
                }

                if ($el_id == 'listing_grids') {
                    $args = array(
                        'post_type' => $type,
                        'post_status' => 'publish',
                        'posts_per_page' => $number_posts,
                        'meta_key' => 'plan_id',
                        'meta_value' => $plan_id,
                        'meta_compare' => '=',
                        'fields' => 'ids',
                        'orderby' => $listing_orderby,
                    );
                    $argsFOrADS = array(
                        'orderby' => 'rand',
                        'post_type' => $type,
                        'post_status' => 'publish',
                        'posts_per_page' => $number_posts,
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key'     => 'campaign_status',
                                'value'   => array('active'),
                                'compare' => 'IN',
                            ),
                            array(
                                'key'     => 'lp_random_ads',
                                'value'   => array('active'),
                                'compare' => 'IN',
                            ),
                            array(
                                'key' => 'plan_id',
                                'value' => $plan_id,
                                'compare' => '=',
                            ),
                        ),
                    );
                } else {
                    $args = array(
                        'post_type' => $type,
                        'post_status' => 'publish',
                        'posts_per_page' => $number_posts,
                        'meta_key' => 'plan_id',
                        'meta_value' => $plan_id,
                        'meta_compare' => '=',
                        'fields' => 'ids',
                        'orderby' => $listing_orderby,
                    );
                }


                $listingcurrency = '';
                $listingprice = '';
                $addClassListing = '';

                $listing_query = null;
                if ($el_id == 'listing_grids') {
                    $listing_query = new WP_Query($argsFOrADS);
                    $found = $listing_query->found_posts;

                    if (($found == 0)) {
                        $listing_query = null;
                        $listing_query = new WP_Query($args);
                    }
                } elseif ($el_id == 'listing_options') {
                    if ($listing_multi_options == 'recent_view') {
                        $args = array(
                            'post_type'       => $type,
                            'post_status'     => 'publish',
                            'posts_per_page'  => $number_posts,
                            'meta_key' => 'plan_id',
                            'meta_value' => $plan_id,
                            'meta_compare' => '=',
                            'fields' => 'ids',
                            'order'           => 'DESC',

                        );
                    } elseif ($listing_multi_options == 'location_view') {

                        $args = array(
                            'post_type' => $type,
                            'post_status'     => 'publish',                            
                            'meta_key' => 'plan_id',
                            'meta_value' => $plan_id,
                            'meta_compare' => '=',
                            'fields' => 'ids',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'location',
                                    'field' => 'id',
                                    'terms' => $listing_loc,
                                    'include_children' => $include_children,
                                )
                            ),
                            'posts_per_page' => $number_posts,
                            'order'               => 'DESC'
                        );
                    } elseif ($listing_multi_options == 'cat_view') {

                        $args = array(
                            'post_type' => $type,
                            'post_status'     => 'publish',                            
                            'meta_key' => 'plan_id',
                            'meta_value' => $plan_id,
                            'meta_compare' => '=',
                            'fields' => 'ids',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'listing-category',
                                    'field' => 'id',
                                    'terms' => $listing_cat,
                                    'include_children' => $include_children,
                                )
                            ),
                            'posts_per_page' => $number_posts,
                            'order'               => 'DESC'
                        );
                    } elseif ($listing_multi_options == 'location_cat_view') {
                        if ($listing_cat != '') {
                            $tax_query[] = array(
                                'taxonomy' => 'listing-category',
                                'field' => 'id',
                                'terms' => $listing_cat,
                                'include_children' => $include_children,
                            );
                        }
                        if ($listing_loc != '') {
                            $tax_query[] = array(
                                'taxonomy' => 'location',
                                'field' => 'id',
                                'terms' => $listing_loc,
                                'include_children' => $include_children,
                            );
                        }
                        $tax_query['relation'] = $tax_relation;
                        $args = array(
                            'post_type' => $type,
                            'post_status'     => 'publish',
                            'tax_query' => $tax_query,
                            'posts_per_page' => $number_posts,
                            'order'               => 'DESC'
                        );
                    }

                    $listing_query = new WP_Query($args);
                } elseif ($el_id == 'listing_tabs') {

                    if ($listing_multi_options == 'location_view') {
                        $args = array(
                            'post_type' => $type,
                            'post_status'     => 'publish',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'location',
                                    'field' => 'id',
                                    'terms' => $listing_loc
                                )
                            ),
                            'posts_per_page' => $number_posts,
                            'order'               => 'DESC'
                        );
                    } elseif ($listing_multi_options == 'cat_view') {
                        $args = array(
                            'post_type' => $type,
                            'post_status'     => 'publish',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'listing-category',
                                    'field' => 'id',
                                    'terms' => $listing_cat,
                                    'include_children' => false
                                )
                            ),
                            'posts_per_page' => $number_posts,
                            'order'               => 'DESC'
                        );
                    }
                    $listing_query = new WP_Query($args);
                } elseif ($el_id == 'claimed_listings_grids') {
                    $args = array(
                        'post_type'       => $type,
                        'post_status'     => 'publish',
                        'posts_per_page' => $number_posts,
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key'     => 'lp_listingpro_options',
                                'value'   => 'not_claimed',
                                'compare' => 'NOT LIKE'
                            ),
                            array(
                                'key'     => 'lp_listingpro_options',
                                'value'   => 'Not claimed',
                                'compare' => 'NOT LIKE'
                            ),
                        )
                    );
                    $listing_query = new WP_Query($args);
                } elseif ($el_id == 'listing_grids_with_coupons') {
                    //new code 2.6.15
                    $postin = array();
                    $Cargs = array(
                        'post_type'       => $type,
                        'post_status'     => 'publish',
                        'posts_per_page' => -1,
                        'meta_key' => 'listing_discount_data',
                        'meta_compare' => 'EXISTS'
                    );
                    $coupon_query = new WP_Query($Cargs);
                    if ($coupon_query->have_posts()) {
                        while ($coupon_query->have_posts()) : $coupon_query->the_post();
                            $dissData = get_post_meta(get_the_ID(), 'listing_discount_data', true);
                            foreach ($dissData as $k => $disData) {
                                if (in_array(get_the_ID(), $postin)) continue;

                                $discount_data = $disData;
                                $cdatatime = date("Y-m-d h:i A");
                                $exETime = '12:00 AM';
                                if (!empty($discount_data['disTimeE'])) {
                                    $exETime = $discount_data['disTimeE'];
                                }
                                $exSTime = '12:00 AM';
                                if (!empty($discount_data['disTimeS'])) {
                                    $exSTime = $discount_data['disTimeS'];
                                }
                                $expiry_date  = coupon_timestamp($discount_data['disExpE'], $exETime);
                                $date_start   = coupon_timestamp($discount_data['disExpS'], $exSTime);

                                $time_now = strtotime($cdatatime);

                                if (((!empty($expiry_date) && $time_now < $expiry_date) && (!empty($date_start) && $time_now > $date_start)) || $discount_data['disExpE'] == 0) {
                                    $postin[] = get_the_ID();
                                } else {
                                    if ((!empty($expiry_date) && $time_now < $expiry_date) && (!empty($date_start) && $time_now < $date_start)) {
                                        $postin[] = get_the_ID();
                                    } else if (!empty($expiry_date)) {
                                        continue;
                                    } else {
                                        $postin[] = get_the_ID();
                                    }
                                }
                                continue;
                            }
                        endwhile;
                    }

                    if(is_array($postin) && empty($postin)){
                        $postin = array(0);
                    }
                    
                    $args = array(
                        'post_type'       => $type,
                        'post_status'     => 'publish',
                        'posts_per_page' => $number_posts,
                        'post__in'     => $postin,
                        'meta_key' => 'listing_discount_data',
                        'meta_compare' => 'EXISTS'
                    );
                    //end new code 2.6.15
                    $listing_query = new WP_Query($args);
                } else {
                    $listing_query = new WP_Query($args);
                }

                $post_count = 1;

                global $listingpro_options;
                $listing_views = $listingpro_options['listing_views'];

                $GLOBALS['listing_layout_element']  =   $listing_layout;
                if (!empty($GLOBALS['listing_layout_element']) || $GLOBALS['listing_layout_element'] != '') {
                    $addClassListing    =   'listing_' . $listing_layout;
                } else {
                    if ($listing_views == 'list_view') {
                        $addClassListing = 'listing_list_view';
                    } elseif ($listing_views == 'grid_view') {
                        $addClassListing = 'listing_grid_view';
                    } else {
                        $addClassListing = '';
                    }
                }
                $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];

                if ($listing_mobile_view == 'app_view2' && wp_is_mobile() && $el_id == 'listing_grids') {
                    if ($listing_query->have_posts()) {
                        echo '<div class="app-view-new-ads-slider">';
                        while ($listing_query->have_posts()) : $listing_query->the_post();

                            get_template_part('mobile/listing-loop-app-view-adds');

                        endwhile;
                        echo '</div>';
                    } else {
                        echo '<p>No Listings found</p>';
                    }
                } elseif (($listing_mobile_view == 'app_view2' || $listing_mobile_view == 'app_view') && wp_is_mobile() && $el_id == 'listing_tabs') {

                    $terms_Arr  =   $listing_cat;
                    $taxonomy   =   'listing-category';
                    if ($listing_multi_options == 'location_view') {
                        $terms_Arr  =   $listing_loc;
                        $taxonomy   =   'location';
                    }
                ?>
                    <?php
                    if (!$via_ajax && $el_id == 'listing_tabs') {
                    ?>
                        <div class="single-tabber2 listing-tabs-element">
                            <ul class="row list-style-none clearfix" data-tabs="tabs">
                                <?php
                                $terms_counter  =   1;
                                foreach ($terms_Arr as $item) {
                                    $active_tab =   '';
                                    if ($terms_counter == 1) {
                                        $active_tab =   'active';
                                    }
                                    $term_Arr   =   get_term_by('id', $item, $taxonomy);
                                    if ($term_Arr) {
                                        echo '<li class="' . $active_tab . '"><a href="#' . $term_Arr->slug . '" data-grid="' . $listing_grid_style . '" data-list="' . $listing_list_style . '" data-layout="' . $listing_layout . '" data-num="' . $number_posts . '" data-tax="' . $taxonomy . '" data-term="' . $item . '">' . $term_Arr->name . '</a></li>';
                                    }
                                    $terms_counter++;
                                }
                                ?>

                            </ul>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="detail-page2-tab-content app-view-listing-tabs">
                        <div class="tab-content">
                            <div class="tab-pane active" id="listing-tabs-inner-container">
                                <?php
                                if ($listing_query->have_posts()) {
                                    echo '<div class="app-view2-first-recent">';
                                    echo '<div class="app-view-new-ads-slider">';
                                    while ($listing_query->have_posts()) : $listing_query->the_post();
                                        get_template_part('mobile/listing-loop-app-view-adds');
                                    endwhile;
                                    echo '</div>';
                                    echo '</div>';
                                } else {
                                    echo '<p>No Listings found</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    if ($el_id == 'listing_tabs') {
                        $taxonomy   =   'listing-category';
                        if ($listing_multi_options == 'location_view') {
                            $terms_Arr  =  array();
                            if (!empty($listing_cat) && is_array($listing_cat)) {
                                $terms_Arr  =   explode(',', $listing_cat);
                            }
                            $terms_Arr  =   explode(',', $listing_loc);
                            $taxonomy   =   'location';
                        }
                    ?>
                        <?php
                        if (!$via_ajax && $el_id == 'listing_tabs') {
                        ?>
                            <div class="single-tabber2 listing-tabs-element">
                                <ul class="row list-style-none clearfix" data-tabs="tabs">
                                    <?php
                                    $terms_counter  =   1;
                                    if (!empty($terms_Arr) && (is_array($terms_Arr) || is_object($terms_Arr))) {
                                        foreach ($terms_Arr as $item) {
                                            $active_tab =   '';
                                            if ($terms_counter == 1) {
                                                $active_tab =   'active';
                                            }
                                            $term_Arr   =   get_term_by('id', $item, $taxonomy);
                                            if ($term_Arr) {
                                                echo '<li class="' . $active_tab . '"><a href="#' . $term_Arr->slug . '" data-grid="' . $listing_grid_style . '" data-list="' . $listing_list_style . '" data-layout="' . $listing_layout . '" data-num="' . $number_posts . '" data-tax="' . $taxonomy . '" data-term="' . $item . '">' . $term_Arr->name . '</a></li>';
                                            }
                                            $terms_counter++;
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="detail-page2-tab-content">
                            <div class="tab-content">
                                <div class="tab-pane active" id="listing-tabs-inner-container">


                            <?php
                        }
                        if ($listing_grid_style == 'grid_view5' && $listing_layout == 'grid_view') {
                            $GLOBALS['grid_col_class']  =   3;
                            $GLOBALS['trending_el']  =   true;

                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                if (($listing_layout == 'grid_view') && ($listing_views == 'lp-list-view-compact' || $listing_views == 'grid_view' || $listing_views == 'grid_view_v3' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                    echo '<div class="map-view-list-container2">';
                                } else {
                                    echo '<div class="map-view-list-container">';
                                }
                                if ($listing_query->have_posts()) {

                                    while ($listing_query->have_posts()) : $listing_query->the_post();
                                        get_template_part('mobile/listing-loop-app-view');
                                    endwhile;
                                } else {
                                    echo '</p>No Listings Found</p>';
                                }
                                if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                    echo '</div>';
                                } else {

                                    echo '</div>';
                                }
                            } else {
                                if ($listing_query->have_posts()) {
                                    echo '<div class="lp-listings">';
                                    echo '<div class="row listing-slider">';

                                    while ($listing_query->have_posts()) : $listing_query->the_post();
                                        get_template_part('templates/loop-grid-view');

                                    endwhile;
                                    echo '</div>';
                                    echo '</div>';
                                } else {
                                    echo '</p>No Listings Found</p>';
                                }
                            }
                        }
                        if ($listing_grid_style == 'grid_view3' && $listing_layout == 'grid_view') {
                            $GLOBALS['grid_col_class']  =   3;
                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                if (($listing_layout == 'grid_view') && ($listing_views == 'lp-list-view-compact' || $listing_views == 'grid_view' || $listing_views == 'grid_view_v3' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                    echo '<div class="map-view-list-container2">';
                                } else {
                                    echo '<div class="map-view-list-container">';
                                }
                                if ($listing_query->have_posts()) {
                                    while ($listing_query->have_posts()) : $listing_query->the_post();

                                        get_template_part('mobile/listing-loop-app-view');

                                    endwhile;
                                    wp_reset_postdata();
                                } else {
                                    echo '</p>No Listings Found</p>';
                                }
                                if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                    echo '</div>';
                                } else {

                                    echo '</div>';
                                }
                            } else {

                                $output .=  '<div class="lp-section-content-container listingcampaings">';
                                $output .=  '    <div class="lp-listings grid-style">';
                                $output .=  '        <div class="row">';
                                if ($listing_query->have_posts()) {
                                    $home_grid_counter  =   0;
                                    ob_start();
                                    while ($listing_query->have_posts()) : $listing_query->the_post();
                                        $home_grid_counter++;
                                        get_template_part('templates/loop-grid-view');
                                        if ($home_grid_counter % 3 == 0) {
                                            echo '<div class="clearfix"></div>';
                                        }

                                    endwhile;
                                    wp_reset_postdata();
                                    $loop_temp  =   ob_get_contents();
                                    ob_end_clean();
                                    $output .=  $loop_temp;
                                } else {
                                    echo '</p>No Listings Found</p>';
                                }
                                if (!empty($grid3_button_text) && isset($grid3_button_text)) {
                                    $btn_href   =   '';
                                    if (!empty($grid3_button_link)) {
                                        $btn_href   =   ' href="' . $grid3_button_link . '"';
                                    }
                                    $output .=  '    <div class="clearfix"></div><div class="more-listings"><a' . $btn_href . '>' . $grid3_button_text . '</a></div>';
                                }
                                $output .=  '        </div>';
                                $output .=  '    </div>';
                                $output .=  '</div>';
                            }
                        } else {
                            if ($listing_layout == 'list_view' && $listing_list_style == 'list_view_v2') {

                                if ($listing_query->have_posts()) {
                                    if ($listing_mobile_view != 'app_view' || !wp_is_mobile()) {
                                        $campaign_layout = 'list';
                                        echo '<div class="lp-section-content-container homepage-listing-view2-element"> <div class="lp-listings list-style active-view">
                                                <div class="search-filter-response">
                                                    <div class="lp-listings-inner-wrap">';
                                    }

                                    while ($listing_query->have_posts()) : $listing_query->the_post();

                                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                            get_template_part('mobile/listing-loop-app-view');
                                        } else {
                                            get_template_part('templates/loop-list-view');
                                        }
                                    endwhile;
                                    if ($listing_mobile_view != 'app_view' || !wp_is_mobile()) {
                                        echo '</div></div></div></div>';
                                    }
                                } else {
                                    echo '<p style="padding: 40px;">No Listings Found</p>';
                                }
                            } else {


                            $output .= '
                <div class="listing-simple ' . $addClassListing . ' listingcampaings">
                    <div class="lp-list-page-grid row" id="content-grids" >';
                                    if ($listing_grid_style == 'grid_view1') {
                                        if ($listing_query->have_posts()) {
                                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                                if (($listing_layout == 'grid_view') && ($listing_views == 'lp-list-view-compact' || $listing_views == 'grid_view' || $listing_views == 'grid_view_v3' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                                    echo '<div class="map-view-list-container2">';
                                                } else {
                                                    echo '<div class="map-view-list-containerlist">';
                                                }
                                            }
                                            while ($listing_query->have_posts()) : $listing_query->the_post();

                                                if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                                    get_template_part('mobile/listing-loop-app-view');
                                                } else {
                                                    get_template_part('listing-loop-ayp');
                                                }

                                            endwhile;
                                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                                if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                                    echo '</div>';
                                                } else {

                                                    echo '</div>';
                                                }
                                            }
                                            $output .= '<div class="md-overlay"></div>';
                                        } else {
                                            echo '</p>No Listings Found</p>';
                                        }
                                    } elseif ($listing_grid_style == 'grid_view2') {
                                        if ($listing_query->have_posts()) {
                                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                                if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                                    echo '<div class="map-view-list-container2">';
                                                } else {

                                                    echo '<div class="map-view-list-containerlist">';
                                                }
                                            }
                                            while ($listing_query->have_posts()) : $listing_query->the_post();

                                                if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                                    get_template_part('mobile/listing-loop-app-view');
                                                } else {
                                                    get_template_part('templates/loop/loop2');
                                                }

                                            //$output .= ob_get_contents();

                                            endwhile;
                                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                                if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                                    echo '</div>';
                                                } else {

                                                    echo '</div>';
                                                }
                                            }
                                            $output .= '<div class="md-overlay"></div>';
                                        } else {
                                            echo '</p>No Listings Found</p>';
                                        }
                                    } elseif ($listing_grid_style == 'grid_view4' || $listing_grid_style == 'grid_view6') {
                                        if ($listing_grid_style == 'grid_view4') {
                                            $GLOBALS['grid_view_element'] = 'grid_view4';
                                        } elseif ($listing_grid_style == 'grid_view6') {

                                            $GLOBALS['grid_view_element'] = 'grid_view6';
                                        }

                                        if ($listing_query->have_posts()) {
                                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {

                                                if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                                    echo '<div class="map-view-list-container2">';
                                                } else {

                                                    echo '<div class="map-view-list-containerlist">';
                                                }
                                            }
                                            while ($listing_query->have_posts()) : $listing_query->the_post();


                                                if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {

                                                    get_template_part('mobile/listing-loop-app-view');
                                                } else {
                                                    if ($listing_grid_style == 'grid_view4') {
                                                        get_template_part('listing-loop-ayp');
                                                    } elseif ($listing_grid_style == 'grid_view6') {

                                                        get_template_part('templates/loop/loop3');
                                                    }
                                                }
                                            endwhile;
                                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                                if (($listing_layout == 'grid_view') && ($listing_views == 'grid_view' || $listing_views == 'grid_view2' || $listing_views == 'grid_view3' || $listing_views == 'list_view' || $listing_views == 'list_view3' || $listing_views == 'grid_view_v2' || $listing_views == 'list_view_v2')) {
                                                    echo '</div>';
                                                } else {
                                                    echo '</div>';
                                                }
                                            }
                                            $output .= '<div class="md-overlay"></div>';
                                        } else {
                                            echo '</p>No Listings Found</p>';
                                        }
                                    }
                                    wp_reset_postdata();
                                    global $postGridCount;
                                    $postGridCount = '0';
                                    $output .= '
                    </div>
                </div>';
                        }
                    }
                    if ($el_id == 'listing_tabs') {
                        echo '</div>
                    </div>
                </div>';
                    }
                }
                if ($el_id == 'listing_grids' || $el_id == 'claimed_listings_grids' || $el_id == 'listing_grids_by_id' || $el_id == 'listing_grids_with_coupons' || $el_id == 'listing_options') :
                    echo '</div>';
                endif;
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();

                return $output;
            }
        
        }
        /**
         * Shortcode output_video
         */
        public static function output_video( $atts, $content = null  ) {
           extract( shortcode_atts( array(
              'video_url' => '',
              'screen_image' => '',
           ), $atts ) );
            $screenImage=null;
            if ( $screen_image ) {
                if( is_array( $screen_image ) )
                {
                    $screen_image   =   $screen_image['id'];
                }
                $imgurl = wp_get_attachment_image_src( $screen_image, 'full');
                if(!empty($imgurl)){
                    $screenImage = '<img alt="image" src="'. $imgurl[0] .'">';
                }
            }
           $html = '<div class="testimonial lp-section-content-container row">';
           $html .= '<div class="col-12">
                        <div class="video-thumb">
                                '.$screenImage.'
                                <a href="#test-popup" class="overlay-video-thumb popup-video">
                                    <i class="fa fa-play-circle-o"></i>
                                </a>
                                <div id="test-popup" class="white-popup mfp-hide">
                                     <video width="100%" height="100%" controls>
                                      <source src="'.esc_url($video_url).'" type="video/mp4">
                                      Your browser does not support the video tag.
                                    </video>
                                </div>
                        </div><!-- ../video-thumb -->
                    </div>';
            $html .= '</div>';
        
           return $html;
        }

        /**
         * Shortcode output_video
         */
        public static function output_categories( $atts, $content = null  ) {
           extract(shortcode_atts(array(
                'category_ids'   => '',
                'catstyles'    => 'cat_grid_abstracted',
                'cat_abstracted_2_btn_text'    => '',
                'cat_abstracted_2_btn_link'    => '',
                'display_sub_cat_box2'    => 'show',
                'cat3_button_link' => '',
                'cat3_button_text'   => 'Explore More',
                'display_main_cats' => ''

            ), $atts));

            if( is_array( $category_ids ) )
            {
                $category_ids   =   implode( ',', $category_ids );
            }

            $has_child_cats ='';
            require_once (THEME_PATH . "/include/aq_resizer.php");
            $output = null;
            global $listingpro_options;
            $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];

            if($listing_mobile_view == 'app_view' && wp_is_mobile() ){
                $output .= '<div class="lp-section-content-container lp-location-slider clearfix">';

                $listingCategories = $category_ids;
                $ucat = array(
                    'post_type' => 'listing',
                    'hide_empty' => false,
                    'orderby' => 'count',
                    'order' => 'ASC',
                    'include'=> $listingCategories
                );
                $allLocations = get_terms( 'listing-category',$ucat);
                $grid = 0;
                foreach($allLocations as $category) {
                    $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                    $category_image = listing_get_tax_meta($category->term_id,'category','banner');
                    $catImg = '';

                    $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                    if( !empty($cat_image_id) ){
                        $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_location270_400', true );
                        $catImg = $thumbnail_url[0];
                    }else{
                        $imgurl = aq_resize( $category_image, '270', '400', true, true, true);
                        if(empty($imgurl) ){
                            $catImg = 'https://via.placeholder.com/372x240';
                        }
                        else{
                            $catImg = $imgurl;
                        }
                    }

                    $output .= '
                
                    <div class="slider-for-category-container">
                        <div class="">
                            <div class="city-girds2">
                                <div class="city-thumb2">
                                    <img alt="image" src="'. $catImg.'" />
                                    <div class="category-style3-title-outer">
                                        <h3 class="lp-h3">
                                            <a href="'.esc_url( get_term_link( $category->term_id , 'listing-category')).'">'.esc_attr($category->name).'</a>
                                        </h3>
                                    </div>
                                    <a href="'.esc_url( get_term_link( $category )).'" class="overlay-link" style="background-color: #00000082;"></a>
                                    <div class="location-overlay"></div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                ';
                    $grid++;

                }
                $output .= '</div>';
            }else{
                if($catstyles == 'cat_slider_style') {
                    $output .= '<div class="lp-section-content-container clearfix listing-category-slider4">';
                }else{
                    $output .= '<div class="lp-section-content-container row">';
                }
                $listingCategories = $category_ids;
                $ucat = array(
                    'post_type' => 'listing',
                    'hide_empty' => false,
                    'orderby' => 'count',
                    'order' => 'ASC',
                    'include'=> $listingCategories
                );
                $allLocations = get_terms( 'listing-category',$ucat);

                $grid = 0;
                foreach($allLocations as $category) {
                    $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                    $category_image = listing_get_tax_meta($category->term_id,'category','banner');
                    $catImg = '';
                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','listing-category', $category->term_id);

                    
                    $gridStyle = 'col-md-3 col-sm-3 col-xs-12';

                    $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                    if( !empty($cat_image_id) ){
                        $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_cats270_213', true );
                        $catImg = $thumbnail_url[0];
                    }else{
                        $imgurl = aq_resize( $category_image, '270', '213', true, true, true);
                        if(empty($imgurl) ){
                            $catImg = 'https://via.placeholder.com/270x213';
                        }
                        else{
                            $catImg = $imgurl;
                        }
                    }

                    $output .= '
                    <div class="'.$gridStyle.'">
                        <div class="city-girds lp-border-radius-8 city-girds4">
                            <div class="city-thumb">
                                <img alt="image" src="'. $catImg.'" />
                                
                            </div>
                            <div class="city-title text-center category-style3-title-outer">
                                <h3 class="lp-h3">
                                    <a href="'.esc_url( get_term_link( $category->term_id , 'listing-category')).'">'.esc_attr($category->name).'</a>
                                </h3>
                                <label class="lp-listing-quantity">'.esc_attr($totalListinginLoc).' '.esc_html__('Listings', 'listingpro-plugin').'</label>'
                            ;

                            $sub = get_term_children( $category->term_id, 'listing-category' );
                            if(!empty($sub)){
                                $output .= '<ul class="clearfix text-center sub-category-outer lp-listing-quantity">';
                                $counter = 1;
                                foreach ( $sub as $subID ) {
                                    if($counter == 1){

                                        $categoryTerm = get_term_by( 'id', $subID, 'listing-category' );

                                        $output .= '<li><p><a href="'.esc_url( get_term_link( $categoryTerm->term_id , 'listing-category')).'">'.$categoryTerm->name.'</a></p></li>';
                                    }
                                    $counter ++;
                                }
                                $output .= '</ul>';


                            }

                            $output .=' 
                            </div>
                            <a href="'.esc_url( get_term_link( $category )).'" class="overlay-link" style="background-color: #00000082;"></a>
                        </div>
                    </div>';
                    $grid++;
                }


                if($catstyles == 'cat_slider_style') {
                    $output .= '</div>';
                }else{
                    $output .= '</div>';
                }

            }
            if($catstyles == 'cat_slider_style' && !empty($cat3_button_link) && !empty( $cat3_button_text ) ) {
                $output .= '<div class="lp-explore-more-text text-center">
                <a href="'.$cat3_button_link.'" class="lp-quote-submit-btn">'.$cat3_button_text.'</a>
            </div>';
            }

            return $output;
        }

        public static function output_slider_thumbnail( $atts ) {
            // extract( shortcode_atts( array(
            //   'images' => '',
            //   'show_thumbnails' => true,
            //   'listing_package' => '',
            //   'listing_orderby' => '',
            //   'number_posts' => '',
            // ), $atts ) );
            $images = '';
            if (isset($atts['images'])) {
                $images = $atts['images'];
            }
            $show_thumbnails = false;
            if (isset($atts['show_thumbnails'])) {
                $show_thumbnails = $atts['show_thumbnails'];
            }
            $listing_package = 'free';
            if (isset($atts['listing_package'])) {
                $listing_package = $atts['listing_package'];
            }
            $plan_id = 0;
            if ( $post = get_page_by_path( $listing_package, OBJECT, 'price_plan' ) ){
                $plan_id = $post->ID;
            }            
            $listing_orderby = 'id';
            if (isset($atts['listing_orderby'])) {
                $listing_orderby = $atts['listing_orderby'];
            }
            $number_posts = 15;
            if (isset($atts['number_posts'])) {
                $number_posts = $atts['number_posts'];
            }

            $deafaultFeatImg = lp_default_featured_image_listing();

            $args = array(
                'post_type' => 'listing',
                'post_status' => 'publish',
                'posts_per_page' => $number_posts,
                'meta_key' => 'plan_id',
                'meta_value' => $plan_id,
                'meta_compare' => '=',
                'fields' => 'ids',
                'orderby' => $listing_orderby,
            );  
            $my_query = null;
            $my_query = new WP_Query($args);

            $images = explode( ',', $images );

            $output = '<div id="primary-slider" class="splide">';
            $output .= '    <div class="splide__track">';
            $output .= '        <ul class="splide__list">';

                    if( $my_query->have_posts() ) {
                        while ($my_query->have_posts()) : $my_query->the_post();

                            $featureImg = '';
                                $metabox = get_post_meta(get_the_ID(), 'lp_' . strtolower(THEMENAME) . '_options', true);
                                $phone = isset($metabox['phone']) ? $metabox['phone'] : "";
                                $website = isset($metabox['website']) ? $metabox['website'] : "";
                                $website = ($website!='') ? $website : get_permalink();

                                if (has_post_thumbnail()) {

                                    $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');

                                    if (!empty($image[0])) {
                                        $featureImg = $image[0];
                                    } elseif (!empty($deafaultFeatImg)) {

                                        $featureImg = $deafaultFeatImg;
                                    } else {
                                        $featureImg = 'https://via.placeholder.com/372x240';
                                    }

                                    //$featureImg = $image[0];
                                } else if ($listingpro_options['lp_def_featured_image_from_gallery'] == 'enable') {

                                    //  echo "yes";
                                    $IDs = get_post_meta(get_the_ID(), 'gallery_image_ids', true);

                                    $IDs = explode(',', $IDs);

                                    if (is_array($IDs)) {
                                        shuffle($IDs);

                                        $img_url = wp_get_attachment_image_src($IDs[0], 'full');

                                        $imgurl = $img_url[0];
                                        if (!empty($imgurl)) {
                                            $featureImg = $imgurl;
                                        } elseif (!empty($deafaultFeatImg)) {

                                            $featureImg = $deafaultFeatImg;
                                        } else {
                                            $featureImg = 'https://via.placeholder.com/372x240';
                                        }
                                    }
                                } elseif (!empty($deafaultFeatImg)) {

                                    $featureImg = $deafaultFeatImg;
                                } else {

                                    $featureImg = 'https://via.placeholder.com/372x240';
                                }

                        $output .= '<li class="splide__slide">';
                        $output .= '    <center>';
                        $output .= ($website!="")?'    <a href="'.esc_url($website).'">':'';
                        $output .= '    <img src="' . $featureImg . '" style="display: block;position: absolute;top: 50%;left: 50%;min-height: 100%;max-height: 100%;transform: translate(-50%, -50%);">';
                        $output .= ($website!="")?'    </a>':'';
                        $output .= '    </center>';
                        $output .= '    <div class="overlay" style = "position: absolute;bottom: 0;left: 0;right: 0;padding: 10px;background-color: rgba(0, 0, 0, 0.5);color: white;font-size: 18px;text-align: center;">'.mb_substr(get_the_title(), 0, 40).'</div>';
                        $output .= '</li>';

                        endwhile;
                    }
            $output .= '        </ul>';
            $output .= '    </div>';
            $output .= '</div>';

            if ($show_thumbnails) {
                $output .= '<div id="secondary-slider" class="splide">';
                $output .= '    <div class="splide__track">';
                $output .= '        <ul class="splide__list">';
                    if( $my_query->have_posts() ) {
                        while ($my_query->have_posts()) : $my_query->the_post();
                            $featureImg = '';

                                if (has_post_thumbnail()) {

                                    $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');

                                    if (!empty($image[0])) {
                                        $featureImg = $image[0];
                                    } elseif (!empty($deafaultFeatImg)) {

                                        $featureImg = $deafaultFeatImg;
                                    } else {
                                        $featureImg = 'https://via.placeholder.com/372x240';
                                    }

                                    //$featureImg = $image[0];
                                } else if ($listingpro_options['lp_def_featured_image_from_gallery'] == 'enable') {

                                    //  echo "yes";
                                    $IDs = get_post_meta(get_the_ID(), 'gallery_image_ids', true);

                                    $IDs = explode(',', $IDs);

                                    if (is_array($IDs)) {
                                        shuffle($IDs);

                                        $img_url = wp_get_attachment_image_src($IDs[0], 'full');

                                        $imgurl = $img_url[0];
                                        if (!empty($imgurl)) {
                                            $featureImg = $imgurl;
                                        } elseif (!empty($deafaultFeatImg)) {

                                            $featureImg = $deafaultFeatImg;
                                        } else {
                                            $featureImg = 'https://via.placeholder.com/372x240';
                                        }
                                    }
                                } elseif (!empty($deafaultFeatImg)) {

                                    $featureImg = $deafaultFeatImg;
                                } else {

                                    $featureImg = 'https://via.placeholder.com/372x240';
                                }
                    // $image_url = wp_get_attachment_image_src( $image, 'full' );
                    $output .= '        <li class="splide__slide" role="button" tabindex="0">';
                    $output .= '            <img src="' . $featureImg . '" alt="Thumbnail 02">';
                    $output .= '        </li>';

                    endwhile;
                }
                $output .= '        </ul>';
                $output .= '    </div>';
                $output .= '</div>';
            }
            return $output;
        }

        /**
         * Map shortcode to WPBakery
         *
         * This is an array of all your settings which become the shortcode attributes ($atts)
         * for the output. See the link below for a description of all available parameters.
         *
         * @since 1.0.0
         * @link  https://kb.wpbakery.com/docs/inner-api/vc_map/
         */
        public static function map_package_listings() {
            return array(
                "name"                      => __("AYP Listings by Package"),
                "base"                      => 'ayp_listing_by_package',
                "class" => "",
                "icon" => get_template_directory_uri() . "/assets/images/favicon.png",
                "params"                    => array(
                    array(
                        "type"        => "dropdown",
                        "class"       => "",
                        "heading"     => esc_html__("Listing Layout", "js_composer"),
                        "param_name"  => "listing_layout",
                        'value' => array(
                            esc_html__('List View', 'js_composer') => 'list_view',
                            esc_html__('Grid View', 'js_composer') => 'grid_view',
                        ),
                        'save_always' => true,
                        "description" => "Select lists layout"
                    ),
                    array(
                        "type"        => "dropdown",
                        "class"       => "",
                        "heading"     => esc_html__("Styles", "js_composer"),
                        "param_name"  => "listing_grid_style",
                        'value' => array(
                            esc_html__('Grid Style 1', 'js_composer') => 'grid_view1',
                            esc_html__('Grid Style 2', 'js_composer') => 'grid_view2',
                            esc_html__('Grid Style 3', 'js_composer') => 'grid_view3',
                            esc_html__('Grid Style 4', 'js_composer') => 'grid_view4',
                            esc_html__('Grid Style 5', 'js_composer') => 'grid_view5',
                            esc_html__('Grid Style 6', 'js_composer') => 'grid_view6',

                        ),
                        "dependency" => array(
                            "element" => "listing_layout",
                            "value" => "grid_view"
                        ),
                        'save_always' => true,
                    ),
                    array(
                        "type"        => "dropdown",
                        "class"       => "",
                        "heading"     => esc_html__("Styles", "js_composer"),
                        "param_name"  => "listing_list_style",
                        'value' => array(
                            esc_html__('List Style 1', 'js_composer') => 'listing_views_1',
                            esc_html__('List Style 2', 'js_composer') => 'list_view_v2',
                        ),
                        "dependency" => array(
                            "element" => "listing_layout",
                            "value" => "list_view"
                        ),
                        'save_always' => true,
                    ),
                    array(
                        "type" => "textfield",
                        "class" => "",
                        "heading" => __("Button Text", "js_composer"),
                        "param_name" => "grid3_button_text",
                        "description" => __("Button for grid style 3, Leave empty to hide.", "js_composer"),
                        "dependency" => array(
                            "element" => "listing_grid_style",
                            "value" => "grid_view3"
                        ),
                    ),
                    array(
                        "type" => "textfield",
                        "class" => "",
                        "heading" => __("Button Link", "js_composer"),
                        "param_name" => "grid3_button_link",
                        "description" => __("Button link for grid style 3", "js_composer"),
                        "dependency" => array(
                            "element" => "listing_grid_style",
                            "value" => "grid_view3"
                        ),
                    ),
                    array(
                        "type"        => "dropdown",
                        "class"       => "",
                        "heading"     => esc_html__("Listing Package", "js_composer"),
                        "param_name"  => "listing_package",
                        'value' => array(
                            esc_html__('Free', 'js_composer') => 'free',
                            esc_html__('Gold', 'js_composer') => 'gold',
                            esc_html__('Diamond', 'js_composer') => 'diamond',
                            esc_html__('Platinum', 'js_composer') => 'platinum',
                        ),
                        "description" => __("Select Package", "js_composer"),
                    ),
                    array(
                        "type"        => "dropdown",
                        "class"       => "",
                        "heading"     => esc_html__("Listing Order By", "js_composer"),
                        "param_name"  => "listing_orderby",
                        'value' => array(
                            esc_html__('id', 'js_composer') => 'id',
                            esc_html__('Random', 'js_composer') => 'rand',
                        ),
                        "description" => __("Select Order By", "js_composer"),
                    ), 
                    array(
                        "type" => "textfield",
                        "class" => "",
                        "heading" => __("Number of Results", "js_composer"),
                        "param_name" => "number_posts",
                        "description" => __("Number of results (-1 for unlimited)", "js_composer"),
                    ),    

                ),
            );
        }
        public static function map_video() {
            return array(
                   "name" => __("Custom Video"),
                   "base" => "custom_video",
                   "class" => "",
                   "icon" => get_template_directory_uri() . "/assets/images/favicon.png",
                   "category" => __("Content"),
                   "params" => array(
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => __("Video URL"),
                            "param_name" => "video_url",
                            "value" => "",
                            "description" => __("Enter the URL of the video you want to embed.")
                        ),
                        array(
                            "type"        => "attach_image",
                            "class"       => "",
                            "heading"     => __("Video preview Image", "js_composer"),
                            "param_name"  => "screen_image",
                            "value"       => "",
                            "description" => "Please upload preview Image Size(580x386)"
                        ),
                   )
                );
        }
        public static function map_categories() {
            return array(
                   "name" => __("Custom Categories Grid"),
                   "base" => "custom_categories",
                   "class" => "",
                   "icon" => get_template_directory_uri() . "/assets/images/favicon.png",
                   "category" => __("Content"),
                   "params" => array(
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => __("Video URL"),
                            "param_name" => "video_url",
                            "value" => "",
                            "description" => __("Enter the URL of the video you want to embed.")
                        ),
                       array(
                            'type'       => 'dropdown',
                            'heading'    => esc_html__( 'Show Heading?', 'locale' ),
                            'param_name' => 'show_heading',
                            'value'      => array(
                                esc_html__( 'No', 'locale' )  => 'no',
                                esc_html__( 'Yes', 'locale' ) => 'yes',
                            ),
                        ),
                        array(
                            'type'       => 'textfield',
                            'heading'    => esc_html__( 'Heading', 'locale' ),
                            'param_name' => 'heading',
                            'dependency' => array( 'element' => 'show_heading', 'value' => 'yes' ),
                        ),
                        array(
                            'type'       => 'textarea_html',
                            'heading'    => esc_html__( 'Custom Text', 'locale' ),
                            'param_name' => 'content',
                        ),
                   )
                );
        }
        public static function map_slider_thumbnail() {
            return array(
                  "name" => __( "Slider with Thumbnails", "my-text-domain" ),
                  "base" => "slider_with_thumbnail",
                  "class" => "",
                  "category" => __( "Content", "my-text-domain"),
                  "params" => array(
                    array(
                        "type"        => "dropdown",
                        "class"       => "",
                        "heading"     => esc_html__("Listing Package", "js_composer"),
                        "param_name"  => "listing_package",
                        'value' => array(
                            esc_html__('Free', 'js_composer') => 'free',
                            esc_html__('Gold', 'js_composer') => 'gold',
                            esc_html__('Diamond', 'js_composer') => 'diamond',
                            esc_html__('Platinum', 'js_composer') => 'platinum',
                        ),
                        "description" => __("Select Package", "js_composer"),
                    ),
                    array(
                        "type" => "textfield",
                        "class" => "",
                        "heading" => __("Number of Results", "js_composer"),
                        "param_name" => "number_posts",
                        "description" => __("Number of results (-1 for unlimited)", "js_composer"),
                    ), 
                     array(
                        "type" => "checkbox",
                        "class" => "",
                        "heading" => __( "Show Thumbnails", "my-text-domain" ),
                        "param_name" => "show_thumbnails",
                        "value" => array( __( "Yes", "my-text-domain" ) => true ),
                        "description" => __( "Enable to show thumbnails below the slider.", "my-text-domain" )
                     ),
                    array(
                        "type"        => "dropdown",
                        "class"       => "",
                        "heading"     => esc_html__("Listing Order By", "js_composer"),
                        "param_name"  => "listing_orderby",
                        'value' => array(
                            esc_html__('id', 'js_composer') => 'id',
                            esc_html__('Random', 'js_composer') => 'rand',
                        ),
                        "description" => __("Select Order By", "js_composer"),
                    ), 
                )
            );
        }
    }
}
new ayp_shortcode;
