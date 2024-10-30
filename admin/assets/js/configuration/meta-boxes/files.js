import {FilesCloudMetaManager} from "./files/FilesCloudMetaManager.js";
import {PhotosCloudMetaManager} from "./files/PhotosCloudMetaManager.js";
import {Utils} from "../../Utils.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

jQuery(document).ready(function($) {
	let filesCloudMetaManager = new FilesCloudMetaManager();
	filesCloudMetaManager.load(jQuery("#files-cloud-meta > input[type='hidden'][name='files-cloud-meta']").val());
	filesCloudMetaManager.render();

	let photosCloudMetaManager = new PhotosCloudMetaManager();
	photosCloudMetaManager.load(jQuery("#photos-cloud-meta > input[type='hidden'][name='photos-cloud-meta']").val());
	photosCloudMetaManager.render();

	let files_use_local = $("#files-use-local");
	let files_use_cloud = $("#files-use-cloud");

	$(files_use_local).on("change", (event) => {
		let input = event.currentTarget;
		let table_cloud_files = $("table.cloud-files");
		let table_local_files = $("table.local-files");

		$(table_cloud_files).removeClass("disabled");
		$(table_local_files).removeClass("disabled");

		if($(input).prop("checked")) {
			$(files_use_cloud).prop("checked", false);
			$(table_cloud_files).addClass("disabled");
		}
	});

	$(files_use_cloud).on("change", (event) => {
		let input = event.currentTarget;
		let table_cloud_files = $("table.cloud-files");
		let table_local_files = $("table.local-files");

		$(table_cloud_files).removeClass("disabled");
		$(table_local_files).removeClass("disabled");

		if($(input).prop("checked")) {
			$(files_use_local).prop("checked", false);
			$(table_local_files).addClass("disabled");
		}
	});

	$("#meta-box-files .alternative-text").on("keyup keypress blur change cut paste", (event) => {
		let element = event.currentTarget;
		let value = jQuery(element).val();
		value = value.toLocaleLowerCase();

		jQuery(element).val(value);
	});

	let module_infocob = jQuery("#infocob-type-produit").val();
	Utils.tributeFieldsInfocob(jQuery(`#meta-box-files input.autocomplete-fields-infocob`), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "CLOUDFICHIER", "FAMILLETYPEINVENTAIRE"] : [module_infocob, "CLOUDFICHIER"]);

	jQuery('#meta-box-files select[multiple=multiple]').multipleSelect("refresh");
	jQuery('#meta-box-files select.search').multipleSelect({
		filter: true,
		formatSelectAll: () => {
			return _x("[Select all]", "JS multipleSelect - formatSelectAll", 'infocob-crm-products');
		},
		formatAllSelected: () => {
			return _x('All selected', "JS multipleSelect - formatSelectAll", 'infocob-crm-products');
		},
		formatCountSelected: (count, total) => {
			return sprintf(_x('%s of %s selected', "JS multipleSelect - formatSelectAll", 'infocob-crm-products'), count, total);
		},
		formatNoMatchesFound: () => {
			return _x('No matches found', "JS multipleSelect - formatSelectAll", 'infocob-crm-products');
		},
	});

	Utils.initTooltips();
});
