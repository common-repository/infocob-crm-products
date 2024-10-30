import {Utils} from "../../../../Utils.js";
import {PostWoocommerceUtils} from "./PostWoocommerceUtils.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

export class PostWoocommerce {
	static id = 0;

	constructor() {
		this._id = PostWoocommerce.id;
		this._post_type = "product";
		this._meta_key = "_sku";
		this._meta_value = "";
		this._langs = [];
		this._update = true;

		let langsBase64 = jQuery("#post-woocommerce").data("langs");
		this._langs = Utils.decodeConfig(langsBase64);

		PostWoocommerce.id++;
	}

	load(post_meta) {
		this.post_type = "product";
		this.meta_key = post_meta.meta_key ?? "";
		this.meta_value = post_meta.meta_value ?? "";
		this.langs = post_meta.langs ?? [];
		this.update = post_meta.update ?? true;
	}

	toJSON() {
		return Object.assign({}, {
			post_type: this.post_type,
			meta_key: this.meta_key,
			meta_value: this.meta_value,
			langs: this.langs,
			update: this.update,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let promises = [];

			let langs_options = ``;
			promises.push(Utils.getLangs("product").then((languages) => {
				if(languages !== false) {
					for(const [index, lang] of Object.entries(languages)) {
						langs_options += `<option value="${ lang }" ${ (this.langs.includes(lang)) ? `selected` : `` }>${ lang }</option>`;
					}
				} else {
					this.langs = [];
				}
			}));

			let texts = {};
			let post_woocommerce = jQuery("#post-woocommerce");
			let weight_unit = jQuery(post_woocommerce).data("weight_unit");
			let dimension_unit = jQuery(post_woocommerce).data("dimension_unit");
			promises.push(Utils.getTranslations([
				{
					text: "Product",
					"text-domain": "woocommerce"
				},{
					text: "SKU",
					"text-domain": "woocommerce"
				},{
					text: "Regular price",
					"text-domain": "woocommerce"
				},{
					text: "Sale price",
					"text-domain": "woocommerce"
				},{
					text: "Weight (%s)",
					"text-domain": "woocommerce",
					args: [weight_unit]
				},{
					text: "Length (%s)",
					"text-domain": "woocommerce",
					args: [dimension_unit]
				},{
					text: "Width (%s)",
					"text-domain": "woocommerce",
					args: [dimension_unit]
				},{
					text: "Height (%s)",
					"text-domain": "woocommerce",
					args: [dimension_unit]
				},{
					text: "Description",
					"text-domain": "woocommerce",
				},
			]).then((translations) => {
				if(translations !== false) {
					for(const [text, translation] of Object.entries(translations)) {
						texts[text] = translation;
					}
				}
			}));

			Promise.allSettled(promises).then(() => {
				resolve(`
					<div class="post-meta-container" data-id="${ this.id }">
						<div class="post-type">
							<input type="text" value="${ texts["Product"] ?? "Product" }" readonly>
						</div>
						<div class="meta">
							<div class="meta-key">
								<select data-name="meta_key" data-tippy-content="${ _x("Meta key", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }">
									<option value="_sku" ${(this.meta_key === "_sku") ? "selected" : ""}>${ texts["SKU"] ?? "SKU" }</option>
									<option value="_regular_price" ${(this.meta_key === "_regular_price") ? "selected" : ""}>${ texts["Regular price"] ?? "Regular price" }</option>
									<option value="_sale_price" ${(this.meta_key === "_sale_price") ? "selected" : ""}>${ texts["Sale price"] ?? "Sale price" }</option>
									<option value="_weight" ${(this.meta_key === "_weight") ? "selected" : ""}>${ texts["Weight (%s)"] ?? "Weight (%s)" }</option>
									<option value="_length" ${(this.meta_key === "_length") ? "selected" : ""}>${ texts["Length (%s)"] ?? "Length (%s)" }</option>
									<option value="_width" ${(this.meta_key === "_width") ? "selected" : ""}>${ texts["Width (%s)"] ?? "Width (%s)" }</option>
									<option value="_height" ${(this.meta_key === "_height") ? "selected" : ""}>${ texts["Height (%s)"] ?? "Height (%s)" }</option>
									<option value="_variation_description" ${(this.meta_key === "_variation_description") ? "selected" : ""}>${ texts["Description"] ?? "Description" }</option>
								</select>
							</div>
							<div class="meta-value">
								<input data-name="meta_value" type="text" value="${ this.meta_value }" class="autocomplete-fields-infocob" data-tippy-content="${ _x("Meta value", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }" placeholder="${ _x("Meta value", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }">
							</div>
							<div class="langs ${ (langs_options === "") ? "invisible" : "" }">
								<select data-name="langs" multiple="multiple" data-tippy-content="${ _x("Languages", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }">
									${ langs_options }
								</select>
							</div>
							<div class="update">
								<label>
									<input data-name="update" type="checkbox" ${ (this.update) ? "checked" : "" }>
									${ _x("Update", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }
								</label>
							</div>
						</div>
						<div class="actions-container">
							<div class="post-meta">
								<button type="button" class="del-post_meta">${ _x("Delete", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }</button>
							</div>
						</div>
					</div>
				`);
			})
		});
	}

	applyEvents() {
		let post_meta_element = this.getPostMetaElement();

		if(post_meta_element.length) {
			let meta_key = jQuery(post_meta_element).find("select[data-name='meta_key']");
			jQuery(meta_key).off("change").on("change", (event) => {
				let options_selected = jQuery(event.currentTarget).find("option:selected");
				this.meta_key = jQuery(options_selected).val();
			});

			let meta_value = jQuery(post_meta_element).find("input[data-name='meta_value']");
			jQuery(meta_value).off("input").on("input", (event) => {
				this.meta_value = jQuery(event.currentTarget).val();
			});

			let langs = jQuery(post_meta_element).find("select[data-name='langs']");
			jQuery(langs).off("change").on("change", (event) => {
				let options_selected = jQuery(event.currentTarget).find("option:selected");
				let langs = [];
				jQuery(options_selected).each((index, option) => {
					langs.push(jQuery(option).val());
				});
				this.langs = langs;

				jQuery(event.currentTarget).multipleSelect("refresh");
			});

			let update = jQuery(post_meta_element).find("input[data-name='update']");
			jQuery(update).off("change").on("change", (event) => {
				this.update = jQuery(event.currentTarget).prop("checked");
			});

			let delPostMeta = jQuery(post_meta_element).find(".actions-container > .post-meta > .del-post_meta");
			jQuery(delPostMeta).off("click").on("click", (event) => {
				event.preventDefault();
				jQuery(post_meta_element).remove();
				jQuery(this).trigger("del-post_meta", {
					id: this.id
				});
			});

			jQuery('#post-woocommerce select[multiple=multiple]').multipleSelect("refresh");
		}
	}

	getPostMetaElement() {
		return jQuery(`#post-woocommerce .post-meta-container[data-id='${ this.id }']`).first();
	}

	/*
		Getters & Setters
	 */

	get id() {
		return parseInt(this._id, 10);
	}

	get post_type() {
		return this._post_type;
	}

	set post_type(value) {
		this._post_type = String(value);
		PostWoocommerceUtils.triggerUpdate();
	}

	get meta_key() {
		return this._meta_key;
	}

	set meta_key(value) {
		this._meta_key = String(value);
		PostWoocommerceUtils.triggerUpdate();
	}

	get meta_value() {
		return this._meta_value;
	}

	set meta_value(value) {
		this._meta_value = String(value);
		PostWoocommerceUtils.triggerUpdate();
	}

	get langs() {
		return this._langs;
	}

	set langs(value) {
		if(Array.isArray(value)) {
			value = value.map((val) => {
				return String(val);
			});

			this._langs = value;
		} else {
			this._langs = [];
		}
		PostWoocommerceUtils.triggerUpdate();
	}

	get update() {
		return Boolean(this._update);
	}

	set update(value) {
		this._update = Boolean(value);
		PostWoocommerceUtils.triggerUpdate();
	}
}
