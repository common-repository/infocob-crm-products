<?php
	
	namespace Infocob\CRM\Products\Admin\Classes\Catalog;
	
	use Infocob\CRM\Products\Admin\Classes\ProductMeta;
	use Infocob\CRM\Products\Admin\Classes\Tools;
	use WP_Query;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class ProductsList {
		
		protected int    $post_id;
		protected string $post_type;
		protected        $products;
		protected        $wp_query_args = [];
		
		protected int   $post_per_page = -1;
		protected bool  $display_image = false;
		protected array $orders        = [];
		protected array $btn_text      = [];
		protected array $default_image = [];
		
		protected array $default_filters = [];
		
		/**
		 * @param int $post_id
		 */
		public function __construct(int $post_id, array $default_filters) {
			$this->post_id = $post_id;
			$this->default_filters = $default_filters;
			
			$this->post_type = get_post_meta($post_id, "general-post-type", true);
			$per_page = (int)filter_var(get_post_meta($post_id, "products-per-page", true), FILTER_SANITIZE_NUMBER_INT);
			if ($per_page <= 0) {
				$this->post_per_page = -1;
			} else {
				$this->post_per_page = $per_page;
			}
			
			$this->display_image = (bool)filter_var(get_post_meta($post_id, "products-display-image", true), FILTER_VALIDATE_BOOLEAN);
			
			$products_btn_text_base64 = get_post_meta($post_id, "products-btn-text", true);
			$products_btn_text = Tools::decodeConfig($products_btn_text_base64);
			if (empty($products_btn_text) || !is_array($products_btn_text)) {
				$products_btn_text = [];
			}
			$this->btn_text = $products_btn_text;
			
			$products_default_image_base64 = get_post_meta($post_id, "products-default-image", true);
			$products_default_image = Tools::decodeConfig($products_default_image_base64);
			if (empty($products_default_image) || !is_array($products_default_image)) {
				$products_default_image = [];
			}
			$this->default_image = $products_default_image;
			
			$products_order_by_base64 = get_post_meta($post_id, "products-order-by", true);
			$this->orders = (array)Tools::decodeConfig($products_order_by_base64);
			if (empty($this->orders)) {
				$this->orders = [];
			}
			
		}
		
		public function setDefaultQuery(&$query) {
			if ($query instanceof WP_Query) {
				$query->set("post_type", $this->post_type);
				$query->set("posts_per_page", $this->post_per_page);
				
				$meta_query = [
					'relation' => 'AND'
				];
				$tax_query = [
					'relation' => 'AND'
				];
				
				foreach ($this->default_filters as $filter) {
					$type = $filter["type"] ?? "post_meta";
					$filter_values = $filter["filter"] ?? [];
					
					if ($type === "post_meta") {
						$display = $filter_values["display"] ?? "";
						$meta_key = $filter_values["meta_key"] ?? "";
						$defaults_value = $filter_values["defaults"] ?? [];
						$defaults_value = array_filter($defaults_value, function ($default_value) {
							return $default_value !== null;
						});
						
						if ($display !== "" && $meta_key !== "") {
							if (in_array($display, ["select-multiple", "checkbox"])) {
								$meta_query[$meta_key] = [
									'key'     => $meta_key,
									'value'   => $defaults_value,
									'compare' => 'IN',
								];
								
							} else if ($display === "range") {
								// Do nothing, no default values
							} else {
								$default_value = $defaults_value[0] ?? "";
								if ($default_value !== "") {
									$meta_query[$meta_key] = [
										'key'     => $meta_key,
										'value'   => $defaults_value[0] ?? "",
										'compare' => '=',
									];
								}
							}
						}
					} else if ($type === "acf" && function_exists("get_field_object")) {
						$display = $filter_values["display"] ?? "";
						$acf_field = $filter_values["acf_field"] ?? "";
						$defaults_value = $filter_values["defaults"] ?? [];
						$defaults_value = array_filter($defaults_value, function ($default_value) {
							return $default_value !== null;
						});
						
						$acf_field_object = get_field_object($acf_field);
						if (!empty($acf_field_object)) {
							$field_name = $acf_field_object["name"] ?? "";
							
							if ($display !== "" && $field_name !== "") {
								if (in_array($display, ["select-multiple", "checkbox"])) {
									$meta_query[$acf_field] = [
										'key'     => $field_name,
										'value'   => $defaults_value,
										'compare' => 'IN',
									];
									
								} else if ($display === "range") {
									// Do nothing, no default values
								} else {
									$default_value = $defaults_value[0] ?? "";
									if ($default_value !== "") {
										$meta_query[$acf_field] = [
											'key'     => $field_name,
											'value'   => $defaults_value[0] ?? "",
											'compare' => '=',
										];
									}
								}
							}
						}
					} else if ($type === "taxonomy") {
						$display = $filter_values["display"] ?? "";
						$taxonomy = $filter_values["taxonomy"] ?? "";
						$defaults_value = $filter_values["defaults"] ?? [];
						$defaults_value = array_filter($defaults_value, function ($default_value) {
							return $default_value !== null;
						});
						
						if ($display !== "" && $taxonomy !== "" && !empty($defaults_value)) {
							if (in_array($display, ["select-multiple", "checkbox"])) {
								$tax_query[$taxonomy] = [
									'taxonomy' => $taxonomy,
									'field'    => 'term_id',
									'terms'    => $defaults_value,
									'operator' => 'IN',
								];
								
							} else if ($display === "range") {
								// Do nothing, no default values
							} else {
								$default_value = $defaults_value[0] ?? "";
								if ($default_value !== "") {
									$tax_query[$taxonomy] = [
										'taxonomy' => $taxonomy,
										'field'    => 'term_id',
										'terms'    => $defaults_value[0] ?? "",
										'operator' => 'IN',
									];
								}
							}
						}
					}
				}
				
				if (!empty($this->orders)) {
					$order_by_query = [];
					foreach ((array)($this->orders["orders_by"] ?? []) as $order_obj) {
						$order_array = (array)$order_obj; // stdclass to array
						
						$order = $order_array["order"] ?? "DESC";
						$order_by = $order_array["order_by"] ?? "";
						$meta_key = $order_array["meta_key"] ?? "";
						
						if (in_array($order_by, ["meta_value", "meta_value_num"]) && $meta_key !== "") {
							if (!isset($meta_query[$meta_key])) {
								$meta_query[$meta_key] = [
									"key" => $meta_key
								];
							}
							
							if ($order_by === "meta_value_num") {
								$meta_query[$meta_key]["type"] = "NUMERIC";
							}
							
							$order_by_query[$meta_key] = $order;
							
						} else {
							$order_by_query[$order_by] = $order;
						}
					}
					
					$query->set("orderby", $order_by_query);
				}
				
				$query->set("meta_query", $meta_query);
				$query->set("tax_query", $tax_query);
			}
		}
		
		public function setQuery(&$query, $filters) {
			if ($query instanceof WP_Query) {
				$query->set("post_type", $this->post_type);
				$query->set("posts_per_page", $this->post_per_page);
				
				$meta_query = [
					'relation' => 'AND'
				];
				$tax_query = [
					'relation' => 'AND'
				];
				
				$custom_orders_query = [];
				foreach ($this->default_filters as $default_filter) {
					$type = $default_filter["type"] ?? "post_meta";
					$filter_values = $default_filter["filter"] ?? [];
					
					if ($type === "post_meta" && isset($filters["infocob-crm-products"][$type])) {
						$filter = $filters["infocob-crm-products"][$type];
						
						$display = $filter_values["display"] ?? "";
						$meta_key = $filter_values["meta_key"] ?? "";
						
						if (isset($filter[$meta_key])) {
							$filter_value = $filter[$meta_key];
							
							if ($filter_value !== "__EMPTY__") {
								if ($display !== "" && $meta_key !== "") {
									if (in_array($display, ["select-multiple", "checkbox"])) {
										$meta_query[$meta_key] = [
											'key'     => $meta_key,
											'value'   => $filter_value,
											'compare' => 'IN',
										];
										
									} else if ($display === "range") {
										$value = [
											$filter_value["min"] ?? 0,
											$filter_value["max"] ?? 0,
										];
										
										$meta_query[$meta_key] = [
											'key'     => $meta_key,
											'value'   => $value,
											'type'    => 'NUMERIC',
											'compare' => 'BETWEEN',
										];
									} else {
										$meta_query[$meta_key] = [
											'key'     => $meta_key,
											'value'   => $filter_value,
											'compare' => '='
										];
									}
								}
							}
						}
						
					} else if ($type === "acf" && isset($filters["infocob-crm-products"][$type]) && function_exists("get_field_object")) {
						$filter = $filters["infocob-crm-products"][$type];
						$display = $filter_values["display"] ?? "";
						$acf_field = $filter_values["acf_field"] ?? "";
						
						$acf_field_object = get_field_object($acf_field);
						if (!empty($acf_field_object)) {
							$field_name = $acf_field_object["name"] ?? "";
							
							if (isset($filter[$acf_field]) && $field_name !== "") {
								$filter_value = $filter[$acf_field];
								
								if ($filter_value !== "__EMPTY__") {
									if ($display !== "" && $acf_field !== "") {
										if (in_array($display, ["select-multiple", "checkbox"])) {
											$meta_query[$acf_field] = [
												'key'     => $field_name,
												'value'   => $filter_value,
												'compare' => 'IN',
											];
											
										} else if ($display === "range") {
											$value = [
												floor($filter_value["min"] ?? 0),
												ceil($filter_value["max"] ?? 0),
											];
											
											$meta_query[$acf_field] = [
												'key'     => $field_name,
												'value'   => $value,
												'type'    => 'NUMERIC',
												'compare' => 'BETWEEN',
											];
										} else {
											$meta_query[$acf_field] = [
												'key'     => $field_name,
												'value'   => $filter_value,
												'compare' => '=',
											];
										}
									}
								}
							}
						}
					} else if ($type === "taxonomy" && isset($filters["infocob-crm-products"][$type])) {
						$filter = $filters["infocob-crm-products"][$type];
						
						$display = $filter_values["display"] ?? "";
						$taxonomy = $filter_values["taxonomy"] ?? "";
						
						if (isset($filter[$taxonomy])) {
							$filter_value = $filter[$taxonomy];
							
							if ($filter_value !== "__EMPTY__") {
								if ($display !== "" && $taxonomy !== "") {
									if (in_array($display, ["select-multiple", "checkbox"])) {
										$tax_query[$taxonomy] = [
											'taxonomy' => $taxonomy,
											'field'    => 'term_id',
											'terms'    => $filter_value,
											'operator' => 'IN',
										];
										
									} else if ($display === "range") {
										// Do nothing
									} else {
										$tax_query[$taxonomy] = [
											'taxonomy' => $taxonomy,
											'field'    => 'term_id',
											'terms'    => $filter_value,
											'operator' => 'IN',
										];
									}
								}
							}
						}
						
					} else if ($type === "order_by" && isset($filters["infocob-crm-products"][$type])) {
						$filter = $filters["infocob-crm-products"][$type];
						
						if (is_array($filter)) {
							foreach ($filter as $order_filter_base64) {
								
								if ($order_filter_base64 !== "__EMPTY__") {
									$order_filter = Tools::decodeConfig($order_filter_base64);
									
									$order = $order_filter["order"] ?? "ASC";
									$order_by = $order_filter["order_by"] ?? "";
									$meta_key = $order_filter["meta_key"] ?? "";
									
									if (in_array($order_by, ["meta_value", "meta_value_num"]) && $meta_key !== "") {
										if (!isset($meta_query[$meta_key])) {
											$meta_query[$meta_key] = [
												"key" => $meta_key
											];
										}
										
										if ($order_by === "meta_value_num") {
											$meta_query[$meta_key]["type"] = "NUMERIC";
										}
										
										$custom_orders_query[$meta_key] = $order;
										
									} else {
										$custom_orders_query[$order_by] = $order;
									}
								}
							}
						}
					}
				}
				
				if (!empty($this->orders) && empty($custom_orders_query)) {
					$order_by_query = [];
					foreach ((array)($this->orders["orders_by"] ?? []) as $order_obj) {
						$order_array = (array)$order_obj; // stdclass to array
						
						$order = $order_array["order"] ?? "DESC";
						$order_by = $order_array["order_by"] ?? "";
						$meta_key = $order_array["meta_key"] ?? "";
						
						if (in_array($order_by, ["meta_value", "meta_value_num"]) && $meta_key !== "") {
							if (!isset($meta_query[$meta_key])) {
								$meta_query[$meta_key] = [
									"key" => $meta_key
								];
							}
							
							if ($order_by === "meta_value_num") {
								$meta_query[$meta_key]["type"] = "NUMERIC";
							}
							
							$order_by_query[$meta_key] = $order;
							
						} else {
							$order_by_query[$order_by] = $order;
						}
					}
					
					$query->set("orderby", $order_by_query);
				} else {
					$query->set("orderby", $custom_orders_query);
				}
				
				$query->set("meta_query", $meta_query);
				$query->set("tax_query", $tax_query);
			}
		}
		
		public function getProducts() {
			return $this->products;
		}
		
		public function get() {
			$html = '
				<div class="infocob-crm-products products-list" data-post_id="' . $this->post_id . '" data-config="">
				
				</div>
			';
			
			return $html;
		}
		
		/**
		 * @return mixed|string
		 */
		public function getPostType() {
			return $this->post_type;
		}
		
	}
