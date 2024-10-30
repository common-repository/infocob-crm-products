import {Utils}        from "../../../../Utils.js";
import {PostInventoryUtils} from "./PostInventoryUtils.js";
import {PostInventoryField} from "./PostInventoryField.js";

const {__, _x, _n, _nx} = wp.i18n;

export class PostInventory {
	static id = 0;

	constructor() {
		this._id = PostInventory.id;
		this._post_type = "";
		this._acf_group = "";
		this._acf_repeater = "";
		this._acf_fields = [];
		this._update = true;
		this._langs = [];

		let langsBase64 = jQuery("#post-inventory").data("langs");
		this._langs = Utils.decodeConfig(langsBase64);

		PostInventory.id++;
	}

	load(post_acf) {
		this.post_type = post_acf.post_type ?? "";
		this.acf_group = post_acf.acf_group ?? "";
		this.acf_repeater = post_acf.acf_repeater ?? "";
		this.loadFields(post_acf.acf_fields ?? []);
		this.update = post_acf.update ?? true;
		this.langs = post_acf.langs ?? [];
	}

	loadFields(acf_fields) {
		if(Array.isArray(acf_fields)) {
			let postInventoryFields = [];
			acf_fields.forEach((acf_field) => {
				let postInventoryField = new PostInventoryField();
				postInventoryField.name= acf_field.name ?? "";
				postInventoryField.value= acf_field.value ?? "";
				postInventoryField.update = acf_field.update ?? true;

				postInventoryFields.push(postInventoryField);
			});

			this.acf_fields = postInventoryFields;
		}
	}

	toJSON() {
		return Object.assign({}, {
			post_type: this.post_type,
			acf_group: this.acf_group,
			acf_repeater: this.acf_repeater,
			acf_fields: this.acf_fields,
			update: this.update,
			langs: this.langs,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let promises = [];

			let post_type_html = `<select data-name="post_type" data-tippy-content="${ _x("Post type", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }"><option value=""></option></select>`;
			promises.push(Utils.getPostTypes().then((post_types) => {
				let options = `<option value=""></option>`;
				for(const [index, field] of Object.entries(post_types)) {
					options += `<option value="${ field.name }" ${ (this.post_type === field.name) ? `selected` : `` }>${ field.label } [${ field.name }]</option>`;
				}

				post_type_html = `
					<select data-name="post_type" data-tippy-content="${ _x("Post type", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }">
						${ options }
					</select>
				`;
			}));

			let acf_group_html = `<select data-name="acf_group" data-tippy-content="${ _x("Group", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }"><option value=""></option></select>`;
			if(this.post_type !== "") {
				promises.push(Utils.getAcfFieldGroupsFromPostType(this.post_type).then((acf_groups) => {
					let options = `<option value=""></option>`;
					for(const [index, group] of Object.entries(acf_groups)) {
						options += `<option value="${ group.ID }" ${ (this.acf_group === group.ID) ? `selected` : `` }>${ group.post_title } [${ group.post_name }]</option>`;
					}

					acf_group_html = `
						<select data-name="acf_group" data-tippy-content="${ _x("Group", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }">
							${ options }
						</select>
					`;
				}));
			}

			let acf_repeater_html = `<select data-name="acf_repeater" data-tippy-content="${ _x("Repeater", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }"><option value=""></option></select>`;
			if(this.acf_group !== "") {
				promises.push(Utils.getAcfRepeaterFieldsFromGroup(this.acf_group).then((acf_repeaters) => {
					let options = `<option value=""></option>`;
					for(const [index, field] of Object.entries(acf_repeaters)) {
						options += `<option value="${ field.name }" ${ (this.acf_repeater === field.name) ? `selected` : `` }>${ field.title } [${ field.name }]</option>`;
					}

					acf_repeater_html = `
						<select data-name="acf_repeater" data-tippy-content="${ _x("Repeater", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }">
							${ options }
						</select>
					`;
				}));
			}

			let acf_fields_html = ``;
			if(this.acf_repeater !== "") {
				promises.push(Utils.getAcfSubFieldsFromField(this.acf_group, this.acf_repeater).then((acf_fields) => {
					let postInventoryFields = [];
					for(const [index, field] of Object.entries(acf_fields)) {
						let postInventoryField = this.get_acf_field(field.name);

						if(postInventoryField === false || !(postInventoryField instanceof PostInventoryField)) {
							postInventoryField = new PostInventoryField();
							postInventoryField.name = field.name ?? "";
						}

						postInventoryField.title = field.title ?? "";

						acf_fields_html += postInventoryField.toHTML();
						postInventoryFields.push(postInventoryField);
					}

					this.acf_fields = postInventoryFields;
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
					<div class="post-inventory-container" data-id="${ this.id }">
						<div class="post-type">
							${ post_type_html }
						</div>
						<div class="acf">
							<div class="acf-group">
								${ acf_group_html }
							</div>
							<div class="acf-repeater">
								${ acf_repeater_html }
							</div>
							<div class="acf-fields">
								${ acf_fields_html }
							</div>
							<div class="langs ${ (langs_options === "") ? "invisible" : "" }">
								<select data-name="langs" multiple="multiple" data-tippy-content="${ _x("Languages", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }">
									${ langs_options }
								</select>
							</div>
							<div class="update">
								<label>
									<input data-name="update" type="checkbox" ${ (this.update) ? "checked" : "" }>
									${ _x("Update", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }
								</label>
							</div>
						</div>
						<div class="actions-container">
							<div class="post-inventory">
								<button type="button" class="del-post_inventory">${ _x("Delete", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }</button>
							</div>
						</div>
					</div>
				`);
			})
		});
	}

	applyEvents() {
		let post_inventory_element = this.getPostInventoryElement();

		if(post_inventory_element.length) {
			let post_type = jQuery(post_inventory_element).find("select[data-name='post_type']");
			jQuery(post_type).off("change").on("change", (event) => {
				let selected_value = jQuery(event.currentTarget).val();
				let select_acf_group = jQuery(post_inventory_element).find("select[data-name='acf_group']");
				let select_langs = jQuery(post_inventory_element).find("select[data-name='langs']");

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

			let acf_group = jQuery(post_inventory_element).find("select[data-name='acf_group']");
			jQuery(acf_group).off("change").on("change", (event) => {
				let selected_value = jQuery(event.currentTarget).val();
				let select_acf_repeater = jQuery(post_inventory_element).find("select[data-name='acf_repeater']");

				this.acf_group = selected_value;

				if(selected_value !== "" && selected_value !== null) {
					Utils.getAcfRepeaterFieldsFromGroup(selected_value).then((acf_fields) => {
						let options = `<option value=""></option>`;
						for(const [index, field] of Object.entries(acf_fields)) {
							options += `<option value="${ field.name }">${ field.title } [${ field.name }]</option>`;
						}

						jQuery(select_acf_repeater).html(options);
						jQuery(select_acf_repeater).trigger("change");
					});
				} else {
					jQuery(select_acf_repeater).html(`<option value=""></option>`);
					jQuery(select_acf_repeater).trigger("change");
				}
			});

			let acf_repeater = jQuery(post_inventory_element).find("select[data-name='acf_repeater']");
			jQuery(acf_repeater).off("change").on("change", (event) => {
				let selected_value = jQuery(event.currentTarget).val();
				let acf_fields_element = jQuery(post_inventory_element).find("div.acf-fields").first();
				this.acf_repeater = selected_value;

				if(selected_value !== "" && selected_value !== null) {
					Utils.getAcfSubFieldsFromField(this.acf_group, selected_value).then((acf_fields) => {
						let postInventoryFields = [];
						let postInventoryField_html = "";
						for(const [index, field] of Object.entries(acf_fields)) {
								let postInventoryField = new PostInventoryField();
								postInventoryField.name = field.name ?? "";
								postInventoryField.title = field.title ?? "";

								this.acf_fields.push(postInventoryField);
								postInventoryField_html += postInventoryField.toHTML();

								postInventoryFields.push(postInventoryField);
						}

						this.acf_fields = postInventoryFields;

						jQuery(acf_fields_element).html(postInventoryField_html);

						let module_infocob = jQuery("#infocob-type-produit").val();
						Utils.tributeFieldsInfocob(jQuery("#post-inventory > .content-post-inventory input.autocomplete-fields-infocob"), [], [module_infocob, "INVENTAIREPRODUIT", "TYPEINVENTAIREPRODUIT", "FAMILLETYPEINVENTAIRE"]);

						this.applyEvents();

						Utils.initTooltips();
					});
				} else {
					this.acf_fields = [];
					jQuery(acf_fields_element).html("");
				}
			});

			let langs = jQuery(post_inventory_element).find("select[data-name='langs']");
			jQuery(langs).off("change").on("change", (event) => {
				let options_selected = jQuery(event.currentTarget).find("option:selected");
				let langs = [];
				jQuery(options_selected).each((index, option) => {
					langs.push(jQuery(option).val());
				});
				this.langs = langs;

				jQuery(event.currentTarget).multipleSelect("refresh");
			});

			let delPostInventory = jQuery(post_inventory_element).find(".actions-container > .post-inventory > .del-post_inventory");
			jQuery(delPostInventory).off("click").on("click", (event) => {
				event.preventDefault();
				jQuery(post_inventory_element).remove();
				jQuery(this).trigger("del-post_inventory", {
					id: this.id
				});
			});

			let update = jQuery(post_inventory_element).find("input[data-name='update']");
			jQuery(update).off("change").on("change", (event) => {
				this.update = jQuery(event.currentTarget).prop("checked");
			});

			this.acf_fields.forEach((postInventoryField) => {
				if(postInventoryField instanceof PostInventoryField) {
					postInventoryField.applyEvents();
				}
			});

			jQuery('#post-inventory select[multiple=multiple]').multipleSelect("refresh");
		}
	}

	getPostInventoryElement() {
		return jQuery(`#post-inventory .post-inventory-container[data-id='${ this.id }']`).first();
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
		PostInventoryUtils.triggerUpdate();
	}

	get acf_group() {
		return this._acf_group;
	}

	set acf_group(value) {
		this._acf_group = parseInt(value, 10);
		PostInventoryUtils.triggerUpdate();
	}

	get acf_repeater() {
		return this._acf_repeater;
	}

	set acf_repeater(value) {
		this._acf_repeater = String(value)
		PostInventoryUtils.triggerUpdate();
	}

	get acf_fields() {
		return this._acf_fields;
	}

	set acf_fields(value) {
		this._acf_fields = value;
		PostInventoryUtils.triggerUpdate();
	}

	get_acf_field(field_name) {
		let postInventoryField = false;
		this.acf_fields.forEach((acf_field) => {
			if(acf_field instanceof PostInventoryField) {
				if (acf_field.name === field_name) {
					postInventoryField = acf_field;
				}
			}
		});

		return postInventoryField;
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
		PostInventoryUtils.triggerUpdate();
	}

	get update() {
		return Boolean(this._update);
	}

	set update(value) {
		this._update = Boolean(value);
		PostInventoryUtils.triggerUpdate();
	}
}
