<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Widgets;
	
	use Infocob\CRM\Products\Admin\Classes\Infocob\Tools\DateTimeFr;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class IcpConfigurationDashboardWidget extends Widget {
		
		public function render($arg1, $arg2) {
			$crons = Tools::getCRONImports();
			
			$content_html = '
				<div class="content-widget">
					<div class="header">
						<h2>' . esc_html_x('CRON scheduled', 'Dashboard Widget', 'infocob-crm-products') . '</h2>
					</div>
			';
			if(!empty($crons)) {
				$wp_date_format = get_option( 'date_format' );
				$wp_time_format = get_option( 'time_format' );
				
				$index = 1;
				foreach ($crons as $post_id => $cron_timestamp) {
					$api_cron_date = new DateTimeFr("now", new \DateTimeZone("Europe/Paris"));
					$api_cron_date = $api_cron_date->setTimestamp($cron_timestamp);
					$api_cron_date = $api_cron_date->format($wp_date_format) . " " . $api_cron_date->format($wp_time_format);
					
					$icp_configuration_post = get_post($post_id);
					if (!empty($icp_configuration_post) && $icp_configuration_post instanceof \WP_Post) {
						$post_link = get_edit_post_link($icp_configuration_post->ID);
						$post_title = get_the_title($icp_configuration_post->ID);
						
						if ($index%2 == 1) {
							$line_class = 'odd';
						} else {
							$line_class = 'even';
						}
						
						$content_html .= '
							<div class="cron ' . $line_class .'">
								<div class="content">
									<span class="name"><a href="' . $post_link .'">' . $post_title . '</a></span>
									<span class="start-import" data-post_id="' . $icp_configuration_post->ID . '">' . esc_html_x('Start import', "Import configuration list page", "infocob-crm-products") . '</span>
									<span class="date">' . $api_cron_date . '</span>
								</div>
							</div>
						';
						
						$index++;
					}
				}
			} else {
				$content_html .= '<div class="no-crons">' . esc_html_x('No CRON task scheduled', 'Dashboard Widget', 'infocob-crm-products') . '</div>';
			}
			$content_html .= '</div>';
			
			echo wp_kses_post($content_html);
		}
	}
