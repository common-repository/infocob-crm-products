<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy;
	
	use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\Tax;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Filter;
	use Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy;
	use WP_Term;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ListFilter extends \Infocob\CRM\Products\Admin\Classes\Catalog\Filters\Parts\Taxonomy\Filter implements Filter{
		
		/**
		 * @param int    $post_id
		 * @param string $taxonomy
		 * @param array  $categories
		 * @param string $display
		 * @param string $title
		 * @param string $unit
		 * @param array  $defaults
		 */
		public function __construct(int $post_id, string $taxonomy, array $categories, string $display, string $title, string $unit, array $defaults) {
			parent::__construct($post_id, $taxonomy, $categories, $display, $title, $unit, $defaults);
		}
		
		/**
		 * @return string
		 */
		public function get() {
			$list = $this->getCategories($this->categories);
			
			$post_type = get_post_meta($this->post_id, "general-post-type", true);
			$archive_link = '';
			if(!empty($post_type)) {
				$archive_link = get_post_type_archive_link($post_type);
			}
			
			return '
				<div class="filter filter-links taxonomy ' . $this->taxonomy . '">
					<label><a href="' . $archive_link . '">' . $this->title . '</a></label>
					<ul class="list">
						' . $list . '
					</ul>
				</div>
			';
		}
		
		private function getCategories($categories) {
			$html = '';
			foreach ($categories as $category) {
				if($category instanceof WP_Term) {
					$selected = (get_queried_object_id() === $category->term_id) ? 'selected' : '';
					$link = get_term_link($category->term_id);
					
					$html .= '<li class="' . $selected . '" data-value="' . $category->term_id . '"><a href="' . $link . '">' . $category->name . '</a></li>';
					if (!empty($category->childs)) {
						$html .= '<ul>';
						$html .= $this->getCategories($category->childs);
						$html .= '</ul>';
					}
				}
			}
			
			return $html;
		}
	}
