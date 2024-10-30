import {PostMetaManager} from "./post/PostMetaManager.js";
import {PostACFManager} from "./post/PostACFManager.js";
import {PostYoastManager} from "./post/PostYoastManager.js";
import {PostWoocommerceManager} from "./post/PostWoocommerceManager.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

jQuery(document).ready(function($) {
	let postMetaManager = new PostMetaManager();
	postMetaManager.load(jQuery("#post-meta > input[type='hidden'][name='post-meta']").val());

	postMetaManager.render();

	if(jQuery("#post-yoast").length) {
		let postYoastManager = new PostYoastManager();
		postYoastManager.load(jQuery("#post-yoast > input[type='hidden'][name='post-yoast']").val());

		postYoastManager.render();
	}

	if(jQuery("#post-acf").length) {
		let postACFManager = new PostACFManager();
		postACFManager.load(jQuery("#post-acf > input[type='hidden'][name='post-acf']").val());

		postACFManager.render();
	}

	if(jQuery("#post-woocommerce").length) {
		let postWoocommerce = new PostWoocommerceManager();
		postWoocommerce.load(jQuery("#post-woocommerce > input[type='hidden'][name='post-woocommerce']").val());

		postWoocommerce.render();
	}
});
