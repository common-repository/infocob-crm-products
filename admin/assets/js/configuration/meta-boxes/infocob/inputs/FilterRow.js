import {Filter} from "./Filter.js";
import {Utils} from "../../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilterRow extends Filter {

    constructor(index, module) {
        super(index, module);
        this.type = "row";
        this._operator = "=";
        this._field_name = "";
    }


    toJSON() {
        return Object.assign({}, super.toJSON(), {
            operator: this.operator,
            next_condition: this.next_condition,
            field_name: this.field_name,
        });
    }

    get operator() {
        return String(this._operator);
    }

    set operator(value) {
        this._operator = String(value);
    }

    get field_name() {
        return String(this._field_name);
    }

    set field_name(value) {
        this._field_name = String(value);
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            Utils.getChampsInfocob(this.module).then((fields) => {
                let options = ``;
                for(const [field_name, label] of Object.entries(fields)) {
                    options += `<option value="${ field_name }" ${ (this.field_name === field_name) ? "selected" : "" }>${ field_name } [${ label }]</option>`;
                }

                resolve(`
                    <div class="row-filter filter" data-id="${ this.id }">
                        <div class="handle-container">
                            <svg class="handle" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="black" width="24px" height="24px"><g><rect fill="none" height="24" width="24"></rect></g><g><g><g><path d="M20,9H4v2h16V9z M4,15h16v-2H4V15z"></path></g></g></g></svg>
                        </div>
                        <div class="input custom">
                            <select data-name="field_name" class="search">
                                ${ options }
                            </select>
                        </div>
                        <div class="input custom">
                            <select data-name="operator">
                                <option value="=" ${ (this.operator === "=") ? "selected" : "" }>&#61;</option>
                                <option value="!=" ${ (this.operator === "!=") ? "selected" : "" }>&ne;</option>
                                <option value=">" ${ (this.operator === ">") ? "selected" : "" }>&#62;</option>
                                <option value="<" ${ (this.operator === "<") ? "selected" : "" }>&#60;</option>
                                <option value=">=" ${ (this.operator === ">=") ? "selected" : "" }>&#8805;</option>
                                <option value="<=" ${ (this.operator === "<=") ? "selected" : "" }>&#8804;</option>
                                <option value="like" ${ (this.operator === "like") ? "selected" : "" }>${ _x("LIKE", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</option>
                                <option value="is_null" ${ (this.operator === "is_null") ? "selected" : "" }>${ _x("IS NULL", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</option>
                                <option value="is_not_null" ${ (this.operator === "is_not_null") ? "selected" : "" }>${ _x("IS NOT NULL", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
                        <div class="input custom">
                            <input data-name="value" type="text" value="${ this.value }" ${(["is_null", "is_not_null"].includes(this.operator)) ? "disabled" : ""}/>
                        </div>
                        <div class="input">
                            <select data-name="next_condition">
                                <option value="and" ${ (this.next_condition === "and") ? "selected" : "" }>${ _x("AND", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</option>
                                <option value="or" ${ (this.next_condition === "or") ? "selected" : "" }>${ _x("OR", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
                        <div class="input">
                            <button type="button" class="del-row">${ _x("Delete", "Admin view configuration post, meta-box 'infocob'", 'infocob-crm-products') }</button>
                        </div>
                    </div>
                `);
            });
        });
    }

    updateFromElement(field_element) {
        super.updateFromElement(field_element);
        this.operator = jQuery(field_element).find("select[data-name=operator]").val() ?? "";
        this.field_name = jQuery(field_element).find("select[data-name=field_name]").val() ?? "";

        if(this.operator === "is_null" || this.operator === "is_not_null") {
            jQuery(field_element).find("input[data-name='value']").attr("disabled", true);
        } else {
            jQuery(field_element).find("input[data-name='value']").removeAttr("disabled");
        }
    }

}
