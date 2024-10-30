<?php
	$thumb = get_the_post_thumbnail_url(null, 'medium');
	if (!$thumb) {
		$images = icp_get_images(get_the_ID(), ['medium']);
		
		$thumb = $images[0]["thumbURL-medium"] ?? false;
	}
?>

<div class="infocobprod-entry infocobprod-entry-{{post_type}}">
	
	<div class="infocobprod-entry-thumb">
		<?php if ($thumb) { ?>
			<?php echo esc_html($thumb); ?>
		<?php } else { ?>
			<div class="no-thumb"></div>
		<?php } ?>
	</div>
	<div class="infocobprod-entry-content">
		<h3 class="infocobprod-entry-h"><?php the_title(); ?></h3>
		<div class="infocobprod-entry-excerpt"><?php the_excerpt(); ?></div>
		<a href="<?php the_permalink(); ?>" class="infocobprod-entry-btn">Lire la suite</a>
	</div>
</div>
