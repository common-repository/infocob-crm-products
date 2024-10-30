import {Utils}        from "../../../../Utils.js";
import {PostACFUtils} from "./PostACFUtils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class PostACF {
	static id = 0;

	constructor() {
		this._id = PostACF.id;
		this._post_type = "";
		this._acf_group = "";
		this._acf_field = "";
		this._acf_value = "";
		this._langs = [];
		this._update = true;

		let langsBase64 = jQuery("#post-acf").data("langs");
		this._langs = Utils.decodeConfig(langsBase64);

		PostACF.id++;
	}

	load(post_acf) {
		this.post_type = post_acf.post_type ?? "";
		this.acf_group = post_acf.acf_group ?? "";
		this.acf_field = post_acf.acf_field ?? "";
		this.acf_value = post_acf.acf_value ?? "";
		this.langs = post_acf.langs ?? [];
		this.update = post_acf.update ?? true;
	}

	toJSON() {
		return Object.assign({}, {
			post_type: this.post_type,
			acf_group: this.acf_group,
			acf_field: this.acf_field,
			acf_value: this.acf_value,
			langs: this.langs,
			update: this.update,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let promises = [];

			let post_type_html = `<select data-name="post_type" data-tippy-content="${ _x("Post type", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }"><option value=""></option></select>`;
			promises.push(Utils.getPostTypes().then((post_types) => {
				let options = `<option value=""></option>`;
				for(const [index, field] of Object.entries(post_types)) {
					options += `<option value="${ field.name }" ${ (this.post_type === field.name) ? `selected` : `` }>${ field.label } [${ field.name }]</option>`;
				}

				post_type_html = `
					<select data-name="post_type">
						${ options }
					</select>
				`;
			}));

			let acf_group_html = `<select data-name="acf_group" data-tippy-content="${ _x("Group", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }"><option value=""></option></select>`;
			if(this.post_type !== "") {
				promises.push(Utils.getAcfFieldGroupsFromPostType(this.post_type).then((acf_groups) => {
					let options = `<option value=""></option>`;
					for(const [index, group] of Object.entries(acf_groups)) {
						options += `<option value="${ group.ID }" ${ (this.acf_group === group.ID) ? `selected` : `` }>${ group.post_title } [${ group.post_name }]</option>`;
					}

					acf_group_html = `
						<select data-name="acf_group" data-tippy-content="${ _x("Group", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }">
							${ options }
						</select>
					`;
				}));
			}

			let acf_field_html = `<select data-name="acf_field" data-tippy-content="${ _x("Field", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }"><option value=""></option></select>`;
			if(this.acf_group !== "") {
				promises.push(Utils.getAcfFieldsFromGroup(this.acf_group).then((acf_fields) => {
					let options = `<option value=""></option>`;
					for(const [index, field] of Object.entries(acf_fields)) {
						options += `<option value="${ field.name }" ${ (this.acf_field === field.name) ? `selected` : `` }>${ field.title } [${ field.name }]</option>`;
					}

					acf_field_html = `
						<select data-name="acf_field" data-tippy-content="${ _x("Field", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }">
							${ options }
						</select>
					`;
				}));
			}

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

			Promise.allSettled(promises).then(() => {
				resolve(`
					<div class="post-acf-container" data-id="${ this.id }">
						<div class="post-type">
							${ post_type_html }
						</div>
						<div class="acf">
							<div class="acf-group">
								${ acf_group_html }
							</div>
							<div class="acf-field">
								${ acf_field_html }
							</div>
							<div class="acf-value">
								<input data-name="acf_value" type="text" value="${ this.acf_value }" class="autocomplete-fields-infocob">
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
							<div class="post-acf">
								<button type="button" class="del-post_acf">${ _x("Delete", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }</button>
							</div>
						</div>
					</div>
				`);
			})
		});
	}

	applyEvents() {
		let post_acf_element = this.getPostACFElement();

		if(post_acf_element.length) {
			let post_type = jQuery(post_acf_element).find("select[data-name='post_type']");
			jQuery(post_type).off("change").on("change", (event) => {
				let selected_value = jQuery(event.currentTarget).val();
				let select_acf_group = jQuery(post_acf_element).find("select[data-name='acf_group']");
				let select_langs = jQuery(post_acf_element).find("select[data-name='langs']");

				this.post_type = selected_value;

				if(selected_value !== "" && selected_value !== null) {
					Utils.getAcfFieldGroupsFromPostType(selected_value).then((acf_groups) => {
						let options = `<option value=""></option>`;
						for(const [index, group] of Object.entries(acf_groups)) {
							options += `<option value="${ group.ID }">${ group.post_title } [${ group.post_name }]</option>`;
						}

						jQuery(select_acf_group).html(options);
						jQuery(select_acf_group).trigger("change");
					});

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
					jQuery(select_acf_group).html(`<option value=""></option>`);
					jQuery(select_acf_group).trigger("change");

					jQuery(select_langs).html(``);
					jQuery(select_langs).trigger("change");
					jQuery(select_langs).parents(".langs")?.first()?.addClass("invisible");
				}
			});

			let acf_group = jQuery(post_acf_element).find("select[data-name='acf_group']");
			jQuery(acf_group).off("change").on("change", (event) => {
				let selected_value = jQuery(event.currentTarget).val();
				let select_acf_field = jQuery(post_acf_element).find("select[data-name='acf_field']");

				this.acf_group = selected_value;

				if(selected_value !== "" && selected_value !== null) {
					Utils.getAcfFieldsFromGroup(selected_value).then((acf_fields) => {
						let options = `<option value=""></option>`;
						for(const [index, field] of Object.entries(acf_fields)) {
							options += `<option value="${ field.name }">${ field.title } [${ field.name }]</option>`;
						}

						jQuery(select_acf_field).html(options);
						jQuery(select_acf_field).trigger("change");
					});
				} else {
					jQuery(select_acf_field).html(`<option value=""></option>`);
					jQuery(select_acf_field).trigger("change");
				}
			});

			let acf_field = jQuery(post_acf_element).find("select[data-name='acf_field']");
			jQuery(acf_field).off("change").on("change", (event) => {
				this.acf_field = jQuery(event.currentTarget).val();
			});

			let acf_value = jQuery(post_acf_element).find("input[data-name='acf_value']");
			jQuery(acf_value).off("input").on("input", (event) => {
				this.acf_value = jQuery(event.currentTarget).val();
			});

			let langs = jQuery(post_acf_element).find("select[data-name='langs']");
			jQuery(langs).off("change").on("change", (event) => {
				let options_selected = jQuery(event.currentTarget).find("option:selected");
				let langs = [];
				jQuery(options_selected).each((index, option) => {
					langs.push(jQuery(option).val());
				});
				this.langs = langs;

				jQuery(event.currentTarget).multipleSelect("refresh");
			});

			let update = jQuery(post_acf_element).find("input[data-name='update']");
			jQuery(update).off("change").on("change", (event) => {
				this.update = jQuery(event.currentTarget).prop("checked");
			});

			let delPostACF = jQuery(post_acf_element).find(".actions-container > .post-acf > .del-post_acf");
			jQuery(delPostACF).off("click").on("click", (event) => {
				event.preventDefault();
				jQuery(post_acf_element).remove();
				jQuery(this).trigger("del-post_acf", {
					id: this.id
				});
			});

			jQuery('#post-acf select[multiple=multiple]').multipleSelect("refresh");
		}
	}

	getPostACFElement() {
		return jQuery(`#post-acf .post-acf-container[data-id='${ this.id }']`).first();
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
		PostACFUtils.triggerUpdate();
	}

	get acf_group() {
		return this._acf_group;
	}

	set acf_group(value) {
		this._acf_group = parseInt(value, 10);
		PostACFUtils.triggerUpdate();
	}

	get acf_field() {
		return this._acf_field;
	}

	set acf_field(value) {
		this._acf_field = String(value);
		PostACFUtils.triggerUpdate();
	}

	get acf_value() {
		return this._acf_value;
	}

	set acf_value(value) {
		this._acf_value = String(value);
		PostACFUtils.triggerUpdate();
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
		PostACFUtils.triggerUpdate();
	}

	get update() {
		return Boolean(this._update);
	}

	set update(value) {
		this._update = Boolean(value);
		PostACFUtils.triggerUpdate();
	}
}
