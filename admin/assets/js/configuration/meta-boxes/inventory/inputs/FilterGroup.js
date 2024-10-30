import {Filter} from "./Filter.js";
import {Utils}  from "../../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilterGroup extends Filter {

    constructor(index, module, value = []) {
        super(index, module);
        super.type = "group";
        super.value = value;
    }

    toJSON() {
        let json = super.toJSON();

        return Object.assign({}, json, {})
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let filtersPromises = [];
            this.value.forEach((value) => {
                if(value instanceof Filter) {
                    filtersPromises.push(value.toHTML());
                }
            });

            Promise.all(filtersPromises).then((responses) => {
                let filters = "";
                responses.forEach((filter) => {
                    filters += filter;
                });

                resolve(`
                    <div class="group-filter filter" data-id="${ this.id }">
                        <div class="handle-container">
                            <svg class="handle" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="black" width="24px" height="24px"><g><rect fill="none" height="24" width="24"></rect></g><g><g><g><path d="M20,9H4v2h16V9z M4,15h16v-2H4V15z"></path></g></g></g></svg>
                        </div>
                        <div class="content-filter">
                            ${ filters }
                        </div>
                        <div class="input">
                            <select data-name="next_condition" data-tippy-content="${ _x("Next condition", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }">
                                <option value="and" ${ (this.next_condition === "and") ? "selected" : "" }>${ _x("AND", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</option>
                                <option value="or" ${ (this.next_condition === "or") ? "selected" : "" }>${ _x("OR", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
                        <div class="input">
                            <button type="button" class="del-group">${ _x("Delete", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</button>
                        </div>
                    </div>
                `);
            });
        });
    }

    updateFromElement(field_element) {
        this.next_condition = jQuery(field_element).find("> .input select[data-name=next_condition]").val() ?? "";
    }
}
