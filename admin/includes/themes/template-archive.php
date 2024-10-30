<?php
$html_top_filters = do_shortcode("{{infocob_crm_products_catalog_top_filters}}");
$html_lef_filters = do_shortcode("{{infocob_crm_products_catalog_left_filters}}");
$html_rig_filters = do_shortcode("{{infocob_crm_products_catalog_right_filters}}");

$q = get_queried_object();
$archive_title = is_a($q, "WP_term") ? $q->name : (
is_a($q, "WP_Post_Type") ? $q->label : ""
);

$nb_cols = 1;
$nb_cols += !empty($html_lef_filters) ? 1 : 0;
$nb_cols += !empty($html_rig_filters) ? 1 : 0;

get_header();

?>

    <div class="infocobprod-wrapper-archive infocobprod-wrapper-archive-<?php echo esc_attr($nb_cols); ?>-cols infocobprod-wrapper-archive-{{post_type}}">

        <div class="infocobprod-wrapper-archive-inner">
            <?php if ($html_top_filters) { ?>
                <div class="infocobprod-filtres infocobprod-filtres-top">
                    <div class="infocobprod-filtres-inner">
                        <p class="infocobprod-filtres-h"><?php echo esc_html_x("Filters", "Title filters catalog front, 'top-filters'", "infocob-crm-products"); ?></p>

                        <?php echo wp_kses($html_top_filters, [
							"div" => [
								"id" => [],
								"class" => [],
								"data-post_id" => [],
								"data-min" => [],
								"data-max" => [],
								"data-average" => [],
								"data-unit" => [],
								"data-step" => [],
								"data-min-value" => [],
								"data-max-value" => [],
							],
							"form" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"method" => []
							],
							"input" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"min" => [],
								"max" => [],
								"readonly" => [],
								"required" => [],
								"checked" => []
							],
							"select" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"min" => [],
								"max" => [],
								"readonly" => [],
								"required" => [],
								"multiple" => [],
							],
							"label" => [
								"for" => [],
								"class" => [],
							],
							"a" => [
								"href" => []
							],
							"option" => [
								"value" => [],
								"class" => [],
								"selected" => []
							]
						]); ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="infocobprod-wrapper-archive-inner infocobprod-wrapper-archive-body">

        <?php if ($html_lef_filters) { ?>
                <div class="infocobprod-filtres infocobprod-filtres-left">
                    <div class="infocobprod-filtres-inner">
                        <p class="infocobprod-filtres-h"><?php echo esc_html_x("Filters", "Title filters catalog front, 'left-filters'", "infocob-crm-products"); ?></p>
                        <?php echo wp_kses($html_lef_filters, [
							"div" => [
								"id" => [],
								"class" => [],
								"data-post_id" => [],
								"data-min" => [],
								"data-max" => [],
								"data-average" => [],
								"data-unit" => [],
								"data-step" => [],
								"data-min-value" => [],
								"data-max-value" => [],
							],
							"form" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"method" => []
							],
							"input" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"min" => [],
								"max" => [],
								"readonly" => [],
								"required" => [],
								"checked" => []
							],
							"select" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"min" => [],
								"max" => [],
								"readonly" => [],
								"required" => [],
								"multiple" => [],
							],
							"label" => [
								"for" => [],
								"class" => [],
							],
							"a" => [
								"href" => []
							],
							"option" => [
								"value" => [],
								"class" => [],
								"selected" => []
							]
						]); ?>
                    </div>
                </div>
            <?php } ?>

            <div class="infocobprod-content">
                <h2 class="infocobprod-content-h"><?php echo esc_html($archive_title); ?></h2>

                <?php if (get_the_archive_description()) { ?>
                    <div class="infocobprod-content-excerpt">
                        <?php the_archive_description(); ?>
                    </div>
                <?php } ?>

                <?php if (have_posts()): ?>
                    <div class="infocobprod-content-entries">
                        <?php while (have_posts()): the_post(); ?>
                            <?php get_template_part('entry', '{{post_type}}'); ?>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

                <?php //Pagination
                the_posts_pagination([
                    'mid_size' => 2,
                    'prev_text' => __('←', 'textdomain'),
                    'next_text' => __('→', 'textdomain'),
                ]);
                ?>
            </div>


            <?php if ($html_rig_filters) { ?>
                <div class="infocobprod-filtres infocobprod-filtres-right">
                    <div class="infocobprod-filtres-inner">
                        <p class="infocobprod-filtres-h"><?php echo esc_html_x("Filters", "Title filters catalog front, 'right-filters'", "infocob-crm-products"); ?></p>
                        <?php echo wp_kses($html_rig_filters, [
							"div" => [
								"id" => [],
								"class" => [],
								"data-post_id" => [],
								"data-min" => [],
								"data-max" => [],
								"data-average" => [],
								"data-unit" => [],
								"data-step" => [],
								"data-min-value" => [],
								"data-max-value" => [],
							],
							"form" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"method" => []
							],
							"input" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"min" => [],
								"max" => [],
								"readonly" => [],
								"required" => [],
								"checked" => []
							],
							"select" => [
								"id" => [],
								"type" => [],
								"name" => [],
								"value" => [],
								"class" => [],
								"min" => [],
								"max" => [],
								"readonly" => [],
								"required" => [],
								"multiple" => [],
							],
							"label" => [
								"for" => [],
								"class" => [],
							],
							"a" => [
								"href" => []
							],
							"li" => [
								"class" => [],
								"data-value" => []
							],
							"ul" => [
								"class" => []
							],
							"option" => [
								"value" => [],
								"class" => [],
								"selected" => []
							]
						]); ?>
                    </div>
                </div>
            <?php } ?>
        </div>


    </div>
<?php get_footer();
