import {Utils} from "../../../../Utils.js";

export class PostACFUtils {
	static triggerUpdate() {
		jQuery("#post-acf").trigger("update");

		Utils.initTooltips();
	}

}
