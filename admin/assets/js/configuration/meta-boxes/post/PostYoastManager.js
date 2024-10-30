import {PostYoast}      from "./classes/PostYoast.js";
import {PostYoastUtils} from "./classes/PostYoastUtils.js";
import {Utils}         from "../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class PostYoastManager {

	constructor() {
		this._post_meta = [];
	}

	saveEvent() {
		jQuery("#post-yoast").off("update").on("update", () => {
			let base64Json = Utils.encodeConfig(this);
			jQuery("#post-yoast > input[name='post-yoast']").val(base64Json);
		});
	}

	load(configBase64) {
		jQuery("#post-yoast .icp-loader").addClass("active");

		let config = Utils.decodeConfig(configBase64);

		if(config.post_meta !== undefined && config.post_meta.length > 0) {
			config.post_meta.forEach((config_post_meta) => {
				let post_meta = new PostYoast();
				post_meta.load(config_post_meta);
				this.addPostYoast(post_meta);
			});
		}
	}

	render() {
		this.toHTML().then((html) => {
			jQuery("#post-yoast > .content-post-meta").html(html);

			this.applyEvents();

			let module_infocob = jQuery("#infocob-type-produit").val();
			Utils.tributeFieldsInfocob(jQuery("#post-yoast > .content-post-meta input.autocomplete-fields-infocob"), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "FAMILLETYPEINVENTAIRE"] : [module_infocob]);

			jQuery("#post-yoast .icp-loader").removeClass("active");

			Utils.initTooltips();
		});
	}

	applyEvents() {
		this.post_meta.forEach((post_meta, index) => {
			if(post_meta instanceof PostYoast) {
				post_meta.applyEvents();

				jQuery(post_meta).off("del-post_meta").on("del-post_meta", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostYoastById(id);
					}
				});
			}
		});

		let addPostYoast = jQuery("#post-yoast > div.actions-container > button.add-post_meta");
		jQuery(addPostYoast).off("click").on("click", (event) => {
			event.preventDefault();

			let post_meta = new PostYoast();
			post_meta.toHTML().then((post_meta_html) => {
				jQuery("#post-yoast > .content-post-meta").append(post_meta_html);
				post_meta.applyEvents();
				this.addPostYoast(post_meta);

				jQuery(post_meta).off("del-post_meta").on("del-post_meta", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostYoastById(id);
						PostYoastUtils.triggerUpdate();
					}
				});

				let module_infocob = jQuery("#infocob-type-produit").val();
				Utils.tributeFieldsInfocob(jQuery(`#post-yoast > .content-post-meta .post-meta-container[data-id="${post_meta.id}"] input.autocomplete-fields-infocob`), [], (module_infocob === "TYPEINVENTAIREPRODUIT") ? [module_infocob, "FAMILLETYPEINVENTAIRE"] : [module_infocob]);
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
				if(post_meta instanceof PostYoast) {
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

	addPostYoast(value) {
		if(value instanceof PostYoast) {
			this.post_meta.push(value);

			PostYoastUtils.triggerUpdate();
		} else {
			console.error(_x("Unable to define value", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products'));
		}
	}

	delPostYoast(value) {
		if(value instanceof PostYoast) {
			for(var i = 0; i < this.post_meta.length; i++) {
				if(this.post_meta[i] === value) {
					this.post_meta.splice(i, 1);
				}
			}

			PostYoastUtils.triggerUpdate();
		}
	}

	delPostYoastById(id) {
		this.post_meta.forEach((post_meta, index) => {
			if(post_meta instanceof PostYoast) {
				if(post_meta.id === id) {
					this.post_meta.splice(index, 1);
				}
			}
		});

		PostYoastUtils.triggerUpdate();
	}

	get post_meta() {
		return this._post_meta;
	}

	set post_meta(value) {
		this._post_meta = value;

		PostYoastUtils.triggerUpdate();
	}

}
