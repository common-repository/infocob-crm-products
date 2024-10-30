import {OrderByUtils} from "./OrderByUtils.js";
import {Utils} from "../../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class OrderBy {
    static id = 0;

    constructor() {
        this._custom_order = 0;
        this._id = OrderBy.id;
        this._order = "DESC";
        this._order_by = "date";
        this._meta_key = "";

        OrderBy.id++;
    }

    load(orderBy) {
        this.order = orderBy.order ?? "DESC";
        this.order_by = orderBy.order_by ?? "date";
        this.meta_key = orderBy.meta_key ?? "";
    }

    toJSON() {
        return Object.assign({}, {
            order: this.order,
            order_by: this.order_by,
            meta_key: this.meta_key,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            let post_type = jQuery("#general-post-type").val();

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

            Promise.allSettled(promises).then(() => {
                resolve(`
                    <div class="products-order-by-container" data-id="${ this.id }">
                        <div class="handle-container">
                            <svg class="handle" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="black" width="24px" height="24px"><g><rect fill="none" height="24" width="24"></rect></g><g><g><g><path d="M20,9H4v2h16V9z M4,15h16v-2H4V15z"></path></g></g></g></svg>
                        </div>
                        <div class="order-by margin">
                            <select data-name="order-by" class="all-witdh">
                                <option value="ID" ${(this.order_by === "ID") ? `selected` : ``}>${ _x("ID", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="author" ${(this.order_by === "author") ? `selected` : ``}>${ _x("Author", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="title" ${(this.order_by === "title") ? `selected` : ``}>${ _x("Title", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="name" ${(this.order_by === "name") ? `selected` : ``}>${ _x("Name", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="date" ${(this.order_by === "date") ? `selected` : ``}>${ _x("Date", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="modified" ${(this.order_by === "modified") ? `selected` : ``}>${ _x("Modified", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="parent" ${(this.order_by === "parent") ? `selected` : ``}>${ _x("Parent", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="rand" ${(this.order_by === "rand") ? `selected` : ``}>${ _x("Rand", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="comment_count" ${(this.order_by === "comment_count") ? `selected` : ``}>${ _x("Comment count", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="menu_order" ${(this.order_by === "menu_order") ? `selected` : ``}>${ _x("Menu order", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="meta_value" ${(this.order_by === "meta_value") ? `selected` : ``}>${ _x("Meta value", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="meta_value_num" ${(this.order_by === "meta_value_num") ? `selected` : ``}>${ _x("Meta value num", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
                        <div class="order margin">
                            <select data-name="order" class="all-witdh">
                                <option value="ASC" ${(this.order === "ASC") ? `selected` : ``}>${ _x("ASC", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                                <option value="DESC" ${(this.order === "DESC") ? `selected` : ``}>${ _x("DESC", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
                        <div class="meta-key margin">
                            <input list="list-products-order-by-${ this.id }-${ this.meta_key }" autocomplete="off" type="text" data-name="meta-key" class="all-witdh" value="${ this.meta_key }" placeholder="${ _x("Meta key", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products') }">
                            <datalist id="list-products-order-by-${ this.id }-${ this.meta_key }">
                                ${ meta_key_list_html }
                            </datalist>
                        </div>
                        <div class="actions-container">
                            <div class="products-order-by">
                                <button type="button" class="del-order-by">${_x("Delete", "Admin view catalog post, meta-box 'products'", 'infocob-crm-products')}</button>
                            </div>
                        </div>
                    </div>
                `);
            });
        });
    }

    applyEvents() {
        let order_by_element = this.getOrderByElement();

        let order = jQuery(order_by_element).find("select[data-name='order']");
        let previous_order = jQuery(order).val();
        jQuery(order).off("change").on("change", (event) => {
            let selected_value = jQuery(event.currentTarget).val();
            if(selected_value !== previous_order) {
               this.order = selected_value;
            }

            previous_order = selected_value;
        });

        let meta_key = jQuery(order_by_element).find("input[data-name='meta-key']");
        let previous_meta_key = jQuery(meta_key).val();
        jQuery(meta_key).off("keyup keypress blur change cut paste").on("change", (event) => {
            let selected_value = jQuery(event.currentTarget).val();
            if(selected_value !== previous_meta_key) {
                this.meta_key = selected_value;
            }

            previous_meta_key = selected_value;
        });

        let order_by = jQuery(order_by_element).find("select[data-name='order-by']");
        let previous_order_by = jQuery(order_by).val();

        if(previous_order_by === "meta_value" || previous_order_by === "meta_value_num") {
            jQuery(meta_key).removeAttr("disabled");
        } else {
            jQuery(meta_key).attr("disabled", true);
        }

        jQuery(order_by).off("change").on("change", (event) => {
            let selected_value = jQuery(event.currentTarget).val();
            if(selected_value !== previous_order_by) {

                if(selected_value === "meta_value" || selected_value === "meta_value_num") {
                    jQuery(meta_key).removeAttr("disabled");
                } else {
                    jQuery(meta_key).attr("disabled", true);
                }

                this.order_by = selected_value;
            }

            previous_order_by = selected_value;
        });

        let delOrderBy = jQuery(order_by_element).find(".actions-container > .products-order-by > .del-order-by");
        jQuery(delOrderBy).off("click").on("click", (event) => {
            event.preventDefault();
            jQuery(order_by_element).remove();
            jQuery(this).trigger("del-order-by", {
                id: this.id
            });
        });

        jQuery('#products-order-by select[multiple=multiple]').multipleSelect("refresh");
    }

    getOrderByElement() {
        return jQuery(`#products-order-by .products-order-by-container[data-id='${ this.id }']`).first();
    }

    get custom_order() {
        return this._custom_order;
    }

    set custom_order(value) {
        this._custom_order = Number(value);
    }

    get id() {
        return this._id;
    }

    get order() {
        return this._order;
    }

    set order(value) {
        this._order = String(value);
        OrderByUtils.triggerUpdate();
    }

    get order_by() {
        return this._order_by;
    }

    set order_by(value) {
        this._order_by = String(value);
        OrderByUtils.triggerUpdate();
    }

    get meta_key() {
        return this._meta_key;
    }

    set meta_key(value) {
        this._meta_key = String(value);
        OrderByUtils.triggerUpdate();
    }
}
