import {Utils} from "../../../../Utils.js";

export class FilterUtils {
	static triggerUpdate() {
		jQuery("#right-filters").trigger("update");

		Utils.initTooltips();
	}

}
