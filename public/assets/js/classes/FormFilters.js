export class FormFilters {

    constructor(post_id) {
        this._post_id = post_id;
        this._get_parameters = "";
        this._left_filters = "";
        this._right_filters = "";
        this._top_filters = "";
    }

    get left_filters() {
        let formElement = jQuery(`div.infocob-crm-products.left.filters.${this.post_id} > form`);
        this._left_filters = jQuery(formElement).serialize();

        return this._left_filters;
    }

    get right_filters() {
        let formElement = jQuery(`div.infocob-crm-products.right.filters.${this.post_id} > form`);
        this._right_filters = jQuery(formElement).serialize();

        return this._right_filters;
    }

    get top_filters() {
        let formElement = jQuery(`div.infocob-crm-products.top.filters.${this.post_id} > form`);
        this._top_filters = jQuery(formElement).serialize();

        return this._top_filters;
    }

    get get_parameters() {
        let parameters = this.left_filters + '&' + this.right_filters + '&' + this.top_filters;
        let encodedParameters = window.btoa(parameters);

        let current_url = new URL(window.location.href);
        let path = current_url?.pathname ?? "";
        let regex = new RegExp('(?<path>page\\/[0-9]+\\/?)$', 'mi');
        current_url.pathname = path.replace(regex, '');

        current_url.searchParams.set("infocob-crm-products", encodedParameters);
        this._get_parameters = current_url.href;

        return this._get_parameters;
    }

    get post_id() {
        return this._post_id;
    }
}
