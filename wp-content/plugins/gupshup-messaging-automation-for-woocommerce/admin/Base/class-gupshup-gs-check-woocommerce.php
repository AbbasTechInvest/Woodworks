<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class GupshupGSCheckWoocommerce {

	/**
	 * Checking woocommerce installed or activated
	 *
	 * @return void
	 */
	public function check_woocommerce_installed() {
		if ( ! function_exists( 'WC' ) ) {
			$this->show_message_to_install_woocommerce();
			return false;
		}
		return true;
	}

	/**
	 * Is woocommerce plugin installed.
	 *
	 * @return boolean
	 */
	public function is_woocommerce_installed() {
		$path    = 'woocommerce/woocommerce.php';
		$plugins = get_plugins();
		return isset( $plugins[ $path ] );
	}

	/**
	 * Showing message to install or activate the woocommerce
	 *
	 * @return void
	 */
	public function show_message_to_install_woocommerce() {

		$screen = get_current_screen();

		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		$plugin  = 'woocommerce/woocommerce.php';

		if ( $this->is_woocommerce_installed() ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$action_url   = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1', 'activate-plugin_' . $plugin );
			$button_label = 'Activate WooCommerce';

		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
			$button_label = 'Install WooCommerce';
		}

		?>
		<div id="message" class="notice notice-error">
			<p>
				The <strong><?php echo esc_html( GUPSHUP_GS_PLUGIN_NAME ); ?></strong> plugin requires <strong>WooCommerce</strong> plugin installed & activated.
			</p>
			<?php echo '<p><a href="' . esc_url($action_url) . '" class="button-primary">' . esc_html($button_label) . '</a></p><p></p>'; ?>
		</div>

		<?php
	}

}
