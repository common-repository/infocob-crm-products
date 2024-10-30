import {FilesCloudMeta}      from "./classes/FilesCloudMeta.js";
import {FilesCloudMetaUtils} from "./classes/FilesCloudMetaUtils.js";
import {Utils}         from "../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilesCloudMetaManager {

	constructor() {
		this._post_meta = [];
	}

	saveEvent() {
		jQuery("#files-cloud-meta").off("update").on("update", () => {
			let base64Json = Utils.encodeConfig(this);
			jQuery("#files-cloud-meta > input[name='files-cloud-meta']").val(base64Json);
		});
	}

	load(configBase64) {
		jQuery("#files-cloud-meta .icp-loader").addClass("active");

		let config = Utils.decodeConfig(configBase64);

		if(config.post_meta !== undefined && config.post_meta.length > 0) {
			config.post_meta.forEach((config_post_meta) => {
				let post_meta = new FilesCloudMeta();
				post_meta.load(config_post_meta);
				this.addPostMeta(post_meta);
			});
		}
	}

	render() {
		this.toHTML().then((html) => {
			jQuery("#files-cloud-meta > .content-post-meta").html(html);

			this.applyEvents();

			let module_infocob = jQuery("#infocob-type-produit").val();
			Utils.tributeFieldsInfocob(jQuery("#files-cloud-meta > .content-post-meta input.autocomplete-fields-infocob"), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "CLOUDFICHIER", "FAMILLETYPEINVENTAIRE"] : [module_infocob, "CLOUDFICHIER"]);

			jQuery("#files-cloud-meta .icp-loader").removeClass("active");

			Utils.initTooltips();
		});
	}

	applyEvents() {
		this.post_meta.forEach((post_meta, index) => {
			if(post_meta instanceof FilesCloudMeta) {
				post_meta.applyEvents();

				jQuery(post_meta).off("del-post_meta").on("del-post_meta", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostMetaById(id);
					}
				});
			}
		});

		let addPostMeta = jQuery("#files-cloud-meta > div.actions-container > button.add-post_meta");
		jQuery(addPostMeta).off("click").on("click", (event) => {
			event.preventDefault();

			let post_meta = new FilesCloudMeta();
			post_meta.toHTML().then((post_meta_html) => {
				jQuery("#files-cloud-meta > .content-post-meta").append(post_meta_html);
				post_meta.applyEvents();
				this.addPostMeta(post_meta);

				jQuery(post_meta).off("del-post_meta").on("del-post_meta", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostMetaById(id);
						FilesCloudMetaUtils.triggerUpdate();
					}
				});

				let module_infocob = jQuery("#infocob-type-produit").val();
				Utils.tributeFieldsInfocob(jQuery(`#files-cloud-meta > .content-post-meta .post-meta-container[data-id="${post_meta.id}"] input.autocomplete-fields-infocob`), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "CLOUDFICHIER", "FAMILLETYPEINVENTAIRE"] : [module_infocob, "CLOUDFICHIER"]);
			});
		});

		this.saveEvent();
	}

	toJSON() {
		return Object.assign({}, {
			post_meta: this.post_meta,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let promises = [];

			this.post_meta.forEach((post_meta) => {
				if(post_meta instanceof FilesCloudMeta) {
					promises.push(post_meta.toHTML());
				}
			});

			if(promises.length > 0) {
				Promise.all(promises).then((responses) => {
					let post_meta_html = "";
					responses.forEach((html) => {
						post_meta_html += html;
					});

					resolve(post_meta_html);
				});
			} else {
				resolve();
			}
		});
	}

	addPostMeta(value) {
		if(value instanceof FilesCloudMeta) {
			this.post_meta.push(value);

			FilesCloudMetaUtils.triggerUpdate();
		} else {
			console.error(_x("Unable to define value", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products'));
		}
	}

	delPostMeta(value) {
		if(value instanceof FilesCloudMeta) {
			for(var i = 0; i < this.post_meta.length; i++) {
				if(this.post_meta[i] === value) {
					this.post_meta.splice(i, 1);
				}
			}

			FilesCloudMetaUtils.triggerUpdate();
		}
	}

	delPostMetaById(id) {
		this.post_meta.forEach((post_meta, index) => {
			if(post_meta instanceof FilesCloudMeta) {
				if(post_meta.id === id) {
					this.post_meta.splice(index, 1);
				}
			}
		});

		FilesCloudMetaUtils.triggerUpdate();
	}

	get post_meta() {
		return this._post_meta;
	}

	set post_meta(value) {
		this._post_meta = value;

		FilesCloudMetaUtils.triggerUpdate();
	}

}
