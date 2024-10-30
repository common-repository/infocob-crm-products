import {FilterUtils} from "./FilterUtils.js";
import {Utils} from "../../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilterAcf {
    static id = 0;

    constructor() {
        this._id = FilterAcf.id;
        this._display = "select";
        this._title = {};
        this._acf_group = "";
        this._acf_field = "";
        this._unit = {};
        this._step = 1;
        this._defaults = [];
        this._langs = [];

        let langsBase64 = jQuery("#left-filters").data("langs");
        this._langs = Utils.decodeConfig(langsBase64);

        FilterAcf.id++;
    }

    load(filterAcf) {
        this.display = filterAcf.display ?? "select";
        this.title = filterAcf.title ?? {};
        this.acf_group = filterAcf.acf_group ?? "";
        this.acf_field = filterAcf.acf_field ?? "";
        this.unit = filterAcf.unit ?? {};
        this.step = filterAcf.step ?? 1;
        this.defaults = filterAcf.defaults ?? [];
    }

    toJSON() {
        return Object.assign({}, {
            display: this.display,
            title: this.title,
            acf_group: this.acf_group,
            acf_field: this.acf_field,
            unit: this.unit,
            step: this.step,
            defaults: this.defaults,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            let post_type_value = jQuery("#general-post-type").val();

            let acf_group_html = `<select data-name="acf-group" class="all-witdh" data-tippy-content="${ _x("Group", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }"><option value=""></option></select>`;
            if(post_type_value !== "") {
                promises.push(Utils.getAcfFieldGroupsFromPostType(post_type_value).then((acf_groups) => {
                    let options = `<option value=""></option>`;
                    for(const [index, group] of Object.entries(acf_groups)) {
                        options += `<option value="${ group.ID }" ${ (this.acf_group === group.ID) ? `selected` : `` }>${ group.post_title } [${ group.post_name }]</option>`;
                    }

                    acf_group_html = `
						<select data-name="acf-group" class="all-witdh" data-tippy-content="${ _x("Group", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }">
							${ options }
						</select>
					`;
                }));
            }

            let acf_field_html = `<select data-name="acf-field" class="all-witdh" data-tippy-content="${ _x("Field", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }"><option value=""></option></select>`;
            if(this.acf_group !== "") {
                promises.push(Utils.getAcfFieldsFromGroup(this.acf_group).then((acf_fields) => {
                    let options = `<option value=""></option>`;
                    for(const [index, field] of Object.entries(acf_fields)) {
                        options += `<option value="${ field.name }" ${ (this.acf_field === field.name) ? `selected` : `` }>${ field.title } [${ field.name }]</option>`;
                    }

                    acf_field_html = `
						<select data-name="acf-field" class="all-witdh">
							${ options }
						</select>
					`;
                }));
            }

            let defaults_html = `<select data-name="defaults" class="all-witdh" data-tippy-content="${ _x("Default", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }"></select>`;
            if(this.acf_field !== "") {
                promises.push(Utils.getAcfFieldsValues(post_type_value, [this.acf_field]).then((response) => {
                    let blank_option = `<option value=""></option>`;
                    let options = ``;

                    let acf_values = response[this.acf_field];
                    if(acf_values.length) {
                        for (const [index, value] of Object.entries(acf_values)) {
                            options += `<option value="${value}" ${(this.defaults.includes(value)) ? `selected` : ``}>${value}</option>`;
                        }

                        let multiple = "";
                        if (["checkbox", "select-multiple"].includes(this.display)) {
                            multiple = `multiple="multiple"`;
                        } else {
                            options = blank_option + options;
                        }

                        defaults_html = `
                            <select data-name="defaults" class="all-witdh" ${multiple} >
                                ${options}
                            </select>
                        `;
                    }
                }));
            }

            let titles = ``;
            let units = ``;
            this.langs.forEach((lang, index) => {
                titles += `<input class="all-witdh multilingual" data-name="title" data-lang="${ lang }" type="text" value="${ this.title[lang] ?? "" }" data-tippy-content="${ sprintf(_x("Title %s", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products'), lang) }" placeholder="${ sprintf(_x("Title %s", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products'), lang) }">`;
                units += `<input class="all-witdh multilingual" data-name="unit" data-lang="${ lang }" type="text" value="${ this.unit[lang] ?? "" }" data-tippy-content="${ sprintf(_x("Unit %s", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products'), lang) }" placeholder="${ sprintf(_x("Unit %s", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products'), lang) }">`;
            });

            Promise.allSettled(promises).then(() => {
                // language=html
                resolve(`
                    <div class="filter acf" data-id="${ this.id }">
                        <div class="display margin">
                            <select data-name="display" class="all-witdh" data-tippy-content="${ _x("Display", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }">
								<option value="select" ${ (this.display === "select") ? "selected" : "" }>${ _x("Select", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
								<option value="select-multiple" ${ (this.display === "select-multiple") ? "selected" : "" }>${ _x("Select multiple", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
								<option value="checkbox" ${ (this.display === "checkbox") ? "selected" : "" }>${ _x("Checkbox", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
								<option value="radio" ${ (this.display === "radio") ? "selected" : "" }>${ _x("Radio", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
								<option value="range" ${ (this.display === "range") ? "selected" : "" }>${ _x("Range", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
						<div class="title margin">
							${ titles }
						</div>
                        <div class="acf-group margin all-witdh">
							${ acf_group_html }
                        </div>
						<div class="acf-field margin all-witdh">
							${ acf_field_html }
						</div>
						<div class="defaults margin">
							${ defaults_html }
						</div>
						<div class="step margin ${ (this.display !== "range") ? "invisible" : "" }" data-tippy-content="${ _x("Step", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }">
							<input type="number" data-name="step" min="0" value="${ this.step }" class="all-witdh">
						</div>
						<div class="unit margin">
							${ units }
						</div>
					</div>
                `);
            });
        });
    }

    applyEvents() {
        let left_filter_element = this.getFilterElement();

        let post_type_value = jQuery("#general-post-type").val();

        let display = jQuery(left_filter_element).find("select[data-name='display']");
        let previous_display = jQuery(display).val();
        jQuery(display).off("change").on("change", (event) => {
            let selected_value = jQuery(event.currentTarget).val();

            if(selected_value !== previous_display) {
                this.display = selected_value;

                let post_type = jQuery("#general-post-type").val();
                let defaults = jQuery(left_filter_element).find("select[data-name='defaults']");
                let defaults_html = `<select data-name="defaults" class="all-witdh"></select>`;
                if(this.display !== "range" && this.acf_field !== "") {
                    Utils.getAcfFieldsValues(post_type, [this.acf_field]).then((response) => {
                        let blank_option = `<option value=""></option>`;
                        let options = ``;

                        let acf_values = response[this.acf_field];
                        if (acf_values.length) {
                            for (const [index, value] of Object.entries(acf_values)) {
                                options += `<option value="${value}">${value}</option>`;
                            }

                            let multiple = "";
                            if (["checkbox", "select-multiple"].includes(this.display)) {
                                multiple = `multiple="multiple"`;
                            } else {
                                options = blank_option + options;
                            }

                            defaults_html = `
                                <select data-name="defaults" class="all-witdh" ${multiple}>
                                    ${options}
                                </select>
                            `;
                        }

                        jQuery(defaults).parents("div.margin").first().html(defaults_html);
                        jQuery(left_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");
                    }).catch(() => {
                        jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    }).finally(() => {
                        this.applyEvents();
                        this.setFieldsVisibility();
                    });
                } else {
                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    jQuery(left_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");
                    this.applyEvents();
                    this.setFieldsVisibility();
                }
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

        let acf_group = jQuery(left_filter_element).find("select[data-name='acf-group']");
        let previous_acf_group = jQuery(acf_group).val();
        jQuery(acf_group).off("change").on("change", (event) => {
            let current_value = jQuery(event.currentTarget).val();
            if(current_value !== previous_acf_group) {
                this.acf_group = current_value;

                let acf_field = jQuery(left_filter_element).find("select[data-name='acf-field']");
                let acf_field_html = `<select data-name="acf-field" class="all-witdh"></select>`;
                Utils.getAcfFieldsFromGroup(this.acf_group).then((acf_fields) => {
                    let options = `<option value=""></option>`;
                    for(const [index, field] of Object.entries(acf_fields)) {
                        options += `<option value="${ field.name }" ${ (this.acf_field === field.name) ? `selected` : `` }>${ field.title } [${ field.name }]</option>`;
                    }

                    acf_field_html = `
                        <select data-name="acf-field" class="all-witdh">
                            ${ options }
                        </select>
                    `;

                    jQuery(acf_field).parents("div.margin").first().html(acf_field_html);

                }).finally(() => {
                    this.applyEvents();
                });
            }

            previous_acf_group = current_value;
        });

        let acf_field = jQuery(left_filter_element).find("select[data-name='acf-field']");
        let previous_acf_field = jQuery(acf_field).val();
        jQuery(acf_field).off("change").on("change", (event) => {
            let current_value = jQuery(event.currentTarget).val();
            if(current_value !== previous_acf_field) {
                this.acf_field = current_value;

                let defaults = jQuery(left_filter_element).find("select[data-name='defaults']");
                let defaults_html = `<select data-name="defaults" class="all-witdh"></select>`;
                Utils.getAcfFieldsValues(post_type_value, [this.acf_field]).then((response) => {
                    let blank_option = `<option value=""></option>`;

                    let options = ``;
                    let acf_values = response[this.acf_field];
                    if(acf_values.length) {
                        for (const [index, value] of Object.entries(acf_values)) {
                            options += `<option value="${value}" ${(this.defaults.includes(value)) ? `selected` : ``}>${value}</option>`;
                        }

                        let multiple = "";
                        if (["checkbox", "select-multiple"].includes(this.display)) {
                            multiple = `multiple="multiple"`;
                        } else {
                            options = blank_option + options;
                        }

                        defaults_html = `
                            <select data-name="defaults" class="all-witdh" ${multiple}>
                                ${options}
                            </select>
                        `;
                    }

                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    jQuery(left_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");

                }).catch(() => {
                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                }).finally(() => {
                    this.applyEvents();
                });
            }

            previous_acf_field = current_value;
        });

        let unit = jQuery(left_filter_element).find("input[data-name='unit']");
        jQuery(unit).off("keyup keypress blur change cut paste").on("keyup keypress blur change cut paste", (event) => {
            let unit_element = jQuery(event.currentTarget);
            let current_value = jQuery(unit_element).val();
            let unit_lang = jQuery(unit_element).data("lang");

            this.addUnit(current_value, unit_lang);
        });

        let step = jQuery(left_filter_element).find("input[data-name='step']");
        jQuery(step).off("keyup keypress blur change cut paste").on("keyup keypress blur change cut paste", (event) => {
            let step_element = jQuery(event.currentTarget);
            this.step = jQuery(step_element).val();
        });

        let defaults = jQuery(left_filter_element).find("select[data-name='defaults']");
        jQuery(defaults).off("change").on("change", (event) => {
            this.defaults = jQuery(event.currentTarget).val();
        });

        jQuery('#left-filters select[multiple=multiple]').multipleSelect("refresh");
    }

    getFilterElement() {
        return jQuery(`#left-filters .left-filters-container .filter.acf[data-id='${ this.id }']`).first();
    }

    setFieldsVisibility() {
        let left_filter_element = this.getFilterElement();
        let defaults = jQuery(left_filter_element).find("select[data-name='defaults']");
        let step = jQuery(left_filter_element).find("input[data-name='step']");

        if(["range"].includes(this.display)) {
            jQuery(defaults).parents("div.defaults").first().addClass("invisible");
            jQuery(step).parents("div.step").first().removeClass("invisible");
        } else {
            jQuery(defaults).parents("div.defaults").first().removeClass("invisible");
            jQuery(step).parents("div.step").first().addClass("invisible");
        }
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

    get acf_group() {
        return this._acf_group;
    }

    set acf_group(value) {
        this._acf_group = parseInt(value, 10);
        FilterUtils.triggerUpdate();
    }

    get acf_field() {
        return this._acf_field;
    }

    set acf_field(value) {
        this._acf_field = String(value);
        FilterUtils.triggerUpdate();
    }

    get unit() {
        return this._unit;
    }

    set unit(value) {
        if(!(value instanceof Object)) {
            value = {};
        }

        this._unit = value;
        FilterUtils.triggerUpdate();
    }

    addUnit(value, lang) {
        this.unit[lang] = String(value);
        FilterUtils.triggerUpdate();
    }

    get step() {
        return this._step;
    }

    set step(value) {
        this._step = Number(value);
        FilterUtils.triggerUpdate();
    }

    get defaults() {
        return this._defaults;
    }

    set defaults(value) {
        if(!Array.isArray(value)) {
            value = [value];
        }

        value.forEach((val, index) => {
            value[index] = String(val);
        });

        this._defaults = value;
        FilterUtils.triggerUpdate();
    }

    get langs() {
        return this._langs;
    }
}
