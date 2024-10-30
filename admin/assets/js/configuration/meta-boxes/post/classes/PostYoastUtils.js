import {Utils} from "../../../../Utils.js";

export class PostYoastUtils {
	static triggerUpdate() {
		jQuery("#post-yoast").trigger("update");

		Utils.initTooltips();
	}

}
