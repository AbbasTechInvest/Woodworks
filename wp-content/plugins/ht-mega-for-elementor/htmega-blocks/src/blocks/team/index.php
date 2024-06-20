<?php
	// Exit if accessed directly.
	if ( !defined('ABSPATH') ) { exit; }

	$card_classes = [
		"htmega-block-{$settings['blockUniqId']}",
		"htmega-team",
		"htmega-team-{$settings['teamStyle']}",
	];

	if(empty($settings['image']) || empty($settings['image']['url'])) {
		$settings['image'] = [
			"url" => HTMEGA_BLOCK_URL .'src/assets/images/team.jpg',
			"width" => 370,
			"height" => 450
		];
	}

	ob_start();
	?>
		<div class="<?php echo trim(implode(' ', $card_classes)); ?>">
			<div class="htmega-team-thumbnail">
				<img src="<?php echo $settings['image']['url'] ?>" width="<?php echo $settings['image']['width'] ?>" height="<?php echo $settings['image']['height'] ?>" />
			</div>
			
			<div class='htmega-team-content'>
				<div class='htmega-team-content-inner'>
					<<?php echo $settings['nameTag'] ?> class='htmega-team-name'><?php echo $settings['name'] ?></<?php echo $settings['nameTag'] ?>>
					<span class='htmega-team-designation'><?php echo $settings['designation'] ?></span>
					<?php echo $settings['showBio'] ? "<p class='htmega-team-bio'>{$settings['bio']}</p>" : ''; ?>
				</div>
				<ul class='htmega-team-social'>
				<?php 
					foreach ($settings['socials'] as $social) {
						$label = $social['label'];
						$icon = $social['icon'];
						$link = $social['link'];
						$target = $settings['newTab'] ? 'target="_blank"' : '';
						$nofollow = $settings['noFollow'] ? 'rel="nofollow"' : '';
						echo "<li><a href='{$link}' {$target} {$nofollow} aria-label='{$label}'><span class='{$icon}'></span></a></li>";
					}
				?>
				</ul>
			</div>
		</div>
	<?php
	echo ob_get_clean();
?>