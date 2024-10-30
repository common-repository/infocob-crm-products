<?php
	/**
	 * Plugin Name: Infocob CRM Products
	 * Description: Link your Infocob products with your Wordpress.
	 * Version: 1.2.2
	 * Author: Infocob web
	 * Author URI: https://www.infocob-web.com/
	 * License: GPL3
	 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
	 * Text Domain: infocob-crm-products
	 * Domain Path: /languages
	 */
	
	namespace Infocob\CRM\Products;
	
	use Infocob\CRM\Products\Admin\Controllers\InfocobCRMProducts;
	
	require_once 'vendor/autoload.php';
	require_once(ABSPATH . 'wp-includes/pluggable.php');
	
	define("INFOCOB_CRM_PRODUCTS_ASSETS_VERSION", "1.1");
	define("INFOCOB_CRM_PRODUCTS_UPGRADE_VERSION", 2);
	
	define('ROOT_INFOCOB_CRM_PRODUCTS_FULL_PATH', __FILE__);
	define('ROOT_INFOCOB_CRM_PRODUCTS_DIR_PATH', plugin_dir_path(__FILE__));
	define('ROOT_INFOCOB_CRM_PRODUCTS_DIR_URL', plugin_dir_url(__FILE__));
	define('INFOCOB_CRM_PRODUCTS_BASENAME', plugin_basename(__FILE__));
	define("INFOCOB_CRM_PRODUCTS_HOSTNAME", parse_url(home_url(), PHP_URL_HOST)); // domain
	
	$infocobCRMProducts = new InfocobCRMProducts();
	$infocobCRMProducts->initialize();
