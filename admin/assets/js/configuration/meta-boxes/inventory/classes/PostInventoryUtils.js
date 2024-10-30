import {Utils} from "../../../../Utils.js";

export class PostInventoryUtils {
	static triggerUpdate() {
		jQuery("#post-inventory").trigger("update");

		Utils.initTooltips();
	}

}
