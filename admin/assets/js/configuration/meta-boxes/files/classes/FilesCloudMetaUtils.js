import {Utils} from "../../../../Utils.js";

export class FilesCloudMetaUtils {
	static triggerUpdate() {
		jQuery("#files-cloud-meta").trigger("update");

		Utils.initTooltips();
	}

}
