import {Utils} from "../../../../Utils.js";

export class FilterUtils {
	static triggerUpdate() {
		jQuery("#left-filters").trigger("update");

		Utils.initTooltips();
	}

}
