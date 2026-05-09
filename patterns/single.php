<?php
/**
 * Title: single
 * Slug: wptv3/single
 * Inserter: no
 */
?>
<!-- wp:template-part {"slug":"header","tagName":"header","area":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"0vh","bottom":"0vh"},"padding":{"top":"10vh","bottom":"8vh"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0vh;margin-bottom:0vh;padding-top:10vh;padding-bottom:8vh"><!-- wp:group {"align":"wide","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group alignwide"><!-- wp:post-title {"level":1,"align":"wide","fontSize":"x-large"} /-->

<!-- wp:shortcode -->
[wptv_video_speakers]
<!-- /wp:shortcode --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|10","left":"var:preset|spacing|30"},"padding":{"right":"0","left":"0"}}}} -->
<div class="wp-block-columns alignwide" style="padding-right:0;padding-left:0"><!-- wp:column {"width":"75%"} -->
<div class="wp-block-column" style="flex-basis:75%"><!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:post-content {"lock":{"move":false,"remove":false},"layout":{"type":"constrained"}} /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:jetpack/like /-->

<!-- wp:jetpack/sharing-buttons {"size":"has-small-icon-size","layout":{"type":"flex","justifyContent":"right"}} -->
<ul class="wp-block-jetpack-sharing-buttons has-small-icon-size jetpack-sharing-buttons__services-list" id="jetpack-sharing-serivces-list"><!-- wp:jetpack/sharing-button {"service":"x","label":"X"} /-->

<!-- wp:jetpack/sharing-button {"service":"facebook","label":"Facebook"} /-->

<!-- wp:jetpack/sharing-button {"service":"linkedin","label":"LinkedIn"} /-->

<!-- wp:jetpack/sharing-button {"service":"mail","label":"Mail"} /-->

<!-- wp:jetpack/sharing-button {"service":"share","label":"Share"} /--></ul>
<!-- /wp:jetpack/sharing-buttons --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"var:preset|spacing|10"} -->
<div style="height:var(--wp--preset--spacing--10)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group -->

<!-- wp:post-excerpt {"className":"single_excerpt"} /-->

<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:spacer {"height":"4rem"} -->
<div style="height:4rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:comments {"className":"wp-block-comments-query-loop"} -->
<div class="wp-block-comments wp-block-comments-query-loop"><!-- wp:heading -->
<h2 class="wp-block-heading">Comments</h2>
<!-- /wp:heading -->

<!-- wp:comments-title {"level":3} /-->

<!-- wp:comment-template -->
<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"style":{"spacing":{"blockGap":"0.5em"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:avatar {"size":40} /-->

<!-- wp:group -->
<div class="wp-block-group"><!-- wp:comment-author-name /-->

<!-- wp:comment-date /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:comment-content /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:comment-edit-link /-->

<!-- wp:comment-reply-link /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:comment-template -->

<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:comments-pagination-previous /-->

<!-- wp:comments-pagination-next /-->
<!-- /wp:comments-pagination -->

<!-- wp:post-comments-form {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} /--></div>
<!-- /wp:comments -->

<!-- wp:spacer {"height":"4rem"} -->
<div style="height:4rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"tagName":"nav","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|40","top":"var:preset|spacing|40"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"},"ariaLabel":"Posts"} -->
<nav aria-label="Posts" class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:post-navigation-link {"type":"previous","label":"Previous: ","showTitle":true,"linkLabel":true,"arrow":"arrow"} /-->

<!-- wp:post-navigation-link {"label":"Next: ","showTitle":true,"linkLabel":true,"arrow":"arrow"} /--></nav>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"large"} -->
<h2 class="wp-block-heading has-large-font-size">Video details</h2>
<!-- /wp:heading -->

<!-- wp:group {"layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:shortcode -->
[wptv_video_details_single]
<!-- /wp:shortcode --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:separator {"className":"is-style-wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"backgroundColor":"contrast"} -->
<hr class="wp-block-separator has-text-color has-contrast-color has-alpha-channel-opacity has-contrast-background-color has-background is-style-wide" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)"/>
<!-- /wp:separator -->

<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:image {"lightbox":{"enabled":false},"width":"200px","sizeSlug":"full","linkDestination":"custom","align":"center"} -->
<figure class="wp-block-image aligncenter size-full is-resized"><a href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank" rel="license noreferrer noopener"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/images/cc-by-sa.png" alt="Creative Commons License" class="" style="width:200px"/></a></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">This work is licensed under a <a href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank" rel="noreferrer noopener">Creative Commons Attribution-ShareAlike 4.0 International License</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer","area":"footer"} /-->