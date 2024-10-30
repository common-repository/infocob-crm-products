import {PostACF}      from "./classes/PostACF.js";
import {PostACFUtils} from "./classes/PostACFUtils.js";
import {Utils}         from "../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class PostACFManager {

	constructor() {
		this._post_acf = [];
	}

	saveEvent() {
		jQuery("#post-acf").off("update").on("update", () => {
			let base64Json = Utils.encodeConfig(this);
			jQuery("#post-acf > input[name='post-acf']").val(base64Json);
		});
	}

	load(configBase64) {
		jQuery("#post-acf .icp-loader").addClass("active");

		let config = Utils.decodeConfig(configBase64);

		if(config.post_acf !== undefined && config.post_acf.length > 0) {
			config.post_acf.forEach((config_post_acf) => {
				let post_acf = new PostACF();
				post_acf.load(config_post_acf);
				this.addPostACF(post_acf);
			});
		}
	}

	render() {
		this.toHTML().then((html) => {
			jQuery("#post-acf > .content-post-acf").html(html);

			this.applyEvents();

			let module_infocob = jQuery("#infocob-type-produit").val();
			Utils.tributeFieldsInfocob(jQuery("#post-acf > .content-post-meta input.autocomplete-fields-infocob"), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "FAMILLETYPEINVENTAIRE"] : [module_infocob]);

			jQuery("#post-acf .icp-loader").removeClass("active");

			Utils.initTooltips();
		});
	}

	applyEvents() {
		this.post_acf.forEach((post_acf, index) => {
			if(post_acf instanceof PostACF) {
				post_acf.applyEvents();

				jQuery(post_acf).off("del-post_acf").on("del-post_acf", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostACFById(id);
					}
				});

				let module_infocob = jQuery("#infocob-type-produit").val();
				Utils.tributeFieldsInfocob(jQuery(`#post-acf > .content-post-acf .post-acf-container[data-id="${post_acf.id}"] input.autocomplete-fields-infocob`), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "FAMILLETYPEINVENTAIRE"] : [module_infocob]);
			}
		});

		let addPostACF = jQuery("#post-acf > div.actions-container > button.add-post_acf");
		jQuery(addPostACF).off("click").on("click", (event) => {
			event.preventDefault();

			let post_acf = new PostACF();
			post_acf.toHTML().then((post_acf_html) => {
				jQuery("#post-acf > .content-post-acf").append(post_acf_html);
				post_acf.applyEvents();
				this.addPostACF(post_acf);

				jQuery(post_acf).off("del-post_acf").on("del-post_acf", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostACFById(id);
						PostACFUtils.triggerUpdate();
					}
				});

				let module_infocob = jQuery("#infocob-type-produit").val();
				Utils.tributeFieldsInfocob(jQuery(`#post-acf > .content-post-acf .post-acf-container[data-id="${post_acf.id}"] input.autocomplete-fields-infocob`), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "FAMILLETYPEINVENTAIRE"] : [module_infocob]);
			});
		});

		this.saveEvent();

		Utils.initTooltips();
	}

	toJSON() {
		return Object.assign({}, {
			post_acf: this.post_acf,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let promises = [];

			this.post_acf.forEach((post_acf) => {
				if(post_acf instanceof PostACF) {
					promises.push(post_acf.toHTML());
				}
			});

			if(promises.length > 0) {
				Promise.all(promises).then((responses) => {
					let post_acf_html = "";
					responses.forEach((html) => {
						post_acf_html += html;
					});

					resolve(post_acf_html);
				});
			} else {
				resolve();
			}
		});
	}

	addPostACF(value) {
		if(value instanceof PostACF) {
			this.post_acf.push(value);

			PostACFUtils.triggerUpdate();
		} else {
			console.error(_x("Unable to define value", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products'));
		}
	}

	delPostACF(value) {
		if(value instanceof PostACF) {
			for(var i = 0; i < this.post_acf.length; i++) {
				if(this.post_acf[i] === value) {
					this.post_acf.splice(i, 1);
				}
			}

			PostACFUtils.triggerUpdate();
		}
	}

	delPostACFById(id) {
		this.post_acf.forEach((post_acf, index) => {
			if(post_acf instanceof PostACF) {
				if(post_acf.id === id) {
					this.post_acf.splice(index, 1);
				}
			}
		});

		PostACFUtils.triggerUpdate();
	}

	get post_acf() {
		return this._post_acf;
	}

	set post_acf(value) {
		this._post_acf = value;

		PostACFUtils.triggerUpdate();
	}

}
