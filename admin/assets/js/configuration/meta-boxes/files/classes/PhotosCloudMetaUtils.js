import {Utils} from "../../../../Utils.js";

export class PhotosCloudMetaUtils {
	static triggerUpdate() {
		jQuery("#photos-cloud-meta").trigger("update");

		Utils.initTooltips();
	}

}
