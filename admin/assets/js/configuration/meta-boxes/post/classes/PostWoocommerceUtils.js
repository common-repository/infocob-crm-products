import {Utils} from "../../../../Utils.js";

export class PostWoocommerceUtils {
	static triggerUpdate() {
		jQuery("#post-woocommerce").trigger("update");

		Utils.initTooltips();
	}

}
