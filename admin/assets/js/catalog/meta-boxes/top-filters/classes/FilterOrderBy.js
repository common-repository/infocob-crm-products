import {FilterUtils} from "./FilterUtils.js";
import {Utils} from "../../../../Utils.js";
import {OrderBy} from "./order/OrderBy.js";
import Sortable from "../../../../../../../node_modules/sortablejs/modular/sortable.complete.esm.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilterOrderBy {
    static id = 0;

    constructor() {
        this._id = FilterOrderBy.id;
        this._orders_by = [];

        FilterOrderBy.id++;
    }

    load(filterOrderBy) {
        if(filterOrderBy !== undefined && filterOrderBy.length > 0) {
            filterOrderBy.forEach((config_order_by, index) => {
                let order_by = new OrderBy();
                order_by.custom_order = index;
                order_by.load(config_order_by);
                this.addOrderBy(order_by);
            });
        }
    }

    toJSON() {
        return Object.assign({}, {
            orders_by: this.orders_by,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            let orders_by_html = '';
            this.orders_by.forEach((order_by) => {
                if(order_by instanceof OrderBy) {
                    promises.push(order_by.toHTML().then((response) => {
                        orders_by_html += response;
                    }));
                }
            });

            Promise.allSettled(promises).then(() => {
                // language=html
                resolve(`
                    <div class="filter order-by" data-id="${ this.id }">	
						<div class="content-products-order-by">
							${ orders_by_html }
						</div>
	
						<div class="actions-container add">
							<button type="button" class="add-order-by">${ _x("Add order by", "Admin view catalog post, meta-box 'top-filters'", 'infocob-crm-products') }</button>
						</div>
					</div>
                `);
            });
        });
    }

    applyEvents() {
        let top_filter_element = this.getFilterElement();

        this.orders_by.forEach((order_by, index) => {
            if(order_by instanceof OrderBy) {
                order_by.applyEvents();

                jQuery(order_by).off("del-order-by").on("del-order-by", (event, data) => {
                    let id = data.id ?? false;
                    if(id !== false) {
                        this.delOrderByById(id);
                    }
                });
            }
        });

        let addOrderBy = jQuery(top_filter_element).find(".actions-container.add > button.add-order-by");
        jQuery(addOrderBy).off("click").on("click", (event) => {
            event.preventDefault();

            let order_by = new OrderBy();
            order_by.toHTML().then((order_by_html) => {
                jQuery(top_filter_element).find(".content-products-order-by").append(order_by_html);
                order_by.applyEvents();
                this.addOrderBy(order_by);

                jQuery(order_by).off("del-order-by").on("del-order-by", (event, data) => {
                    let id = data.id ?? false;
                    if(id !== false) {
                        this.delOrderByById(id);
                        FilterUtils.triggerUpdate();
                    }
                });
            });
        });

        /*
         * BEGIN Sorter events
         */
        let sortable_container = jQuery(top_filter_element).find(".content-products-order-by");
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

        Utils.initTooltips();
    }

    getFilterElement() {
        return jQuery(`#top-filters .top-filters-container .filter.order-by[data-id='${ this.id }']`).first();
    }

    updateOrder() {
        if(this.orders_by !== undefined && this.orders_by.length > 0) {
            let top_filter_element = this.getFilterElement();

            let orders_by = jQuery(top_filter_element).find(".content-products-order-by .order-by-container");
            if(orders_by.length) {
                // Set orders_by object order as the html element order
                orders_by.each((index, order_by_element) => {
                    let order_by_id = jQuery(order_by_element).data("id");
                    let order_by_index = this.getOrderByIndexById(order_by_id);
                    if(order_by_index !== false) {
                        if (this.orders_by[order_by_index] instanceof OrderBy) {
                            this.orders_by[order_by_index].custom_order = index;
                        }
                    }
                });

                // Sort by the order defined above
                this.orders_by.sort((order_by_a, order_by_b) => {
                    if(order_by_a instanceof OrderBy && order_by_b instanceof OrderBy) {
                        if(order_by_a.custom_order < order_by_b.custom_order) {
                            return -1
                        } else if(order_by_a.custom_order > order_by_b.custom_order) {
                            return 1
                        }
                    }

                    return 0;
                });
            }
        }
    }

    get id() {
        return this._id;
    }

    get orders_by() {
        return this._orders_by;
    }

    set orders_by(value) {
        this._orders_by = value;
        FilterUtils.triggerUpdate();
    }

    getOrderByIndexById(id) {
        let order_by_index = false;
        this.orders_by.forEach((order_by, index) => {
            if(order_by instanceof OrderBy) {
                if(order_by.id === id) {
                    order_by_index = index;
                }
            }
        });

        return order_by_index;
    }

    addOrderBy(order_by) {
        if(order_by instanceof OrderBy) {
            this.orders_by.push(order_by);

            FilterUtils.triggerUpdate();
        }
    }

    delOrderBy(order_by) {
        if(order_by instanceof OrderBy) {
            this.orders_by =  this.orders_by.filter(function(value, index, arr){
                return value !== order_by;
            });

            FilterUtils.triggerUpdate();
        }
    }

    delOrderByById(id) {
        this.orders_by.forEach((order_by, index) => {
            if(order_by instanceof OrderBy) {
                if(order_by.id === id) {
                    this.orders_by.splice(index, 1);
                }
            }
        });

        FilterUtils.triggerUpdate();
    }
}
