import {Utils} from "../../../../Utils.js";

export class FilterUtils {
	static triggerUpdate() {
		jQuery("#top-filters").trigger("update");

		Utils.initTooltips();
	}

}
