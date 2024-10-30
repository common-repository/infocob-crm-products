import {PostWoocommerce}      from "./classes/PostWoocommerce.js";
import {PostWoocommerceUtils} from "./classes/PostWoocommerceUtils.js";
import {Utils}         from "../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class PostWoocommerceManager {

	constructor() {
		this._post_meta = [];
	}

	saveEvent() {
		jQuery("#post-woocommerce").off("update").on("update", () => {
			let base64Json = Utils.encodeConfig(this);
			jQuery("#post-woocommerce > input[name='post-woocommerce']").val(base64Json);
		});
	}

	load(configBase64) {
		jQuery("#post-woocommerce .icp-loader").addClass("active");

		let config = Utils.decodeConfig(configBase64);

		if(config.post_meta !== undefined && config.post_meta.length > 0) {
			config.post_meta.forEach((config_post_meta) => {
				let post_meta = new PostWoocommerce();
				post_meta.load(config_post_meta);
				this.addPostWoocommerce(post_meta);
			});
		}
	}

	render() {
		this.toHTML().then((html) => {
			jQuery("#post-woocommerce > .content-post-meta").html(html);

			this.applyEvents();

			let module_infocob = jQuery("#infocob-type-produit").val();
			Utils.tributeFieldsInfocob(jQuery("#post-woocommerce > .content-post-meta input.autocomplete-fields-infocob"), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "FAMILLETYPEINVENTAIRE"] : [module_infocob]);

			jQuery("#post-woocommerce .icp-loader").removeClass("active");

			Utils.initTooltips();
		});
	}

	applyEvents() {
		this.post_meta.forEach((post_meta, index) => {
			if(post_meta instanceof PostWoocommerce) {
				post_meta.applyEvents();

				jQuery(post_meta).off("del-post_meta").on("del-post_meta", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostWoocommerceById(id);
					}
				});
			}
		});

		let addPostWoocommerce = jQuery("#post-woocommerce > div.actions-container > button.add-post_meta");
		jQuery(addPostWoocommerce).off("click").on("click", (event) => {
			event.preventDefault();

			let post_meta = new PostWoocommerce();
			post_meta.toHTML().then((post_meta_html) => {
				jQuery("#post-woocommerce > .content-post-meta").append(post_meta_html);
				post_meta.applyEvents();
				this.addPostWoocommerce(post_meta);

				jQuery(post_meta).off("del-post_meta").on("del-post_meta", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostWoocommerceById(id);
						PostWoocommerceUtils.triggerUpdate();
					}
				});

				let module_infocob = jQuery("#infocob-type-produit").val();
				Utils.tributeFieldsInfocob(jQuery(`#post-meta > .content-post-meta .post-meta-container[data-id="${post_meta.id}"] input.autocomplete-fields-infocob`), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "FAMILLETYPEINVENTAIRE"] : [module_infocob]);
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
				if(post_meta instanceof PostWoocommerce) {
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

	addPostWoocommerce(value) {
		if(value instanceof PostWoocommerce) {
			this.post_meta.push(value);

			PostWoocommerceUtils.triggerUpdate();
		} else {
			console.error(_x("Unable to define value", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products'));
		}
	}

	delPostWoocommerce(value) {
		if(value instanceof PostWoocommerce) {
			for(var i = 0; i < this.post_meta.length; i++) {
				if(this.post_meta[i] === value) {
					this.post_meta.splice(i, 1);
				}
			}

			PostWoocommerceUtils.triggerUpdate();
		}
	}

	delPostWoocommerceById(id) {
		this.post_meta.forEach((post_meta, index) => {
			if(post_meta instanceof PostWoocommerce) {
				if(post_meta.id === id) {
					this.post_meta.splice(index, 1);
				}
			}
		});

		PostWoocommerceUtils.triggerUpdate();
	}

	get post_meta() {
		return this._post_meta;
	}

	set post_meta(value) {
		this._post_meta = value;

		PostWoocommerceUtils.triggerUpdate();
	}

}
