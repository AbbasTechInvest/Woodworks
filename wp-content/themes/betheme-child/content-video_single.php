<?php
/**
 * template for single video page
*/                           
?>
                            <div class="mfn-single-product-tmpl-wrapper product type-product post-1212 status-publish first instock product_cat-diy-craft-work-projects has-post-thumbnail shipping-taxable purchasable product-type-simple">
                                <div class="section_wrapper no-bebuilder-section clearfix">
                                    <div class="woocommerce-notices-wrapper"></div>
                                </div>
                                <div class="mfn-builder-content mfn-single-product-tmpl-builder">
                                    <section class="section mcb-section mfn-default-section mcb-section-3rzpvgzkb" style="">
                                        <div class="mcb-background-overlay"></div>
                                        <div class="section_wrapper mfn-wrapper-for-wraps mcb-section-inner mcb-section-inner-3rzpvgzkb">
                                            <div class="wrap mcb-wrap mcb-wrap-480f7ae97 one-second tablet-one-second laptop-one-second mobile-one valign-top clearfix" data-desktop-col="one-second" data-laptop-col="laptop-one-second" data-tablet-col="tablet-one-second" data-mobile-col="mobile-one" style="padding:0 2% 0 0;background-color:">
                                            <div class="mcb-wrap-inner mcb-wrap-inner-480f7ae97 mfn-module-wrapper mfn-wrapper-for-wraps">
                                                <div class="mcb-wrap-background-overlay"></div>
                                                <div class="column mcb-column mcb-item-373b093f1 one laptop-one tablet-one mobile-one column_product_images" style="">
                                                    <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-373b093f1 mcb-item-product_images-inner">
                                                        <div class="woocommerce-product-gallery woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-4 images mfn-product-hasnt-gallery " data-columns="4" style="opacity: 1; transition: opacity 0.25s ease-in-out 0s;">
                                                        <figure class="woocommerce-product-gallery__wrapper" data-columns="4">
                                                            <div data-thumb="<?php the_post_thumbnail_url(); ?>" data-thumb-alt="" class="woocommerce-product-gallery__image"><a href="<?php the_post_thumbnail_url(); ?>"><img fetchpriority="high" width="570" height="570" src="<?php the_post_thumbnail_url(); ?>" class="wp-post-image" alt="" title="409ee593cce9872190539c7bfe8d67c1" data-caption="" data-src="<?php the_post_thumbnail_url(); ?>" data-large_image="<?php the_post_thumbnail_url(); ?>" data-large_image_width="570" data-large_image_height="570" decoding="async"></a></div>
                                                        </figure>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="wrap mcb-wrap mcb-wrap-0729a69bd one-second tablet-one-second laptop-one-second mobile-one column-margin-20px valign-top sticky-desktop clearfix" data-desktop-col="one-second" data-laptop-col="laptop-one-second" data-tablet-col="tablet-one-second" data-mobile-col="mobile-one" style="padding:0 0 0 0;background-color:">
                                            <div class="mcb-wrap-inner mcb-wrap-inner-0729a69bd mfn-module-wrapper mfn-wrapper-for-wraps">
                                                <div class="mcb-wrap-background-overlay"></div>
                                                <div class="column mcb-column mcb-item-c7fb3f627 one laptop-one tablet-one mobile-one column_product_title" style="">
                                                    <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-c7fb3f627 mcb-item-product_title-inner">
                                                        <h2 class="woocommerce-products-header__title title page-title"><?php the_title() ?></h2>
                                                    </div>
                                                </div>
                                                <div class="column mcb-column mcb-item-c7e9d8941 one laptop-one tablet-one mobile-one column_product_short_description" style="">
                                                    <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-c7e9d8941 mcb-item-product_short_description-inner">
                                                        <div class="woocommerce-product-details__short-description">
                                                        <p><?php the_field('video_short_description') ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="column mcb-column mcb-item-3eebb40a5 one laptop-one tablet-one mobile-one column_product_price" style="">
                                                    <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-3eebb40a5 mcb-item-product_price-inner">
                                                        <div class="price"><span class="woocommerce-Price-amount amount"><bdi>5.000&nbsp;<span class="woocommerce-Price-currencySymbol">د.ك</span></bdi></span></div>
                                                    </div>
                                                </div>
                                                <div class="column mcb-column mcb-item-2fdbbf051 one laptop-one tablet-one mobile-one column_product_cart_button" style="">
                                                    <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-2fdbbf051 mcb-item-product_cart_button-inner">
                                                        <div class="mfn-product-add-to-cart ">
                                                            <button class="button alt" onclick="book(this)" data-amount="<?php the_field('video_fee'); ?>" id="video_<?php echo get_the_ID(); ?>"><span class="button_label">Buy Now</span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="column mcb-column mcb-item-bcac2cb65 one laptop-one tablet-one mobile-one column_product_meta" style="">
                                                    <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-bcac2cb65 mcb-item-product_meta-inner">
                                                        <div class="product_meta mfn_product_meta">
                                                            <span class="posted_in">Category: 
                                                                <a class=""><?php $category = get_the_category(); if(!empty($category)){ echo $category[0]->cat_name; } else { echo "N/A"; } ?></a>
                                                            </span><br>
                                                            <span class="posted_in">Tags: 
                                                                <?php
                                                                    $tags = get_the_tags();
                                                                    if ($tags):
                                                                        $i=0;
                                                                        foreach ($tags as $tag): ?>
                                                                            <a class="">
                                                                                <?php
                                                                                    if ($i>0) { echo ", "; }
                                                                                    echo $tag->name;
                                                                                    $i++;
                                                                                ?>
                                                                            </a>
                                                                        <?php endforeach;
                                                                    endif;
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                                <div class="section_wrapper no-bebuilder-section clearfix"></div>
                            </div>

                            <div style="padding:25px;">
                                <div class="mfn-builder-content mfn-single-product-tmpl-builder">
                                    <div style="padding:25px">
                                        <h3>Description</h3>
                                        <p><?php the_content(); ?></p>
                                        <hr>
                                        <h3>General Information</h3>
                                        <p>All our Tutorial videos are made in Kuwait, and Wework holds the complete rights to these videos.</p>
                                        <p>We welcome all feedbacks, suggestions that may improve our services.</p>
                                        <p>You may <a href="<?php echo site_url() . "/contact-2"; ?>">contact us</a>, If you wish to get featured on a video or collaborate with us.</p>
                                        <br><hr>
                                        <?php $trailer_link = get_field('video_trailer_link'); ?>
                                        <?php if(!empty($trailer_link)): ?>
                                        <h3>Trailer</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="background:#000; margin:-80px 0 -50px 0;">
                            <div class="container" style="padding:3% 5%">
                                <div style="padding:56.25% 0 0 0;position:relative;">
                                    
                                    <?php /* smaple url structure: https://player.vimeo.com/video/903738885 */?>
                                    <iframe src="<?php echo get_field('video_trailer_link'); ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture"
                                        style="position:absolute;top:0;left:0;width:100%;height:100%;"
                                        title="<?php the_title() ?>"></iframe>
                                </div>
                            </div>
                        </div>

                        <div class="container" style="max-width: 1200px; padding: 25px">
                            <div style="padding:25px;">
                                <div class="mfn-builder-content mfn-single-product-tmpl-builder">
                                    <div style="padding:25px">
                                        <?php endif; ?>
                                        <h3>Rules</h3>
                                        <!-- <small> -->
                                            <ul style="list-style-type: square;">
                                                <li>Respect Copyright: Users must not share, distribute, or reproduce tutorial videos without proper authorization.</li>
                                                <li>Personal Use Only: Tutorial videos are for personal, non-commercial use. Commercial use requires explicit permission.</li>
                                                <li>No Redistribution: Users are strictly prohibited from redistributing tutorial videos on any platform.</li>
                                                <li>Follow Instructor Guidelines: Users must adhere to safety guidelines and instructions provided in the videos.</li>
                                                <li>Report Violations: Prompt reporting of copyright infringements or rule violations is encouraged.</li>
                                                <li>No Modifications: Users cannot alter or edit tutorial videos in any way.</li>
                                                <li>Compliance: Users must follow the website's terms of service and privacy policy.</li>
                                                <li>Disclaimer: Users access and use tutorial videos at their own risk.</li>
                                            </ul>
                                        <!-- </small> -->
                                    </div>
                                </div>
                            </div>