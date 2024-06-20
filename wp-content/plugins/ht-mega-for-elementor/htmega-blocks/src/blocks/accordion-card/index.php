<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

$card_classes = ['htmega-accordion-card'];
$settings['open'] && $card_classes[] = 'htmega-accordion-card-active';

ob_start();
?>
<div class="<?php echo trim(implode(' ', $card_classes)); ?>">
	<div class="htmega-accordion-card-header">
		<?php echo "<{$settings['titleTag']} class='htmega-accordion-card-title'>{$settings['title']}</{$settings['titleTag']}>"; ?>
		<div class="htmega-accordion-card-indicator">
			<span class="inactive <?php echo $settings['iconInActive']; ?>"></span>
			<span class="active <?php echo $settings['iconActive']; ?>"></span>
		</div>
	</div>
	<div class="htmega-accordion-card-body" style="<?php echo !$settings['open'] ? 'display: none;' : ''; ?>">
		<div class="htmega-accordion-card-body-inner">
			<?php echo $content; ?>
		</div>
	</div>
</div>
<?php
echo ob_get_clean();
?>