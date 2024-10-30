import {MappingsManager} from "./mappings/MappingsManager.js";

jQuery(document).ready(($) => {
	jQuery("#meta-box-mappings").on("loaded", () => {
		let mappings_element = $("#mappings > input[type='hidden'][name='mappings']");
		if (mappings_element.length) {
			let mappingsManager = new MappingsManager();
			mappingsManager.load($(mappings_element).val());

			mappingsManager.render();
		}
	});
});
