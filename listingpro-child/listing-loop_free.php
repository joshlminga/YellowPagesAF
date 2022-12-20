	<?php
	$output = null;

	$favrt  =   listingpro_is_favourite_new(get_the_ID());

	global $listingpro_options;
	$lp_review_switch = $listingpro_options['lp_review_switch'];

	$lp_default_map_pin = $listingpro_options['lp_map_pin']['url'];
	if (empty($lp_default_map_pin)) {
		$lp_default_map_pin = get_template_directory() . '/assets/images/pins/pin.png';
	}

	if (!isset($postGridCount)) {
		$postGridCount = '0';
	}
	global $postGridCount;
	$postGridCount++;

	$listing_style = '';
	$listing_style = '1';//$listingpro_options['listing_style'];
	if (isset($_GET['list-style']) && !empty($_GET['list-style'])) {
		$listing_style = esc_html($_GET['list-style']);
	}
	if (is_front_page()) {
		$listing_style = 'col-md-4 col-sm-6';
		$postGridnumber = 3;
	} else {
		if ($listing_style == '1') {
			$listing_style = 'col-md-4 col-sm-6';
			$postGridnumber = 3;
		} elseif ($listing_style == '3' && !is_page()) {
			$listing_style = 'col-md-6 col-sm-12';
			$postGridnumber = 2;
		} elseif ($listing_style == '5') {
			$listing_style = 'col-md-12 col-sm-12';
			$postGridnumber = 2;
		} else {
			$listing_style = 'col-md-6 col-sm-6';
			$postGridnumber = 2;
		}
	}
	if (is_page_template('template-favourites.php')) {
		$listing_style = 'col-md-4 col-sm-6';
		$postGridnumber = 3;
	}
	$gAddress = listing_get_metabox('gAddress');
	lp_get_lat_long_from_address($gAddress, get_the_ID());
	$latitude = listing_get_metabox_by_ID('latitude', get_the_ID());
	$longitude = listing_get_metabox_by_ID('longitude', get_the_ID());
	$gAddress = listing_get_metabox('gAddress');
	$phone = listing_get_metabox('phone');
	if (!empty($latitude)) {
		$latitude = str_replace(",", ".", $latitude);
	}
	if (!empty($longitude)) {
		$longitude = str_replace(",", ".", $longitude);
	}
	$isfavouriteicon = listingpro_is_favourite_grids(get_the_ID(), $onlyicon = true);
	$isfavouritetext = listingpro_is_favourite_grids(get_the_ID(), $onlyicon = false);

	$adStatus = apply_filters('lp_get_ad_status', '', get_the_ID());

	$CHeckAd = '';
	$adClass = '';
	if ($adStatus == 'active') {
// 		$CHeckAd = '<span class="listing-pro">' . esc_html__('Ad', 'listingpro') . '</span>';
		$adClass = 'promoted';
	}
	$claimed_section = listing_get_metabox('claimed_section');

	$claim = '';
	$claimStatus = '';

	$deafaultFeatImg = lp_default_featured_image_listing();

	if ($claimed_section == 'claimed') {
		if (is_singular('listing')) {
			$claimStatus = esc_html__('Claimed', 'listingpro');
		}
		$claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="' . esc_html__('Claimed', 'listingpro') . '"><i class="fa fa-check"></i> ' . $claimStatus . '</span>';
	} elseif ($claimed_section == 'not_claimed') {
		$claim = '';
	}
	$listing_layout = $listingpro_options['listing_views'];
	if (is_author()) {
		$listing_layout =   $listingpro_options['my_listing_views'];
	}
	if (isset($GLOBALS['listing_layout_element']) && !empty($GLOBALS['listing_layout_element']) && $GLOBALS['listing_layout_element'] != '') {
		$listing_layout = $GLOBALS['listing_layout_element'];
	}
	$grid_view_element  =   '';
	if (isset($GLOBALS['grid_view_element'])) {
		$grid_view_element  =   $GLOBALS['grid_view_element'];
	}

	if (!is_front_page() && isset($listingpro_options['listing_views']) && !empty($listingpro_options['listing_views'])) {
		// WeddingPro
		if ($listingpro_options['listing_views'] == "wpro_gridStyle_1") {
			if (class_exists('WeddingPro')) {
				$is_ad = false;
				if ($adStatus == 'active') $is_ad = true;
				echo wpro_listing_grid(get_the_ID(), null, $is_ad, $listing_style, true);
				return;
			}
		}
		// BlackPro
		elseif ($listingpro_options['listing_views'] == "bpro_gridStyle_1") {
			if (class_exists('BlackPro')) {
				$is_ad = false;
				if ($adStatus == 'active') $is_ad = true;
				echo bpro_listing_grid(get_the_ID(), null, $is_ad, $listing_style, true);
				return;
			}
		}
		// PetPro
		elseif ($listingpro_options['listing_views'] == "ppro_gridStyle_1") {
			if (class_exists('PetFinder')) {
				$is_ad = false;
				if ($adStatus == 'active') $is_ad = true;
				echo ppro_listing_grid(get_the_ID(), null, $is_ad, $listing_style, true);
				return;
			}
		}
		// CbdPro
		elseif ($listingpro_options['listing_views'] == "cbdpro_gridStyle_1") {
			if (class_exists('CbdPro')) {
				$is_ad = false;
				if ($adStatus == 'active') $is_ad = true;
				echo cbdpro_listing_grid(get_the_ID(), null, $is_ad, $listing_style, true);
				return;
			}
		}
		// LawyerPro
		elseif ($listingpro_options['listing_views'] == "lpro_gridStyle_1") {
			if (class_exists('LawyerPro')) {
				$is_ad = false;
				if ($adStatus == 'active') $is_ad = true;
				echo lpro_listing_grid(get_the_ID(), null, $is_ad, $listing_style, true);
				return;
			}
		}
	}

	if (isset($GLOBALS['my_listing_views']) && $GLOBALS['my_listing_views'] != '') {
		$listing_layout =   $GLOBALS['my_listing_views'];
	}
	$listing_stylee = $listingpro_options['listing_style'];
	$featureImg = '';

	if (has_post_thumbnail()) {

		$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');

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

			$img_url = wp_get_attachment_image_src($IDs[0], 'listingpro-blog-grid');

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



	?>
		<div data-feaimg="<?php echo esc_url($featureImg); ?>" class="<?php echo esc_attr($listing_style); ?> <?php echo esc_attr($adClass); ?> lp-grid-box-contianer grid_view_s1 grid_view2 card1 lp-grid-box-contianer1" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>" data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>" data-lppinurl="<?php echo esc_url($lp_default_map_pin); ?>">
			<?php if (is_page_template('template-favourites.php')) { ?>
				<div class="remove-fav md-close" data-post-id="<?php echo get_the_ID(); ?>">
					<i class="fa fa-close"></i>
				</div>
			<?php } ?>
			<div class="lp-grid-box">
				<div class="lp-grid-box-thumb-container">
					<div class="lp-grid-box-thumb">
						<div class="show-img">
							<?php

							echo '<img src="' . $featureImg . '" alt="image">';

							?>
						</div>
						<div class="hide-img listingpro-list-thumb">
							<?php
							if (has_post_thumbnail()) {
								$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');
								if (!empty($image[0])) {
									echo "<img alt='image' src='" . $image[0] . "' />";
								} elseif (!empty($deafaultFeatImg)) {
									echo "<img alt='image' src='" . $deafaultFeatImg . "' />";
								} else {
									echo '<img src="' . esc_html__('https://via.placeholder.com/372x240', 'listingpro') . '" alt="image">';
								}
							} elseif (!empty($deafaultFeatImg)) {
								echo "<img alt='image' src='" . $deafaultFeatImg . "' />";
							} else {
								echo '<img src="' . esc_html__('https://via.placeholder.com/372x240', 'listingpro') . '" alt="image">';
							}
							?>
						</div>
					</div>
				</div>
				<div class="lp-grid-desc-container lp-border clearfix">
					<div class="lp-grid-box-description " style="min-height: 90px;">
						<div class="lp-grid-box-left pull-left">
							<h4 class="lp-h4">
								<?php echo wp_kses_post($CHeckAd); ?>
								<?php echo mb_substr(get_the_title(), 0, 30) ?>
								<?php echo wp_kses_post($claim); ?>
							</h4>
							<ul>
								<li>
									<?php
									$cats = get_the_terms(get_the_ID(), 'listing-category');
									if (!empty($cats)) {
										$catCount = 1;
										foreach ($cats as $cat) {
											if ($catCount == 1) {
												$category_image = "";// listing_get_tax_meta($cat->term_id, 'category', 'image');
												if (!empty($category_image)) {
													echo '<span class="cat-icon"><img class="icon icons8-Food" src="' . $category_image . '" alt="cat-icon"></span>';
												}
												$term_link = get_term_link($cat);
												echo '
																<a href="' . $term_link . '">
																	' . $cat->name . '
																</a>';
												$catCount++;
											}
										}
									}
									?>
								</li>
							</ul>
						</div>
					</div>
					<?php
					$openStatus = listingpro_check_time(get_the_ID());
					$cats = get_the_terms(get_the_ID(), 'location');
					if (!empty($openStatus) || !empty($cats)) {
					?>
						<div class="lp-grid-box-bottom">
							<div class="pull-left">
								<div class="show">
									<?php
									$countlocs = 1;
									$cats = get_the_terms(get_the_ID(), 'location');
									if (!empty($cats)) {
										echo '<span class="cat-icon">' . listingpro_icons('mapMarkerGrey') . '</span>';
										foreach ($cats as $cat) {
											if ($countlocs == 1) {
												$term_link = get_term_link($cat);
												echo '
																<a href="' . $term_link . '">
																	' . $cat->name . '
																</a>';
											}
											$countlocs++;
										}
									}

									?>
								</div>
								<?php if (!empty($gAddress)) { ?>
									<div class="hide">
										<span class="cat-icon">
											<?php echo listingpro_icons('mapMarkerGrey'); ?>
										</span>
										<span class="text gaddress"><?php echo mb_substr($gAddress, 0, 30); ?>...</span>
									</div>
								<?php } ?>
							</div>
							<?php
							$openStatus = listingpro_check_time(get_the_ID());
							if (!empty($openStatus)) {
								echo '
												<div class="pull-right">
													<a class="status-btn">';
								echo wp_kses_post($openStatus);
								echo ' 
													</a>
												</div>';
							}
							?>
							<div class="clearfix"></div>
						</div>

					<?php } ?>
				</div>
			</div>
		</div>
	<?php
	
	?>

	<?php //get_template_part('templates/preview'); 
	?>

	<?php
	if ($postGridCount % $postGridnumber == 0) {
		echo '<div class="clearfix lp-archive-clearfix"></div>';
	}
	?>