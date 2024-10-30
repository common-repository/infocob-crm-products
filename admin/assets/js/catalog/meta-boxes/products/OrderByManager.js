import {Utils} from "../../../Utils.js";
import {OrderByUtils} from "./classes/OrderByUtils.js";
import Sortable from "../../../../../../node_modules/sortablejs/modular/sortable.complete.esm.js";
import {OrderBy} from "./classes/OrderBy.js";

const {__, _x, _n, _nx} = wp.i18n;

export class OrderByManager {

    constructor(post_type) {
        this._post_type = post_type;
        this._orders_by = [];
    }

    saveEvent() {
        jQuery("#products-order-by").off("update").on("update", () => {
            this.updateOrder();

            let base64Json = Utils.encodeConfig(this);
            jQuery("#products-order-by > input[name='products-order-by']").val(base64Json);
        });
    }

    load(configBase64) {
        jQuery("#products-order-by > .content-products-order-by").html("");
        jQuery("#products-order-by .icp-loader").addClass("active");

        let config = Utils.decodeConfig(configBase64);

        if(config.orders_by !== undefined && config.orders_by.length > 0) {
            config.orders_by.forEach((config_order_by, index) => {
                let order_by = new OrderBy();
                order_by.custom_order = index;
                order_by.load(config_order_by);
                this.addOrderBy(order_by);
            });
        }
    }

    render() {
        this.toHTML().then((html) => {
            jQuery("#products-order-by > .content-products-order-by").html(html);

            this.applyEvents();

            jQuery("#products-order-by .icp-loader").removeClass("active");
        });
    }

    toJSON() {
        return Object.assign({}, {
            post_type: this.post_type,
            orders_by: this.orders_by,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            this.orders_by.forEach((order_by) => {
                if(order_by instanceof OrderBy) {
                    promises.push(order_by.toHTML());
                }
            });

            if(promises.length > 0) {
                Promise.all(promises).then((responses) => {
                    let order_by_html = "";
                    responses.forEach((html) => {
                        order_by_html += html;
                    });

                    resolve(order_by_html);
                });
            } else {
                resolve();
            }
        });
    }

    applyEvents() {
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

        let addOrderBy = jQuery("#products-order-by > div.actions-container > button.add-order-by");
        jQuery(addOrderBy).off("click").on("click", (event) => {
            event.preventDefault();

            let order_by = new OrderBy();
            order_by.toHTML().then((order_by_html) => {
                jQuery("#products-order-by > .content-products-order-by").append(order_by_html);
                order_by.applyEvents();
                this.addOrderBy(order_by);

                jQuery(order_by).off("del-order-by").on("del-order-by", (event, data) => {
                    let id = data.id ?? false;
                    if(id !== false) {
                        this.delOrderByById(id);
                        OrderByUtils.triggerUpdate();
                    }
                });
            });
        });

        /*
         * BEGIN Sorter events
         */
        let sortable_container = jQuery("#products-order-by .content-products-order-by");
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
        if(this.orders_by !== undefined && this.orders_by.length > 0) {
            let orders_by = jQuery("#products-order-by .products-products-order-by");
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

    get post_type() {
        return this._post_type;
    }

    get orders_by() {
        return this._orders_by;
    }

    set orders_by(value) {
        this._orders_by = value;
        OrderByUtils.triggerUpdate();
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

            OrderByUtils.triggerUpdate();
        }
    }

    delOrderBy(order_by) {
        if(order_by instanceof OrderBy) {
            this.orders_by =  this.orders_by.filter(function(value, index, arr){
                return value !== order_by;
            });

            OrderByUtils.triggerUpdate();
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

        OrderByUtils.triggerUpdate();
    }
}
