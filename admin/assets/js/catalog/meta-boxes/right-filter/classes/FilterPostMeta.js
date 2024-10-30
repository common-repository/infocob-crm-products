import {FilterUtils} from "./FilterUtils.js";
import {Utils} from "../../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilterPostMeta {
    static id = 0;

    constructor() {
        this._id = FilterPostMeta.id;
        this._display = "select";
        this._title = {};
        this._meta_key = "";
        this._unit = {};
        this._step = 1;
        this._defaults = [];
        this._langs = [];

        let langsBase64 = jQuery("#right-filters").data("langs");
        this._langs = Utils.decodeConfig(langsBase64);

        FilterPostMeta.id++;
    }

    load(filterPostMeta) {
        this.display = filterPostMeta.display ?? "select";
        this.title = filterPostMeta.title ?? {};
        this.meta_key = filterPostMeta.meta_key ?? "";
        this.unit = filterPostMeta.unit ?? {};
        this.step = filterPostMeta.step ?? 1;
        this.defaults = filterPostMeta.defaults ?? [];
    }

    toJSON() {
        return Object.assign({}, {
            display: this.display,
            title: this.title,
            meta_key: this.meta_key,
            unit: this.unit,
            step: this.step,
            defaults: this.defaults,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            let post_type = jQuery("#general-post-type").val();

            let defaults_html = `<select data-name="defaults" class="all-witdh" data-tippy-content="${ _x("Default", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }"></select>`;
            if(this.display !== "range") {
                if(this.meta_key !== "") {
                    promises.push(Utils.getPostMetaValues(post_type, [this.meta_key]).then((response) => {
                        let blank_option = `<option value=""></option>`;
                        let options = ``;

                        let meta_values = response[this.meta_key];
                        if (meta_values.length) {
                            for (const [index, value] of Object.entries(meta_values)) {
                                options += `<option value="${value}" ${(this.defaults.includes(value)) ? `selected` : ``}>${value}</option>`;
                            }

                            let multiple = "";
                            if (["checkbox", "select-multiple"].includes(this.display)) {
                                multiple = `multiple="multiple"`;
                            } else {
                                options = blank_option + options;
                            }

                            defaults_html = `
                                <select data-name="defaults" class="all-witdh" ${multiple} data-tippy-content="${ _x("Default", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }">
                                    ${options}
                                </select>
                            `;
                        }
                    }));
                }
            }

            let meta_key_list_html = ``;
            promises.push(Utils.getPostMetaValues(post_type, []).then((response) => {
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

            let titles = ``;
            let units = ``;
            this.langs.forEach((lang, index) => {
                titles += `<input class="all-witdh multilingual" data-name="title" data-lang="${ lang }" type="text" value="${ this.title[lang] ?? "" }" data-tippy-content="${ sprintf(_x("Title %s", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products'), lang) }" placeholder="${ sprintf(_x("Title %s", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products'), lang) }">`;
                units += `<input class="all-witdh multilingual" data-name="unit" data-lang="${ lang }" type="text" value="${ this.unit[lang] ?? "" }" data-tippy-content="${ sprintf(_x("Unit %s", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products'), lang) }" placeholder="${ sprintf(_x("Unit %s", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products'), lang) }">`;
            });

            Promise.allSettled(promises).then(() => {
                // language=html
                resolve(`
                    <div class="filter post-meta" data-id="${ this.id }">
                        <div class="display margin">
                            <select data-name="display" class="all-witdh" data-tippy-content="${ _x("Display", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }">
								<option value="select" ${ (this.display === "select") ? "selected" : "" }>${ _x("Select", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }</option>
								<option value="select-multiple" ${ (this.display === "select-multiple") ? "selected" : "" }>${ _x("Select multiple", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }</option>
                                <option value="checkbox" ${ (this.display === "checkbox") ? "selected" : "" }>${ _x("Checkbox", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }</option>
                                <option value="radio" ${ (this.display === "radio") ? "selected" : "" }>${ _x("Radio", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }</option>
                                <option value="range" ${ (this.display === "range") ? "selected" : "" }>${ _x("Range", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
						<div class="title margin">
							${ titles }
						</div>
                        <div class="meta-key margin">
                            <input list="list-post-meta-${ this.id }-${ this.meta_key }" autocomplete="off" class="all-witdh" data-name="meta-key" type="text" value="${ this.meta_key }" data-tippy-content="${ _x("Meta key", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }" placeholder="${ _x("Meta key", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }">
                            <datalist id="list-post-meta-${ this.id }-${ this.meta_key }">
                                ${ meta_key_list_html }
                            </datalist>
                        </div>	
						<div class="defaults margin ${ (this.display === "range") ? "invisible" : "" }">
							${ defaults_html }
						</div>
						<div class="step margin ${ (this.display !== "range") ? "invisible" : "" }">
							<input type="number" data-name="step" min="0" value="${ this.step }" class="all-witdh" data-tippy-content="${ _x("Step", "Admin view catalog post, meta-box 'right-filters'", 'infocob-crm-products') }">
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
        let right_filter_element = this.getFilterElement();

        let display = jQuery(right_filter_element).find("select[data-name='display']");
        let previous_display = jQuery(display).val();
        jQuery(display).off("change").on("change", (event) => {
            let selected_value = jQuery(event.currentTarget).val();

            if(selected_value !== previous_display) {
                this.display = selected_value;

                let post_type = jQuery("#general-post-type").val();
                let defaults = jQuery(right_filter_element).find("select[data-name='defaults']");
                let defaults_html = `<select data-name="defaults" class="all-witdh"></select>`;
                if(this.display !== "range" && this.meta_key !== "") {
                    Utils.getPostMetaValues(post_type, [this.meta_key]).then((response) => {
                        let blank_option = `<option value=""></option>`;
                        let options = ``;

                        let meta_values = response[this.meta_key];
                        if (meta_values.length) {
                            for (const [index, value] of Object.entries(meta_values)) {
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
                        jQuery(right_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");
                    }).catch(() => {
                        jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    }).finally(() => {
                        this.applyEvents();
                        this.setFieldsVisibility();
                    });
                } else {
                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    jQuery(right_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");
                    this.applyEvents();
                    this.setFieldsVisibility();
                }
            }

            previous_display = selected_value;
        });

        let title = jQuery(right_filter_element).find("input[data-name='title']");
        jQuery(title).off("keyup keypress blur change cut paste").on("keyup keypress blur change cut paste", (event) => {
            let title_element = jQuery(event.currentTarget);
            let current_value = jQuery(title_element).val();
            let title_lang = jQuery(title_element).data("lang");

            this.addTitle(current_value, title_lang);
        });

        let meta_key = jQuery(right_filter_element).find("input[data-name='meta-key']");
        let previous_meta_key = jQuery(meta_key).val();
        jQuery(meta_key).off("keyup keypress blur change cut paste").on("keyup keypress blur change cut paste", (event) => {
            let current_value = jQuery(event.currentTarget).val();
            if(current_value !== previous_meta_key) {
                this.meta_key = current_value;

                let post_type = jQuery("#general-post-type").val();
                let defaults = jQuery(right_filter_element).find("select[data-name='defaults']");
                let defaults_html = `<select data-name="defaults" class="all-witdh"></select>`;
                if(this.display !== "range" && this.meta_key !== "") {
                    Utils.getPostMetaValues(post_type, [this.meta_key]).then((response) => {
                        let blank_option = `<option value=""></option>`;
                        let options = ``;

                        let meta_values = response[this.meta_key];
                        if (meta_values.length) {
                            for (const [index, value] of Object.entries(meta_values)) {
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
                        jQuery(right_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");
                    }).catch(() => {
                        jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    }).finally(() => {
                        this.applyEvents();
                    });
                } else {
                    jQuery(defaults).parents("div.margin").first().html(defaults_html);
                    jQuery(right_filter_element).find("select[data-name='defaults'][multiple=multiple]").multipleSelect("refresh");
                    this.applyEvents();
                }

                this.applyEvents();
            }

            previous_meta_key = current_value;
        });

        let unit = jQuery(right_filter_element).find("input[data-name='unit']");
        jQuery(unit).off("keyup keypress blur change cut paste").on("keyup keypress blur change cut paste", (event) => {
            let unit_element = jQuery(event.currentTarget);
            let current_value = jQuery(unit_element).val();
            let unit_lang = jQuery(unit_element).data("lang");

            this.addUnit(current_value, unit_lang);
        });

        let step = jQuery(right_filter_element).find("input[data-name='step']");
        jQuery(step).off("keyup keypress blur change cut paste").on("keyup keypress blur change cut paste", (event) => {
            let step_element = jQuery(event.currentTarget);
            this.step = jQuery(step_element).val();
        });

        let defaults = jQuery(right_filter_element).find("select[data-name='defaults']");
        jQuery(defaults).off("change").on("change", (event) => {
            this.defaults = jQuery(event.currentTarget).val();
        });

        jQuery('#right-filters select[multiple=multiple]').multipleSelect("refresh");
    }

    getFilterElement() {
        return jQuery(`#right-filters .right-filters-container .filter.post-meta[data-id='${ this.id }']`).first();
    }

    setFieldsVisibility() {
        let right_filter_element = this.getFilterElement();
        let defaults = jQuery(right_filter_element).find("select[data-name='defaults']");
        let step = jQuery(right_filter_element).find("input[data-name='step']");

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

    get meta_key() {
        return this._meta_key;
    }

    set meta_key(value) {
        this._meta_key = String(value);
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
