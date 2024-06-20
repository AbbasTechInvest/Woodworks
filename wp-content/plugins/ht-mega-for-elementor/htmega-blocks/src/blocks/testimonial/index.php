<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

extract($settings);
$uniqClass = "htmega-block-" . $blockUniqId;
$classes = [$uniqClass, "htmega-testimonials", "htmega-testimonials-" . $testimonialStyle];
$slider && $classes[] = "htmega-slick-slider htmega-testimonials-carousel";
if (!$slider) {
	$classes[] = "htmega-grid";
	$columns['desktop'] && $classes[] = "htmega-grid-col-" . $columns['desktop'];
	$columns['tablet'] && $classes[] = "htmega-grid-col-tablet-" . $columns['tablet'];
	$columns['mobile'] && $classes[] = "htmega-grid-col-mobile-" . $columns['mobile'];
}
;
$classs = implode(" ", $classes);

$default_image_url = HTMEGA_BLOCK_URL . '/src/assets/images/testimonial.jpg';
$quote_icon = HTMEGA_BLOCK_PATH . '/src/assets/images/quote.svg';

// Slider Options
$slider_settings = [];
if ($slider) {
	$is_rtl = is_rtl();
	$direction = $is_rtl ? 'rtl' : 'ltr';
	$slider_settings = [
		'arrows' => $arrows,
		'dots' => $dots,
		'autoplay' => $autoplay,
		'autoplay_speed' => absint($autoplaySpeed),
		'animation_speed' => absint($animationSpeed),
		'pause_on_hover' => $pauseOnHover,
		'rtl' => $is_rtl,
		'slidesToShow' => $sliderItems,
		'slidesToScroll' => $scrollColumns,
		'infinite' => true,
		'fade' => false,
		'responsive' => [
			[
				'breakpoint' => $tabletWidth,
				'settings' => [
					'slidesToShow' => $tabletDisplayColumns,
					'slidesToScroll' => $tabletScrollColumns
				]
			],
			[
				'breakpoint' => $mobileWidth,
				'settings' => [
					'slidesToShow' => $mobileDisplayColumns,
					'slidesToScroll' => $mobileScrollColumns
				]
			]
		]
	];
}
$slider_direction = "dir='ltr'";
$slider && $slider_direction = "dir='{$direction}'";
$slider && $slider_settings = "data-settings='" . wp_json_encode($slider_settings) . "'";

$testimonial_ratting = function ($ratting) {
	return sprintf('<div class="htmega-testimonial-ratting">
		<span class="fa fa-star%1$s"></span>
		<span class="fa fa-star%2$s"></span>
		<span class="fa fa-star%3$s"></span>
		<span class="fa fa-star%4$s"></span>
		<span class="fa fa-star%5$s"></span>
	</div>',
		$ratting < 1 && $ratting > 0 ? '-half-o' : ($ratting >= 1 ? '' : '-o'),
		$ratting > 1 && $ratting < 2 ? '-half-o' : ($ratting >= 2 ? '' : '-o'),
		$ratting > 2 && $ratting < 3 ? '-half-o' : ($ratting >= 3 ? '' : '-o'),
		$ratting > 3 && $ratting < 4 ? '-half-o' : ($ratting >= 4 ? '' : '-o'),
		$ratting > 4 && $ratting < 5 ? '-half-o' : ($ratting >= 5 ? '' : '-o'),
	);
};

ob_start();
?>
<div class="<?php echo esc_attr($classs); ?>" <?php echo ($slider_direction); ?> <?php echo ($slider ? $slider_settings : ''); ?>>
	<?php
	if (is_array($testimonials)) {
		foreach ($testimonials as $key => $testimonial):
			$default_img = sprintf('<img src="%s" alt="%s" width="300" height="300" />', esc_url($default_image_url), esc_attr($testimonial['name']));
			$testimonial_image = !empty($testimonial['image']['id']) ? wp_get_attachment_image($testimonial['image']['id'], 'medium', false, [
				"alt" => esc_attr($testimonial['name'])
			]) : $default_img;
			?>

			<div class="htmega-testimonial htmega-testimonial-<?php echo $testimonialStyle; ?>">
				<div class="htmega-testimonial-inner">
					<div class="htmega-testimonial-hover"></div>

					<?php if ($showThumbnail): ?>
						<div class='htmega-testimonial-thumbnail'>
							<?php echo ($testimonial_image); ?>
						</div>
					<?php endif; ?>

					<?php if ($showRatting && ($testimonialStyle === '1' || $testimonialStyle === '2' || $testimonialStyle === '5')): ?>
						<?php echo ($testimonial_ratting($testimonial['ratting'])); ?>
					<?php endif; ?>

					<div class='htmega-testimonial-content'>

						<?php if ($showContent): ?>
							<div class='htmega-testimonial-text'>
								<?php esc_html_e($testimonial['content']); ?>
								<?php if ($showIcon && $testimonialStyle === '1'): ?>
									<div class='htmega-testimonial-quote-icon'>
										<?php echo file_get_contents($quote_icon); ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ($showRatting && ($testimonialStyle === '3' || $testimonialStyle === '4')): ?>
							<?php echo ($testimonial_ratting($testimonial['ratting'])); ?>
						<?php endif; ?>

						<?php if ($showName) {
							printf(
								'<%1$s class="htmega-testimonial-name">%2$s</%1$s>',
								esc_attr($titleTag),
								esc_html($testimonial['name'])
							);
						} ?>

						<?php if ($showDesignation): ?>
							<span class='htmega-testimonial-designation<?php echo $showDesignationBorder ? ' htmega-testimonial-designation-shape' : '';?>'>
								<?php echo $testimonial['designation']; ?>
							</span>
						<?php endif; ?>

						<?php if ($showIcon && $testimonialStyle !== '1'): ?>
							<div class='htmega-testimonial-quote-icon'>
								<?php echo file_get_contents($quote_icon); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php
		endforeach;
	}
	?>
</div>
<?php
echo ob_get_clean();