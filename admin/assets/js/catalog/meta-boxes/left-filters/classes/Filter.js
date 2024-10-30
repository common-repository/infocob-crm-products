import {FilterPostMeta} from "./FilterPostMeta.js";
import {FilterUtils} from "./FilterUtils.js";
import {FilterTaxonomy} from "./FilterTaxonomy.js";
import {FilterAcf} from "./FilterAcf.js";
import {FilterOrderBy} from "./FilterOrderBy.js";
import {FilterButtonFilter} from "./FilterButtonFilter.js";
import {FilterButtonReset} from "./FilterButtonReset.js";
import {Utils} from "../../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class Filter {
    static id = 0;

    constructor() {
        this._order = 0;
        this._id = Filter.id;
        this._type = "post_meta";
        this._filter = new FilterPostMeta();

        this._acf_enabled = Boolean(jQuery("#left-filters").data("acf"));

        Filter.id++;
    }

    load(filterPostMeta) {
        this.type = filterPostMeta.type ?? "post_meta";
        this.loadFilter(filterPostMeta.filter);
    }

    loadFilter(filterPostMeta) {
        if(this.type === "taxonomy") {
            this.type = "taxonomy";

            let filter = new FilterTaxonomy();
            filter.display = filterPostMeta.display ?? "select";
            filter.title = filterPostMeta.title ?? {};
            filter.taxonomy = filterPostMeta.taxonomy ?? "";
            filter.defaults = filterPostMeta.defaults ?? [];

            this.filter = filter;
        } else if(this.type === "acf" && this.acf_enabled) {
            this.type = "acf";

            let filter = new FilterAcf();
            filter.display = filterPostMeta.display ?? "select";
            filter.title = filterPostMeta.title ?? {};
            filter.acf_group = filterPostMeta.acf_group ?? "";
            filter.acf_field = filterPostMeta.acf_field ?? "";
            filter.unit = filterPostMeta.unit ?? {};
            filter.step = filterPostMeta.step ?? 1;
            filter.defaults = filterPostMeta.defaults ?? [];

            this.filter = filter;
        } else if(this.type === "order_by") {
            this.type = "order_by";

            let filter = new FilterOrderBy();
            filter.load(filterPostMeta.orders_by ?? []);

            this.filter = filter;
        } else if(this.type === "button_filter") {
            this.type = "button_filter";

            let filter = new FilterButtonFilter();
            filter.title = filterPostMeta.title ?? {};

            this.filter = filter;
        } else if(this.type === "button_reset") {
            this.type = "button_reset";

            let filter = new FilterButtonReset();
            filter.title = filterPostMeta.title ?? {};

            this.filter = filter;
        } else {
            this.type = "post_meta";

            let filter = new FilterPostMeta();
            filter.display = filterPostMeta.display ?? "select";
            filter.title = filterPostMeta.title ?? {};
            filter.meta_key = filterPostMeta.meta_key ?? "";
            filter.unit = filterPostMeta.unit ?? {};
            filter.step = filterPostMeta.step ?? 1;
            filter.defaults = filterPostMeta.defaults ?? [];

            this.filter = filter;
        }
    }

    toJSON() {
        return Object.assign({}, {
            type: this.type,
            filter: this.filter,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            let filter_html = "";
            if (this.filter instanceof FilterPostMeta || this.filter instanceof FilterTaxonomy || this.filter instanceof FilterAcf || this.filter instanceof FilterOrderBy || this.filter instanceof FilterButtonFilter || this.filter instanceof FilterButtonReset) {
                promises.push(this.filter.toHTML().then((html) => {
                    filter_html = html;
                }));
            }

            Promise.allSettled(promises).then(() => {
                resolve(`
                    <div class="left-filters-container" data-id="${ this.id }">
                        <div class="handle-container">
                            <svg class="handle" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="black" width="24px" height="24px"><g><rect fill="none" height="24" width="24"></rect></g><g><g><g><path d="M20,9H4v2h16V9z M4,15h16v-2H4V15z"></path></g></g></g></svg>
                        </div>
                        <div class="type margin">
                            <select data-name="type" class="all-witdh" data-tippy-content="${ _x("Type", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }">
                                <option value="post_meta" ${(this.type === "post_meta") ? `selected` : ``}>${ _x("Post meta", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                                <option value="taxonomy" ${(this.type === "taxonomy") ? `selected` : ``}>${ _x("Taxonomy", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                                ${ this.acf_enabled ? `<option value="acf" ${(this.type === "acf") ? `selected` : ``}>${ _x("ACF", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>` : ``}
                                <option value="order_by" ${(this.type === "order_by") ? `selected` : ``}>${ _x("Order by", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                                <option value="button_filter" ${(this.type === "button_filter") ? `selected` : ``}>${ _x("Button filter", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                                <option value="button_reset" ${(this.type === "button_reset") ? `selected` : ``}>${ _x("Button reset", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
                         ${ filter_html }
                        <div class="actions-container">
                            <div class="left-filters">
                                <button type="button" class="del-left-filter">${_x("Delete", "Admin view catalog post, meta-box 'left-filters'", 'infocob-crm-products')}</button>
                            </div>
                        </div>
                    </div>
                `);
            });
        });
    }

    updateFilter() {
        let left_filter_element = this.getLeftFilterElement();

        let filter_html = "";
        if (this.filter instanceof FilterPostMeta || this.filter instanceof FilterTaxonomy || this.filter instanceof FilterAcf || this.filter instanceof FilterOrderBy || this.filter instanceof FilterButtonFilter || this.filter instanceof FilterButtonReset) {
            this.filter.toHTML().then((html) => {
                filter_html = html;
                jQuery(left_filter_element).find(".filter").remove();
                jQuery(filter_html).insertBefore(jQuery(left_filter_element).find(".actions-container").first())

                this.applyEvents();
            });
        }
    }

    applyEvents() {
        if(this.filter instanceof FilterPostMeta || this.filter instanceof FilterTaxonomy || this.filter instanceof FilterAcf || this.filter instanceof FilterOrderBy || this.filter instanceof FilterButtonFilter || this.filter instanceof FilterButtonReset) {
            this.filter.applyEvents();
        }

        let left_filter_element = this.getLeftFilterElement();

        let type = jQuery(left_filter_element).find("select[data-name='type']");
        let previous_type = jQuery(type).val();
        jQuery(type).off("change").on("change", (event) => {
            let selected_value = jQuery(event.currentTarget).val();
            if(selected_value !== previous_type) {
                if(selected_value === "taxonomy") {
                    this.type = "taxonomy";
                    this.filter = new FilterTaxonomy();
                } else if(selected_value === "acf") {
                    this.type = "acf";
                    this.filter = new FilterAcf();
                } else if(selected_value === "order_by") {
                    this.type = "order_by";
                    this.filter = new FilterOrderBy();
                } else if(selected_value === "button_filter") {
                    this.type = "button_filter";
                    this.filter = new FilterButtonFilter();
                } else if(selected_value === "button_reset") {
                    this.type = "button_reset";
                    this.filter = new FilterButtonReset();
                } else {
                    this.type = "post_meta";
                    this.filter = new FilterPostMeta();
                }

                this.updateFilter();
            }

            previous_type = selected_value;
        });

        let delLeftFilter = jQuery(left_filter_element).find(".actions-container > .left-filters > .del-left-filter");
        jQuery(delLeftFilter).off("click").on("click", (event) => {
            event.preventDefault();
            jQuery(left_filter_element).remove();
            jQuery(this).trigger("del-left-filter", {
                id: this.id
            });
        });

        jQuery('#left-filters select[multiple=multiple]').multipleSelect("refresh");
        Utils.initTooltips();
    }

    getLeftFilterElement() {
        return jQuery(`#left-filters .left-filters-container[data-id='${ this.id }']`).first();
    }

    get order() {
        return this._order;
    }

    set order(value) {
        this._order = Number(value);
    }

    get id() {
        return this._id;
    }

    get type() {
        return this._type;
    }

    set type(value) {
        this._type = value;
        FilterUtils.triggerUpdate();
    }

    get filter() {
        return this._filter;
    }

    set filter(value) {
        if (value instanceof FilterPostMeta || value instanceof FilterTaxonomy || value instanceof FilterAcf || value instanceof FilterOrderBy || value instanceof FilterButtonFilter || value instanceof FilterButtonReset) {
            this._filter = value;
        }
        FilterUtils.triggerUpdate();
    }

    get acf_enabled() {
        return this._acf_enabled;
    }
}
