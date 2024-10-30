import {FilterUtils} from "./FilterUtils.js";
import {Utils} from "../../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilterTaxonomy {
    static id = 0;

    constructor() {
        this._id = FilterTaxonomy.id;
        this._display = "list";
        this._title = {};
        this._taxonomy = "";
        this._defaults = [];
        this._langs = [];

        let langsBase64 = jQuery("#left-filters").data("langs");
        this._langs = Utils.decodeConfig(langsBase64);

        FilterTaxonomy.id++;
    }

    load(filterPostMeta) {
        this.display = filterPostMeta.display ?? "select";
        this.title = filterPostMeta.title ?? {};
        this.taxonomy = filterPostMeta.taxonomy ?? "";
        this.defaults = filterPostMeta.defaults ?? [];
    }

    toJSON() {
        return Object.assign({}, {
            display: this.display,
            title: this.title,
            taxonomy: this.taxonomy,
            defaults: this.defaults,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            let post_type_value = jQuery("#general-post-type").val();

            let taxonomy_html = `<select data-name="taxonomy" class="all-witdh" data-tippy-content="${ _x("Taxonomy", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }"><option value=""></option></select>`;
            if(post_type_value !== "") {
                promises.push(Utils.getTaxonomiesFromPostType(post_type_value).then((taxonomies) => {
                    let options = `<option value=""></option>`;
                    for (const [index, field] of Object.entries(taxonomies)) {
                        options += `<option value="${field.name}" ${(field.name === this.taxonomy) ? `selected` : ``}>${field.label} [${field.name}]</option>`;
                    }

                    taxonomy_html = `
						<select data-name="taxonomy" class="all-witdh" data-tippy-content="${ _x("Taxonomy", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }">
							${options}
						</select>
					`;
                }));
            }

            let defaults_html = `<select data-name="defaults" class="all-witdh" data-tippy-content="${ _x("Default", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }"></select>`;
            if(this.taxonomy !== "") {
                promises.push(Utils.getCategoriesFromTaxonomy(this.taxonomy).then((categories) => {
                    let blank_option = `<option value=""></option>`;

                    let options = ``;
                    for(const [index, field] of Object.entries(categories)) {
                        let values = field.term_id;
                        if(field.term_ids) {
                            values = field.term_ids.join(';')
                        }
                        let level_text = "";
                        let level = field.level ?? 0;
                        for(let i = 0; i < level; i++) {
                            level_text += "-";
                        }
                        options += `<option value="${ values }" ${ (this.defaults.includes(field.term_id)) ? `selected` : `` }>${ level_text } ${ field.name } [${ field.slug }]</option>`;
                    }

                    let multiple = "";
                    if (["checkbox", "select-multiple"].includes(this.display)) {
                        multiple = `multiple="multiple"`;
                    } else {
                        options = blank_option + options;
                    }

                    defaults_html = `
                        <select data-name="defaults" class="all-witdh" ${multiple} data-tippy-content="${ _x("Default", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }">
                            ${ options }
                        </select>
                    `;
                }));
            }

            let titles = ``;
            this.langs.forEach((lang, index) => {
                titles += `<input class="all-witdh multilingual" data-name="title" data-lang="${ lang }" type="text" value="${ this.title[lang] ?? "" }" data-tippy-content="${ sprintf(_x("Title %s", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products'), lang) }" placeholder="${ sprintf(_x("Title %s", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products'), lang) }">`;
            });

            Promise.allSettled(promises).then(() => {
                // language=html
                resolve(`
                    <div class="filter taxonomy" data-id="${ this.id }">
                        <div class="display margin">
                            <select data-name="display" class="all-witdh" data-tippy-content="${ _x("Display", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }">
								<option value="list" ${ (this.display === "list") ? "selected" : "" }>${ _x("List", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                                <option value="select" ${ (this.display === "select") ? "selected" : "" }>${ _x("Select", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
								<option value="select-multiple" ${ (this.display === "select-multiple") ? "selected" : "" }>${ _x("Select multiple", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                                <option value="checkbox" ${ (this.display === "checkbox") ? "selected" : "" }>${ _x("Checkbox", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                                <option value="radio" ${ (this.display === "radio") ? "selected" : "" }>${ _x("Radio", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
						<div class="title margin">
							${ titles }
						</div>
                        <div class="taxonomy margin all-witdh">
							${ taxonomy_html }
                        </div>
						<div class="defaults margin">
							${ defaults_html }
						</div>
					</div>
                `);
            });
        });
    }

    applyEvents() {
        let left_filter_element = this.getFilterElement();

        let display = jQuery(left_filter_element).find("select[data-name='display']");
        let previous_display = jQuery(display).val();
        jQuery(display).off("change").on("change", (event) => {
            let selected_value = jQuery(event.currentTarget).val();
            if(selected_value !== previous_display) {
                this.display = selected_value;

                let defaults = jQuery(left_filter_element).find("select[data-name='defaults']");
                let defaults_html = `<select data-name="defaults" class="all-witdh"></select>`;
                Utils.getCategoriesFromTaxonomy(this.taxonomy).then((categories) => {
                    let blank_option = `<option value=""></option>`;

                    let options = ``;
                    for(const [index, field] of Object.entries(categories)) {
                        let values = field.term_id;
                        if(field.term_ids) {
                            values = field.term_ids.join(';')
                        }
                        let level_text = "";
                        let level = field.level ?? 0;
                        for(let i = 0; i < level; i++) {
                            level_text += "-";
                        }
                        options += `<option value="${ values }" ${ (this.defaults.includes(field.term_id)) ? `selected` : `` }>${ level_text } ${ field.name } [${ field.slug }]</option>`;
                    }

                    let multiple = "";
                    if (["checkbox", "select-multiple"].includes(this.display)) {
                        multiple = `multiple="multiple"`;
                    } else {
                        options = blank_option + options;
                    }

                    defaults_html = `
                        <select data-name="defaults" class="all-witdh" ${multiple}>
                            ${ options }
                        </select>
                    `;

                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    jQuery(left_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");

                }).catch(() => {
                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                }).finally(() => {
                   this.applyEvents();
                });
            }

            previous_display = selected_value;
        });

        let title = jQuery(left_filter_element).find("input[data-name='title']");
        jQuery(title).off("keyup keypress blur change cut paste").on("keyup keypress blur change cut paste", (event) => {
            let title_element = jQuery(event.currentTarget);
            let current_value = jQuery(title_element).val();
            let title_lang = jQuery(title_element).data("lang");

            this.addTitle(current_value, title_lang);
        });

        let taxonomy = jQuery(left_filter_element).find("select[data-name='taxonomy']");
        let previous_taxonomy = jQuery(taxonomy).val();
        jQuery(taxonomy).off("change").on("change", (event) => {
            let current_value = jQuery(event.currentTarget).val();
            if(current_value !== previous_taxonomy) {
                this.taxonomy = current_value;

                let defaults = jQuery(left_filter_element).find("select[data-name='defaults']");
                let defaults_html = `<select data-name="defaults" class="all-witdh"></select>`;
                Utils.getCategoriesFromTaxonomy(this.taxonomy).then((categories) => {
                    let blank_option = `<option value=""></option>`;

                    let options = ``;
                    for(const [index, field] of Object.entries(categories)) {
                        let values = field.term_id;
                        if(field.term_ids) {
                            values = field.term_ids.join(';')
                        }
                        let level_text = "";
                        let level = field.level ?? 0;
                        for(let i = 0; i < level; i++) {
                            level_text += "-";
                        }
                        options += `<option value="${ values }" ${ (this.defaults.includes(field.term_id)) ? `selected` : `` }>${ level_text } ${ field.name } [${ field.slug }]</option>`;
                    }

                    let multiple = "";
                    if (["checkbox", "select-multiple"].includes(this.display)) {
                        multiple = `multiple="multiple"`;
                    } else {
                        options = blank_option + options;
                    }

                    defaults_html = `
                        <select data-name="defaults" class="all-witdh" ${multiple}>
                            ${ options }
                        </select>
                    `;

                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    jQuery(left_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");

                }).catch(() => {
                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                }).finally(() => {
                    this.applyEvents();
                });
            }

            previous_taxonomy = current_value;
        });

        let defaults = jQuery(left_filter_element).find("select[data-name='defaults']");
        jQuery(defaults).off("change").on("change", (event) => {
            let options_selected = jQuery(event.currentTarget).find("option:selected");
            let categories = [];
            jQuery(options_selected).each((index, option) => {
                let value_string = jQuery(option).val();
                let values = value_string.split(';');
                categories = categories.concat(values);
            });
            this.defaults = categories;
        });

        jQuery('#left-filters select[multiple=multiple]').multipleSelect("refresh");
    }

    getFilterElement() {
        return jQuery(`#left-filters .left-filters-container .filter.taxonomy[data-id='${ this.id }']`).first();
    }

    get id() {
        return this._id;
    }

    get display() {
        return this._display;
    }

    set display(value) {
        this._display = String(value);
        FilterUtils.triggerUpdate();
    }

    get title() {
        return this._title;
    }

    set title(value) {
        if(!(value instanceof Object)) {
            value = {};
        }

        this._title = value;
        FilterUtils.triggerUpdate();
    }

    addTitle(value, lang) {
        this.title[lang] = String(value);
        FilterUtils.triggerUpdate();
    }

    get taxonomy() {
        return this._taxonomy;
    }

    set taxonomy(value) {
        this._taxonomy = String(value);
        FilterUtils.triggerUpdate();
    }

    get defaults() {
        return this._defaults;
    }

    set defaults(value) {
        if(Array.isArray(value)) {
            value = value.map((val) => {
                if(!isNaN(val)) {
                    return parseInt(val, 10);
                }
            });

            this._defaults = value;
        } else {
            this._defaults = [];
        }
        FilterUtils.triggerUpdate();
    }

    get langs() {
        return this._langs;
    }
}
