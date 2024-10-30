import {Utils} from "../../../../Utils.js";

export class OrderByUtils {
	static triggerUpdate() {
		jQuery("#products-order-by").trigger("update");

		Utils.initTooltips();
	}

}
