import {Filter} from "./classes/Filter.js";
import {Utils} from "../../../Utils.js";
import {FilterUtils} from "./classes/FilterUtils.js";
import Sortable from "../../../../../../node_modules/sortablejs/modular/sortable.complete.esm.js";
import {FilterOrderBy} from "../left-filters/classes/FilterOrderBy.js";

const {__, _x, _n, _nx} = wp.i18n;

export class RightFiltersManager {

    constructor(post_type) {
        this._post_type = post_type;
        this._filters = [];
    }

    saveEvent() {
        jQuery("#right-filters").off("update").on("update", () => {
            this.updateOrder();

            let base64Json = Utils.encodeConfig(this);
            jQuery("#right-filters > input[name='right-filters']").val(base64Json);
        });
    }

    load(configBase64) {
        jQuery("#right-filters > .content-right-filters").html("");
        jQuery("#right-filters .icp-loader").addClass("active");

        let config = Utils.decodeConfig(configBase64);

        if(config.filters !== undefined && config.filters.length > 0) {
            config.filters.forEach((config_filter, index) => {
                let filter = new Filter();
                filter.order = index;
                filter.load(config_filter);
                this.addFilter(filter);
            });
        }
    }

    render() {
        this.toHTML().then((html) => {
            jQuery("#right-filters > .content-right-filters").html(html);

            this.applyEvents();

            jQuery("#right-filters .icp-loader").removeClass("active");
        });
    }

    toJSON() {
        return Object.assign({}, {
            post_type: this.post_type,
            filters: this.filters,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            this.filters.forEach((filter) => {
                if(filter instanceof Filter) {
                    promises.push(filter.toHTML());
                }
            });

            if(promises.length > 0) {
                Promise.all(promises).then((responses) => {
                    let filters_html = "";
                    responses.forEach((html) => {
                        filters_html += html;
                    });

                    resolve(filters_html);
                });
            } else {
                resolve();
            }
        });
    }

    applyEvents() {
        this.filters.forEach((filter, index) => {
            if(filter instanceof Filter) {
                filter.applyEvents();

                jQuery(filter).off("del-right-filter").on("del-right-filter", (event, data) => {
                    let id = data.id ?? false;
                    if(id !== false) {
                        this.delFilterById(id);
                    }
                });

                jQuery(filter).off("update-order").on("update-order", (event, data) => {
                    this.updateOrder();
                });
            }
        });

        let addFilter = jQuery("#right-filters > div.actions-container > button.add-right-filter");
        jQuery(addFilter).off("click").on("click", (event) => {
            event.preventDefault();

            let filter = new Filter();
            filter.toHTML().then((filter_html) => {
                jQuery("#right-filters > .content-right-filters").append(filter_html);
                filter.applyEvents();
                this.addFilter(filter);

                jQuery(filter).off("del-right-filter").on("del-right-filter", (event, data) => {
                    let id = data.id ?? false;
                    if(id !== false) {
                        this.delFilterById(id);
                        FilterUtils.triggerUpdate();
                    }
                });

                jQuery("#right-filters-enable").prop("checked", true);
            });
        });

        /*
         * BEGIN Sorter events
         */
        let sortable_container = jQuery("#right-filters .content-right-filters");
        if(sortable_container.length) {
            new Sortable(sortable_container[0], {
                handle: ".handle-container",
                animation: 150,
                ghostClass: 'blue-background-class',
                onSort: (event) => {
                    this.updateOrder();
                },
            });
        }

        /*
         * END
         */

        this.saveEvent();

        Utils.initTooltips();
    }

    updateOrder() {
        if(this.filters !== undefined && this.filters.length > 0) {
            this.filters.forEach((filter) => {
                if(filter instanceof Filter) {
                    if(filter.filter instanceof FilterOrderBy) {
                        filter.filter.updateOrder();
                    }
                }
            });

            let filters = jQuery("#right-filters .right-filters-container");
            if(filters.length) {
                // Set filters object order as the html element order
                filters.each((index, filter_element) => {
                    let filter_id = jQuery(filter_element).data("id");
                    let filter_index = this.getFilterIndexById(filter_id);
                    if(filter_index !== false) {
                        if (this.filters[filter_index] instanceof Filter) {
                            this.filters[filter_index].order = index;
                        }
                    }
                });

                // Sort by the order defined above
                this.filters.sort((filter_a, filter_b) => {
                    if(filter_a instanceof Filter && filter_b instanceof Filter) {
                        if(filter_a.order < filter_b.order) {
                            return -1
                        } else if(filter_a.order > filter_b.order) {
                            return 1
                        }
                    }

                    return 0;
                });
            }
        }
    }

    get post_type() {
        return this._post_type;
    }

    get filters() {
        return this._filters;
    }

    set filters(value) {
        this._filters = value;
        FilterUtils.triggerUpdate();
    }

    getFilterIndexById(id) {
        let filter_index = false;
        this.filters.forEach((filter, index) => {
            if(filter instanceof Filter) {
                if(filter.id === id) {
                    filter_index = index;
                }
            }
        });

        return filter_index;
    }

    addFilter(filter) {
        if(filter instanceof Filter) {
            this.filters.push(filter);

            FilterUtils.triggerUpdate();
        }
    }

    delFilter(filter) {
        if(filter instanceof Filter) {
            this.filters =  this.filters.filter(function(value, index, arr){
                return value !== filter;
            });

            FilterUtils.triggerUpdate();
        }
    }

    delFilterById(id) {
        this.filters.forEach((filter, index) => {
            if(filter instanceof Filter) {
                if(filter.id === id) {
                    this.filters.splice(index, 1);
                }
            }
        });

        FilterUtils.triggerUpdate();
    }
}
