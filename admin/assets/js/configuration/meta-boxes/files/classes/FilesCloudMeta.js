import {Utils} from "../../../../Utils.js";
import {FilesCloudMetaUtils} from "./FilesCloudMetaUtils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilesCloudMeta {
	static id = 0;

	constructor() {
		this._id = FilesCloudMeta.id;
		this._post_type = "";
		this._meta_key = "";
		this._meta_value = "";
		this._langs = [];
		this._update = true;

		let langsBase64 = jQuery("#files-cloud-meta").data("langs");
		this._langs = Utils.decodeConfig(langsBase64);

		FilesCloudMeta.id++;
	}

	load(post_meta) {
		this.post_type = post_meta.post_type ?? "";
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

			let post_type_html = `<select data-name="post_type" data-tippy-content="${ _x("Post type", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }"><option value=""></option></select>`;
			promises.push(Utils.getPostTypes().then((post_types) => {
				let options = `<option value=""></option>`;
				for(const [index, field] of Object.entries(post_types)) {
					options += `<option value="${ field.name }" ${ (this.post_type === field.name) ? `selected` : `` }>${ field.label } [${ field.name }]</option>`;
				}

				post_type_html = `
					<select data-name="post_type" data-tippy-content="${ _x("Post type", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }">
						${ options }
					</select>
				`;
			}));

			let langs_options = ``;
			promises.push(Utils.getLangs(this.post_type).then((languages) => {
				if(languages !== false) {
					for(const [index, lang] of Object.entries(languages)) {
						langs_options += `<option value="${ lang }" ${ (this.langs.includes(lang)) ? `selected` : `` }>${ lang }</option>`;
					}
				} else {
					this.langs = [];
				}
			}));

			let meta_key_list_html = ``;
			promises.push(Utils.getPostMetaValues(this.post_type, []).then((response) => {
				let meta_values = response;
				let options = ``;

				let keys = [];
				for (const [key, value] of Object.entries(meta_values)) {
					if(!keys.includes(key)) {
						options += `<option value="${key}"></option>`;
						keys.push(keys);
					}
				}

				meta_key_list_html = options;
			}));

			Promise.allSettled(promises).then(() => {
				resolve(`
					<div class="post-meta-container" data-id="${ this.id }">
						<div class="post-type">
							${ post_type_html }
						</div>
						<div class="meta">
							<div class="meta-key">
								<input data-name="meta_key" type="text" value="${ this.meta_key }" list="list-files-cloud-${ this.id }-${ this.meta_key }" autocomplete="off" placeholder="${ _x("Meta key", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }" data-tippy-content="${ _x("Add a dot before the meta key name to set properties of the post object (ex : .post_title)", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }">
								<datalist id="list-files-cloud-${ this.id }-${ this.meta_key }">
									${ meta_key_list_html }
								</datalist>
							</div>
							<div class="meta-value">
								<input data-name="meta_value" type="text" value="${ this.meta_value }" class="autocomplete-fields-infocob" data-tippy-content="${ _x("Meta value", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }" placeholder="${ _x("Meta value", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }">
							</div>
							<div class="langs ${ (langs_options === "") ? "invisible" : "" }">
								<select data-name="langs" multiple="multiple" data-tippy-content="${ _x("Languages", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }">
									${ langs_options }
								</select>
							</div>
							<div class="update">
								<label>
									<input data-name="update" type="checkbox" ${ (this.update) ? "checked" : "" }>
									${ _x("Update", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }
								</label>
							</div>
						</div>
						<div class="actions-container">
							<div class="post-meta">
								<button type="button" class="del-post_meta">${ _x("Delete", "Admin view configuration post, meta-box 'files'", 'infocob-crm-products') }</button>
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
			let post_type = jQuery(post_meta_element).find("select[data-name='post_type']");
			jQuery(post_type).off("change").on("change", (event) => {
				let selected_value = jQuery(event.currentTarget).val();
				let select_langs = jQuery(post_meta_element).find("select[data-name='langs']");

				this.post_type = selected_value;

				if(selected_value !== "" && selected_value !== null) {
					Utils.getLangs(this.post_type).then((languages) => {
						if(languages !== false) {
							let options = ``;
							for(const [index, lang] of Object.entries(languages)) {
								options += `<option value="${ lang }" ${ (this.langs.includes(lang)) ? `selected` : `` }>${ lang }</option>`;
							}

							jQuery(select_langs).html(options);
							jQuery(select_langs).trigger("change");
							jQuery(select_langs).parents(".langs")?.first()?.removeClass("invisible");
						} else {
							jQuery(select_langs).html(``);
							jQuery(select_langs).trigger("change");
							jQuery(select_langs).parents(".langs")?.first()?.addClass("invisible");
						}
					});
				} else {
					jQuery(select_langs).html(``);
					jQuery(select_langs).trigger("change");
					jQuery(select_langs).parents(".langs")?.first()?.addClass("invisible");
				}
			});

			let meta_key = jQuery(post_meta_element).find("input[data-name='meta_key']");
			jQuery(meta_key).off("input").on("input", (event) => {
				this.meta_key = jQuery(event.currentTarget).val();
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

			jQuery('#files-cloud-meta select[multiple=multiple]').multipleSelect("refresh");
		}
	}

	getPostMetaElement() {
		return jQuery(`#files-cloud-meta .post-meta-container[data-id='${ this.id }']`).first();
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
		FilesCloudMetaUtils.triggerUpdate();
	}

	get meta_key() {
		return this._meta_key;
	}

	set meta_key(value) {
		this._meta_key = String(value);
		FilesCloudMetaUtils.triggerUpdate();
	}

	get meta_value() {
		return this._meta_value;
	}

	set meta_value(value) {
		this._meta_value = String(value);
		FilesCloudMetaUtils.triggerUpdate();
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
		FilesCloudMetaUtils.triggerUpdate();
	}

	get update() {
		return Boolean(this._update);
	}

	set update(value) {
		this._update = Boolean(value);
		FilesCloudMetaUtils.triggerUpdate();
	}
}
