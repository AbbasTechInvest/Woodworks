<div class="mfn-modal has-footer modal-template-type show">

	<div class="mfn-modalbox mfn-form mfn-form-verical mfn-shadow-1">

		<div class="modalbox-header">

			<div class="options-group">
				<div class="modalbox-title-group">
					<span class="modalbox-icon mfn-icon-settings"></span>
					<div class="modalbox-desc">
						<h4 class="modalbox-title">New template</h4>
					</div>
				</div>
			</div>

			<div class="options-group">
				<a class="mfn-option-btn mfn-option-blank btn-large btn-modal-close" title="Close" href="edit.php?post_type=template">
					<span class="mfn-icon mfn-icon-close"></span>
				</a>
			</div>

		</div>

		<div class="modalbox-content">
			<h3>Templates Will Make Your Work Smarter</h3>
			<p>Create various pieces of your site, and then combine them with one click to build the final layout. It’s that simple.</p>

			<div class="template-type-form">
				<h4>Choose Type Of Template</h4>

				<?php
					$type = $this->getReferer();

					$mfn_post_types = array(
						'header' => 'Header',
						'footer' => 'Footer',
						'popup' => 'Popup',
						'megamenu' => 'Mega menu',
						'sidemenu' => 'Sidebar menu',
						'single-post' => 'Single post',
						'blog' => 'Blog',
						'single-portfolio' => 'Single portfolio',
						'portfolio' => 'Portfolio',
						'default' => 'Page template',
						'section' => 'Global section',
						'wrap' => 'Global wrap',
					);

					if(function_exists('is_woocommerce')){
						$mfn_post_types['single-product'] = 'Single product';
						$mfn_post_types['shop-archive'] = 'Shop archive';
					}
					
				?>

				<!-- input 1 -->
				<label class="form-label">Select the type of template you would like to create</label>
				<select class="mfn-form-control select-template-type df-input">
					<?php foreach ($mfn_post_types as $m => $p) {
						echo '<option value="'.$m.'" '.selected( $type, $m ).' >'.$p.'</option>';
					} ?>
				</select>

				<!-- input 2 -->
				<label class="form-label">Name your template</label>
				<input type="text" class="mfn-form-control input-template-type-name df-input" placeholder="Name">

			</div>
		</div>

		<div class="modalbox-footer">
			<div class="options-group right">
				<a class="mfn-btn mfn-btn-blue btn-modal-save btn-save-template-type" data-builder="<?php echo apply_filters('betheme_slug', 'mfn'); ?>" href="#"><span class="btn-wrapper">Create template</span></a>
				<a class="mfn-btn btn-modal-close" href="edit.php?post_type=template"><span class="btn-wrapper">Cancel</span></a>
			</div>
		</div>

	</div>

</div>
