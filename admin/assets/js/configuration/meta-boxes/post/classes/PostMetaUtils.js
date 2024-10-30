import {Utils} from "../../../../Utils.js";

export class PostMetaUtils {
	static triggerUpdate() {
		jQuery("#post-meta").trigger("update");

		Utils.initTooltips();
	}

}
