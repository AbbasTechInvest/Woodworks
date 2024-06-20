<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

$custom_replaced_logo = apply_filters('betheme_logo', '') ? 'style="background-image:url('. apply_filters('betheme_logo_nohtml', ''). ')"' : '';
$version = apply_filters('betheme_disable_theme_version', MFN_THEME_VERSION);

$view_page_link = get_the_permalink( $this->post_id );
$generate_preview_link = get_permalink($this->post_id).'&'.apply_filters('betheme_slug', 'mfn').'-preview=true';

if( $this->template_type && in_array($this->template_type, array('footer', 'header')) ) {
    $view_page_link = get_site_url();
    $generate_preview_link = get_site_url().'?mfn-'.$this->template_type.'-template='.$this->post_id.'&'.apply_filters('betheme_slug', 'mfn').'-preview=true';
}elseif( $this->template_type && in_array($this->template_type, array('single-product', 'shop-archive')) ){
    $view_page_link = get_the_permalink( $this->post_id ).'?mfn-template-id='.$this->post_id;
    $generate_preview_link = get_the_permalink( $this->post_id ).'?mfn-template-id='.$this->post_id.'&'.apply_filters('betheme_slug', 'mfn').'-preview=true';
}

if( wp_get_referer() && strpos( wp_get_referer(), 'login' ) === false && strpos( wp_get_referer(), 'action=mfn-live-builder' ) === false ){
    $referrer = wp_get_referer();
}else{
    $referrer = admin_url();
}

?>

<div class="mfn-topbar">

    <div class="topbar-nav">
        <?php if( ! WHITE_LABEL ){ ?>
            <a href="<?php echo admin_url('admin.php?page='.apply_filters('betheme_slug', 'betheme')); ?>" class="mfnb-logo" <?php echo $custom_replaced_logo; ?>>Be Builder - Powered by Muffin Group</a>
        <?php }else{ ?>
            <a href="<?php echo admin_url('admin.php?page='.apply_filters('betheme_slug', 'betheme')); ?>" class="mfnb-logo" style="background-image:unset"></a>
        <?php } ?>
        <nav id="main-menu">
            <ul>
                <li class="menu-items active"><a data-tooltip="Elements" data-position="bottom" href="#">Elements <?php echo $this->view; ?></a></li>
                <li class="menu-sections"><a data-tooltip="Pre-built sections" data-position="bottom" href="#">Pre-built sections</a></li>
                <li class="menu-export"><a data-tooltip="Export / Import <?php echo ($this->view == 'demo' ? '(Unavailable in Demo)' : ''); ?>" data-position="bottom" href="#">Export / Import</a></li>
                <li class="menu-page"><a data-tooltip="Single page import <?php echo ($this->view == 'demo' ? '(Unavailable in Demo)' : ''); ?>" data-position="bottom" href="#">Single page import</a></li>
                <li class="menu-revisions"><a data-tooltip="History <?php echo ($this->view == 'demo' ? '(Unavailable in Demo)' : ''); ?>" data-position="bottom" href="#">History</a></li>
                <li class="menu-settings"><a data-tooltip="Settings <?php echo ($this->view == 'demo' ? '(Unavailable in Demo)' : ''); ?>" class="mfn-view-options-tab" data-position="bottom" href="#">Settings</a></li>
            </ul>
        </nav>
    </div>

    <div class="topbar-addons">

        <div class="mfn-option-dropdown page-options">
            <a href="#" class="mfn-option-btn btn-large mfn-option-text btn-icon-right mfn-option-blank"><span class="text pre-built-current"><?php echo get_the_title($this->post_id); ?></span><span class="mfn-icon mfn-icon-arrow-down"></span></a>
            <div class="dropdown-wrapper">
                <a class="mfn-dropdown-item" href="<?php echo admin_url('post.php?post='.$this->post_id.'&action=edit'); ?>"><span class="mfn-icon mfn-icon-edit"></span> Edit page</a>
                <a class="mfn-dropdown-item mfn-show-another-pages" href="#"><span class="mfn-icon mfn-icon-edit-pages"></span> Edit another page</a>
                <div class="mfn-dropdown-divider"></div>
                <a class="mfn-dropdown-item" href="<?php echo admin_url(); ?>"><span class="mfn-icon mfn-icon-wordpress"></span> Back to WordPress</a>
            </div>
        </div>

        <ul class="options-group responsive-mode mfn-preview-toolbar">
            <li><a class="mfn-option-btn btn-medium mfn-option-blank mfn-preview-opt btn-active" data-preview="desktop" title="Desktop" href="#" data-tooltip="Desktop 1441px <" data-position="bottom"><span class="mfn-icon mfn-icon-desktop"></span></a></li>
            <li><a class="mfn-option-btn btn-medium mfn-option-blank mfn-preview-opt" data-preview="laptop" title="Laptop" href="#" data-tooltip="Laptop 960 - 1440px" data-position="bottom"><span class="mfn-icon mfn-icon-laptop"></span></a></li>
            <li><a class="mfn-option-btn btn-medium mfn-option-blank mfn-preview-opt" data-preview="tablet" title="Tablet" href="#" data-tooltip="Tablet 768 - 959px" data-position="bottom"><span class="mfn-icon mfn-icon-tablet"></span></a></li>
            <li><a class="mfn-option-btn btn-medium mfn-option-blank mfn-preview-opt" data-preview="mobile" title="Mobile" href="#" data-tooltip="Mobile < 768px" data-position="bottom"><span class="mfn-icon mfn-icon-mobile"></span></a></li>
        </ul>

    </div>
    
    <div class="topbar-tools">

        <?php if( $this->template_type && in_array($this->template_type, array('header')) ) { ?>
            <div class="topbar-header-options mfn-header-type-preview">
                <a class="mfn-option-btn mfn-option-blank-dark btn-large mfn-preview-opt-header btn-active" data-preview="header-default" title="Default" href="#" data-tooltip="Default" href="#" data-position="bottom">Default</a>
                <a class="disabled mfn-option-btn mfn-option-blank-dark btn-large mfn-preview-opt-header" data-preview="header-sticky" title="Sticky" href="#" data-tooltip="Enable it in Header Options" href="#" data-position="bottom">Sticky</a>
                <a class="disabled mfn-option-btn mfn-option-blank-dark btn-large mfn-preview-opt-header" data-preview="header-mobile" title="Mobile" href="#" data-tooltip="Enable it in Header Options" href="#" data-position="bottom">Mobile</a>
            </div>
        <?php } ?>

        <a class="mfn-option-btn btn-large mfn-option-blank mfn-back-to-wp" title="Back to WordPress" href="<?php echo $referrer; ?>" data-tooltip="Back to WordPress" data-position="bottom"><span class="mfn-icon mfn-icon-wordpress"></span></a>

        <div class="mfn-option-dropdown">
            <a class="mfn-option-btn btn-large mfn-option-blank btn-icon-right mfn-option-text btn-icon-right btn-medium" style="padding: 0 4px;" href="#"><span class="mfn-icon mfn-icon-support"></span><span style="width: 9px;" class="mfn-icon mfn-icon-unfold"></span></a>
            <div class="dropdown-wrapper">
                <h6>Manual & Support:</h6>
                <div class="mfn-dropdown-divider"></div>
                <a target="_blank" class="mfn-dropdown-item" href="https://support.muffingroup.com/"><span class="mfn-icon mfn-icon-support-center"></span> Support Center</a>
                <a target="_blank" class="mfn-dropdown-item" href="https://forum.muffingroup.com/betheme/"><span class="mfn-icon mfn-icon-ticket-system"></span> Ticket System</a>
                <div class="mfn-dropdown-divider"></div>
                <a target="_blank" class="mfn-dropdown-item" href="https://support.muffingroup.com/documentation/"><span class="mfn-icon mfn-icon-documentation"></span> Documentation</a>
                <a target="_blank" class="mfn-dropdown-item" href="https://www.youtube.com/@MuffinGroup"><span class="mfn-icon mfn-icon-video-tutorials"></span> Video Tutorials</a>
                <a target="_blank" class="mfn-dropdown-item" href="https://support.muffingroup.com/faq/"><span class="mfn-icon mfn-icon-support-faq"></span> FAQ</a>
                <a target="_blank" class="mfn-dropdown-item" href="https://support.muffingroup.com/changelog/"><span class="mfn-icon mfn-icon-changelog"></span> Changelog</a>
                <div class="mfn-dropdown-divider"></div>
                <a target="_blank" class="mfn-dropdown-item" href="https://www.facebook.com/groups/betheme"><span class="mfn-icon mfn-icon-community"></span> FB Community</a>
                <a target="_blank" class="mfn-dropdown-item" href="https://muffingroup.com/betheme/"><span class="mfn-icon mfn-icon-be"></span> Betheme Website</a>
            </div>
        </div>

        <a class="mfn-option-btn btn-large mfn-option-blank btn-navigator-switcher" title="Navigator" href="#" data-tooltip="Navigator" data-position="bottom"><span class="mfn-icon mfn-icon-navigator"></span></a>
        <a class="mfn-option-btn btn-large mfn-option-blank btn-undo mfn-history-btn" title="Undo" href="#" data-tooltip="Undo" data-position="bottom"><span class="mfn-icon mfn-icon-undo"></span></a>
        <a class="mfn-option-btn btn-large mfn-option-blank btn-redo mfn-history-btn inactive" title="Redo" href="#" data-tooltip="Redo" data-position="bottom"><span class="mfn-icon mfn-icon-redo"></span></a>

        <a data-tooltip="View page" data-position="bottom" class="mfn-option-btn btn-large mfn-option-blank menu-viewpage" title="View page" href="<?php echo $view_page_link; ?>" target="_blank"><span class="mfn-icon mfn-icon-view-page"></span></a>
        <a class="mfn-option-btn btn-large mfn-option-blank mfn-preview-generate" title="Generate preview" data-tooltip="Generate preview" data-position="bottom" target="_blank" href="#" data-href="<?php echo $generate_preview_link; ?>"><span class="mfn-icon mfn-icon-preview"></span></a>
  
        <a href="#" data-action="<?php echo get_post_status($this->post_id) == 'publish' ? 'update' : 'publish'; ?>" class="mfn-btn btn-save-form-primary mfn-btn-green btn-copy-text btn-save-changes"><span class="btn-wrapper"><?php echo get_post_status($this->post_id) == 'publish' ? 'Update' : 'Publish'; ?></span></a>
        <div class="mfn-option-dropdown btn-save-action">
            <a href="#" class="mfn-btn btn-save-option mfn-btn-green"><span class="mfn-icon mfn-icon-unfold-light"></span></a>
            <div class="dropdown-wrapper">
                <a data-action="<?php echo get_post_status($this->post_id) == 'publish' ? 'draft' : 'update'; ?>" class="mfn-dropdown-item btn-save-form-secondary btn-save-changes" href="#"><?php echo get_post_status($this->post_id) == 'publish' ? 'Save as draft' : 'Save draft'; ?></a>
            </div>
        </div>

    </div>

</div>


