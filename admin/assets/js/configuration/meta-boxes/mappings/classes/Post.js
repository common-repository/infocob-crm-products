import {Utils}           from "../../../../Utils.js";
import {MappingsUtils}   from "./MappingsUtils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class Post {
	static id = 0;

	constructor() {
		this._id = Post.id;
		this._post_type = "";
		this._taxonomy = "";
		this._categories = [];

		Post.id++;
	}

	load(post) {
		this.post_type = post.post_type ?? "";
		this.taxonomy = post.taxonomy ?? "";
		this.categories = post.categories ?? [];
	}

	toJSON() {
		return Object.assign({}, {
			post_type: this.post_type,
			taxonomy: this.taxonomy,
			categories: this.categories,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let promises = [];

			let post_type_html = `<select data-name="post_type"><option value=""></option></select>`;
			let taxonomy_html = `<select data-name="taxonomy"><option value=""></option></select>`;
			let categories_html = `<select data-name="categories" multiple="multiple"></select>`;

			promises.push(Utils.getPostTypes().then((post_types) => {
				let options = `<option value=""></option>`;
				for(const [index, field] of Object.entries(post_types)) {
					options += `<option value="${ field.name }" ${ (field.name === this.post_type) ? `selected` : `` }>${ field.label } [${ field.name }]</option>`;
				}

				post_type_html = `
					<select data-name="post_type">
						${ options }
					</select>
				`;
			}));

			if(this.post_type !== "") {
				promises.push(Utils.getTaxonomiesFromPostType(this.post_type).then((taxonomies) => {
					let options = `<option value=""></option>`;
					for(const [index, field] of Object.entries(taxonomies)) {
						options += `<option value="${ field.name }" ${ (field.name === this.taxonomy) ? `selected` : `` }>${ field.label } [${ field.name }]</option>`;
					}

					taxonomy_html = `
						<select data-name="taxonomy">
							${ options }
						</select>
					`;
				}));
			}

			if(this.taxonomy !== "") {
				promises.push(Utils.getCategoriesFromTaxonomy(this.taxonomy).then((categories) => {
					let options = ``;
					for(const [index, field] of Object.entries(categories)) {
						let selected = this.categories.includes(field.term_id) ? 'selected' : '';
						let values = field.term_id;
						if(field.term_ids) {
							values = field.term_ids.join(';')
						}
						let level_text = "";
						let level = field.level ?? 0;
						for(let i = 0; i < level; i++) {
							level_text += "-";
						}
						options += `<option value="${ values }" ${ selected }>${ level_text } ${ field.name } [${ field.slug }]</option>`;
					}

					categories_html = `
						<select data-name="categories" multiple="multiple">
							${ options }
						</select>
					`;
				}));
			}

			Promise.allSettled(promises).then(() => {
				resolve(`
					<div class="post-container" data-id="${ this.id }">
						<div class="post-type">
							${ post_type_html }
						</div>
						<div class="taxonomy">
							${ taxonomy_html }
						</div>
						<div class="categories">
							${ categories_html }
						</div>
						<div class="duplicate">
							<button class="btn-duplicate" type="button">${_x("Duplicate", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products')}</button>						
						</div>
						<div class="toggle">
							<button class="btn-toggle" type="button">${_x("Expand", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products')}</button>						
						</div>
					</div>
				`);
			})
		});
	}

	applyEvents() {
		let post_element = this.getPostElement();

		if(post_element.length) {
			let post_type = jQuery(post_element).find("select[data-name='post_type']");
			jQuery(post_type).off("change").on("change", (event) => {
				let selected_value = jQuery(event.currentTarget).val();
				let select_taxonomy = jQuery(post_element).find("select[data-name='taxonomy']");

				this.post_type = selected_value;

				if(selected_value !== "" && selected_value !== null) {
					Utils.getTaxonomiesFromPostType(selected_value).then((taxonomies) => {
						let options = `<option value=""></option>`;
						for(const [index, field] of Object.entries(taxonomies)) {
							options += `<option value="${ field.name }">${ field.label } [${ field.name }]</option>`;
						}

						jQuery(select_taxonomy).html(options);
						jQuery(select_taxonomy).trigger("change");
					});
				} else {
					jQuery(select_taxonomy).html(`<option value=""></option>`);
					jQuery(select_taxonomy).trigger("change");
				}
			});

			let taxonomy = jQuery(post_element).find("select[data-name='taxonomy']");
			jQuery(taxonomy).off("change").on("change", (event) => {
				let selected_value = jQuery(event.currentTarget).val();
				let select_categories = jQuery(post_element).find("select[data-name='categories']");

				this.taxonomy = selected_value;

				if(selected_value !== "" && selected_value !== null) {
					Utils.getCategoriesFromTaxonomy(selected_value).then((categories) => {
						let options = ``;
						for(const [index, field] of Object.entries(categories)) {
							let level_text = "";
							let level = field.level ?? 0;
							for(let i = 0; i < level; i++) {
								level_text += "-";
							}

							options += `<option value="${ field.term_id }">${ level_text } ${ field.name } [${ field.slug }]</option>`;
						}

						jQuery(select_categories).html(options);
						jQuery(select_categories).trigger("change");
					});
				} else {
					jQuery(select_categories).html(``);
					jQuery(select_categories).trigger("change");
				}
			});

			let categories = jQuery(post_element).find("select[data-name='categories']");
			jQuery(categories).off("change").on("change", (event) => {
				let options_selected = jQuery(event.currentTarget).find("option:selected");
				let categories = [];
				jQuery(options_selected).each((index, option) => {
					let value_string = jQuery(option).val();
					let values = value_string.split(';');
					categories = categories.concat(values);
				});
				this.categories = categories;

				jQuery(event.currentTarget).multipleSelect("refresh");
			});

			let btnDuplicate = jQuery(post_element).find("button.btn-duplicate");
			jQuery(btnDuplicate).off("click").on("click", (event) => {
				let row_id = jQuery(post_element).parents(".row").first().data("id");
				jQuery("#mappings").trigger("add-row", row_id);
			});

			let btnToggle = jQuery(post_element).find("button.btn-toggle");
			jQuery(btnToggle).off("click").on("click", (event, state) => {
				let expandText = _x("Expand", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products');
				let reduceText = _x("Reduce", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products');
				let row = jQuery(event.currentTarget).parents("div.row");
				if(state === undefined) {
					if (jQuery(row).hasClass("closed")) {
						jQuery(row).removeClass("closed");
						jQuery(event.currentTarget).html(reduceText);
					} else {
						jQuery(row).addClass("closed");
						jQuery(event.currentTarget).html(expandText);
					}
				} else {
					if(state === "open") {
						jQuery(row).removeClass("closed");
						jQuery(event.currentTarget).html(reduceText);

					} else if(state === "close") {
						jQuery(row).addClass("closed");
						jQuery(event.currentTarget).html(expandText);
					}
				}
			});
		}
	}

	getPostElement() {
		return jQuery(`#mappings .post-container[data-id='${this.id}']`).first();
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
	}

	get taxonomy() {
		return this._taxonomy;
	}

	set taxonomy(value) {
		this._taxonomy = String(value);
	}

	get categories() {
		return this._categories;
	}

	set categories(value) {
		if(Array.isArray(value)) {
			value = value.map((val) => {
				if(!isNaN(val)) {
					return parseInt(val, 10);
				}
			});

			this._categories = value;
		} else {
			this._categories = [];
		}
	}
}
