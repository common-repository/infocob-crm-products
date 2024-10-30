import {Utils}           from "../../../../Utils.js";
import {MappingsManager} from "../MappingsManager.js";
import {MappingsUtils}   from "./MappingsUtils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class Cond {
	static id = 0;

	constructor() {
		this._id = Cond.id;
		this._field_name = "";
		this._operator = "";
		this._value = "";
		this._next_condition = "";

		Cond.id++;
	}

	load(conds) {
		this.field_name = conds.field_name ?? "";
		this.operator = conds.operator ?? "";
		this.value = conds.value ?? "";
		this.next_condition = conds.next_condition ?? "and";
	}

	toJSON() {
		return Object.assign({}, {
			field_name: this.field_name,
			operator: this.operator,
			value: this.value,
			next_condition: this.next_condition,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let promises = [];
			let module_infocob = jQuery("#infocob-type-produit").val();

			let field_name_html = `<select data-name="field_name" class="search"></select>`;

			promises.push(Utils.getChampsInfocob(module_infocob).then((champs_infocob) => {
				let options = ``;
				for(const [field, label] of Object.entries(champs_infocob)) {
					options += `<option value="${ field }" ${ (field === this.field_name) ? `selected` : `` }>${ field } [${ label }]</option>`;
				}

				field_name_html = `
					<select data-name="field_name" class="search">
						${ options }
					</select>
				`;
			}));

			Promise.allSettled(promises).then(() => {
				resolve(`
                    <div class="cond-container" data-id="${ this.id }">
                        <div class="field_name">
                            ${ field_name_html }
                        </div>
                        <div class="operator">
                            <select data-name="operator">
                                <option value="=" ${ (this.operator === "=") ? `selected` : `` }>&#61;</option>
                                <option value="!=" ${ (this.operator === "!=") ? `selected` : `` }>&ne;</option>
                                <option value=">" ${ (this.operator === ">") ? `selected` : `` }>&#62;</option>
                                <option value="<" ${ (this.operator === "<") ? `selected` : `` }>&#60;</option>
                                <option value=">=" ${ (this.operator === ">=") ? `selected` : `` }>&#8805;</option>
                                <option value="<=" ${ (this.operator === "<=") ? `selected` : `` }>&#8804;</option>
                                <option value="regex" ${ (this.operator === "regex") ? `selected` : `` }>REGEX</option>
                                <option value="not_regex" ${ (this.operator === "not_regex") ? `selected` : `` }>NOT REGEX</option>
                                <option value="is_null" ${ (this.operator === "is_null") ? `selected` : `` }>${ _x("IS NULL", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</option>
                                <option value="is_not_null" ${ (this.operator === "is_not_null") ? `selected` : `` }>${ _x("IS NOT NULL", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
                        <div class="value">
                            <input type="text" data-name="value" value="${ this.value }">
                        </div>
                        <div class="next-condition">
                            <select data-name="next_condition">
                                <option value="and" ${ (this.next_condition === "and") ? `selected` : `` }>${ _x("AND", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</option>
                                <option value="or" ${ (this.next_condition === "or") ? `selected` : `` }>${ _x("OR", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</option>
                            </select>
                        </div>
                        <div class="delete">
                            <button type="button" class="del-cond">${ _x("Delete", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</button>
                        </div>
                    </div>
                `);
			});
		});
	}

	applyEvents() {
		let cond_element = this.getCondElement();

		if(cond_element.length) {
			let field_name = jQuery(cond_element).find("select[data-name='field_name']");
			jQuery(field_name).on("change", (event) => {
				this.field_name = jQuery(event.currentTarget).val();
			});

			let operator = jQuery(cond_element).find("select[data-name='operator']");
			jQuery(operator).on("change", (event) => {
				this.operator = jQuery(event.currentTarget).val();
				if(this.operator === "is_null" || this.operator === "is_not_null") {
					jQuery(cond_element).find("input[data-name='value']").attr("disabled", true);
				} else {
					jQuery(cond_element).find("input[data-name='value']").removeAttr("disabled");
				}
			});

			let value = jQuery(cond_element).find("input[data-name='value']");
			jQuery(value).on("input", (event) => {
				this.value = jQuery(event.currentTarget).val();
			});

			let next_condition = jQuery(cond_element).find("select[data-name='next_condition']");
			jQuery(next_condition).on("change", (event) => {
				this.next_condition = jQuery(event.currentTarget).val();
			});

			let delCond = jQuery(cond_element).find(".delete button.del-cond");
			jQuery(delCond).off("click").on("click", (event) => {
				event.preventDefault();
				jQuery(cond_element).remove();
				jQuery(this).trigger("del-cond", {
					id: this.id
				});
			});

			jQuery(field_name).trigger("change");
			jQuery(operator).trigger("change");
			jQuery(value).trigger("change");
			jQuery(next_condition).trigger("change");
		}
	}

	getCondElement() {
		return jQuery(`#mappings .cond-container[data-id='${ this.id }']`).first();
	}

	get id() {
		return parseInt(this._id, 10);
	}

	get field_name() {
		return this._field_name;
	}

	set field_name(value) {
		this._field_name = String(value);
	}

	get operator() {
		return this._operator;
	}

	set operator(value) {
		this._operator = String(value);
	}

	get value() {
		return this._value;
	}

	set value(value) {
		this._value = String(value);
	}

	get next_condition() {
		return this._next_condition;
	}

	set next_condition(value) {
		this._next_condition = String(value);
	}
}
