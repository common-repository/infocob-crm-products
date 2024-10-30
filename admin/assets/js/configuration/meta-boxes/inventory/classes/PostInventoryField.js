import {Utils}        from "../../../../Utils.js";
import {PostInventoryUtils} from "./PostInventoryUtils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class PostInventoryField {
	static id = 0;

	constructor() {
		this._id = PostInventoryField.id;
		this._name = "";
		this._title = ""; // Not saved
		this._value = "";

		PostInventoryField.id++;
	}

	load(post_acf) {
		this.name = post_acf.name ?? "";
		this.title = post_acf.title ?? "";
		this.value = post_acf.value ?? "";
	}

	toJSON() {
		return Object.assign({}, {
			name: this.name,
			value: this.value,
		});
	}

	toHTML() {
		return `<div class="acf-field" data-id="${this.id}" data-tippy-content="${ _x("Field", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products') }">
				<div class="name">
					<label id="${ this.id + '_' + this.name }">${ this.title } [${ this.name }]</label>
					<input for="${ this.id + '_' + this.name }" data-name="value" data-field="${ this.name }" type="text" value="${ this.value }" class="autocomplete-fields-infocob">
				</div>
			</div>`
	}

	applyEvents() {
		let post_inventory_field_element = this.getPostInventoryFieldElement();

		if(post_inventory_field_element.length) {
			let name = jQuery(post_inventory_field_element).find("input[data-name='value']");
			jQuery(name).off("input").on("input", (event) => {
				this.value = event.currentTarget.value;
			});
		}
	}

	getPostInventoryFieldElement() {
		return jQuery(`#post-inventory .post-inventory-container .acf-fields .acf-field[data-id='${ this.id }']`).first();
	}

	/*
		Getters & Setters
	 */

	get id() {
		return parseInt(this._id, 10);
	}

	get name() {
		return this._name;
	}

	set name(value) {
		this._name = String(value);
		PostInventoryUtils.triggerUpdate();
	}

	get title() {
		return this._title;
	}

	set title(value) {
		this._title = String(value);
		PostInventoryUtils.triggerUpdate();
	}

	get value() {
		return this._value;
	}

	set value(value) {
		this._value = String(value);
		PostInventoryUtils.triggerUpdate();
	}
}
