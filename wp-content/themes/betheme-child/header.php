<?php
/**
 * The Header for our theme.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */
?><!DOCTYPE html>
<?php
if ($_GET && key_exists('mfn-rtl', $_GET)):
	echo '<html class="no-js" lang="ar" dir="rtl">';
else:
	?>
	<html <?php language_attributes(); ?> class="no-js <?php echo esc_attr(mfn_html_classes()); ?>" <?php mfn_tag_schema(); ?>>
<?php endif; ?>

<head>

	<meta charset="<?php bloginfo('charset'); ?>" />
	<?php wp_head();

	global $mfn_global;

	if (empty($_GET['visual']) && !empty($mfn_global['sidemenu'])) {
		// global sidemenu
		$sidemenu = new MfnSideMenu($mfn_global['sidemenu']);
		$sidemenu->css();
	}

	if (!empty(get_post_meta(get_the_ID(), 'mfn-post-js', true)))
		echo get_post_meta(get_the_ID(), 'mfn-post-js', true);

	?>

	<!-- code for preloader -->
	<style>
		/* Fullscreen video container */
		#preloader-video-container {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: #fff;
			/* Fallback color */
			z-index: 9999;
			/* Ensure it appears above all content */
			display: flex;
			align-items: center;
			justify-content: center;
			border: 0;
		}

		#preloader-video {
			width: auto;
			height: 100%;
			object-fit: cover;
			/* Ensure video covers the entire container */
		}

		/* Responsive Styles for Mobile Devices */
		@media (max-width: 768px) {
			#preloader-video {
				width: 200%;
				/* Adjust width for mobile */
				height: auto;
				/* Maintain aspect ratio */
				object-fit: cover;
			}
		}
	</style>

</head>

<body <?php body_class(); ?>>

	<?php if (is_front_page()) : ?>
		<?php
			function is_iOS() {
				$user_agent = ( isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' );
				return (strpos($user_agent, 'iPad') !== false || strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPod') !== false);
			}
		?>
		<?php if (!is_iOS()) : // no video for ios ?>


			<!-- Preloader Video Container -->
			<?php /* <div id="preloader-video-container">
				<video id="preloader-video" autoplay muted playsInline>
					<source src="<?php echo get_site_url(); ?>/wp-content/uploads/2024/06/monk.mp4" type="video/mp4">
				</video>
			</div> */ ?>

			<!-- Rest of your body content -->

			<script>

				// trigger action before document is ready
				if (localStorage.getItem('preloaderSeen')) {
					jQuery('#preloader-video-container').remove();
				}
				else {
					// Call the function to inject the preloader
					injectPreloader();
				}

				function injectPreloader() {
					const container = document.createElement('div');
					container.id = "preloader-video-container";

					const video = document.createElement('video');
					video.id = "preloader-video";
					video.autoplay = true;
					video.muted = true;
					video.playsinline = true; // Ensures inline playback without controls

					const source = document.createElement('source');
					source.src = "<?php echo get_site_url(); ?>/wp-content/uploads/2024/06/monk.mp4";
					source.type = "video/mp4";

					video.appendChild(source);
					container.appendChild(video);

					document.body.appendChild(container);


					document.addEventListener("DOMContentLoaded", function () {
						const video = document.getElementById('preloader-video');

						video.onended = function () {
							const preloaderContainer = document.getElementById('preloader-video-container');
							preloaderContainer.style.display = 'none';
							localStorage.setItem('preloaderSeen', true);
						}
					});
				}
			</script>

		<?php endif; ?>
	<?php endif; ?>

	<?php if (mfn_is_blocks()): ?>

		<div id="Wrapper">

		<?php else: // mfn_is_blocks() ?>

			<?php

			if (!empty(get_post_meta(get_the_ID(), 'mfn-post-one-page', true)) && get_post_meta(get_the_ID(), 'mfn-post-one-page', true) == '1') {
				echo '<div id="home"></div>';
			}
			?>

			<?php do_action('mfn_hook_top'); ?>

			<?php get_template_part('includes/header', 'sliding-area'); ?>

			<?php
			if (mfn_header_style(true) == 'header-creative') {
				get_template_part('includes/header', 'creative');
			}
			?>

			<div id="Wrapper">

				<?php

				if (mfn_header_style(true) == 'header-below') {
					echo mfn_slider();
				}

				// be setup wizard
				if (isset($_GET['mfn-setup-preview'])) {
					$mfn_global['header'] = false;
				}

				if ($mfn_global['header']) {
					$is_visual = false;
					if (!empty($_GET['visual']))
						$is_visual = true;
					get_template_part('includes/header', 'template', array('id' => $mfn_global['header'], 'visual' => $is_visual));
				} else {
					get_template_part('includes/header', 'classic');
				}

				if ('intro' == get_post_meta(mfn_ID(), 'mfn-post-template', true)) {
					get_template_part('includes/header', 'single-intro');
				}
				?>

				<?php do_action('mfn_hook_content_before'); ?>

			<?php endif; // mfn_is_blocks() ?>

			<?php // omit closing php tag
