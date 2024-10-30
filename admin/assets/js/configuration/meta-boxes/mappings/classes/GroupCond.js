import {Cond}          from "./Cond.js";
import {MappingsUtils} from "./MappingsUtils.js";
import {Utils}         from "../../../../Utils.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

export class GroupCond {
	static id = 0;

	constructor() {
		this._id = GroupCond.id;
		this._conds = [];
		this._next_condition = "and";

		GroupCond.id++;
	}

	load(conds) {
		this.loadConds(conds.conds ?? []);
		this.next_condition = conds.next_condition ?? "";
	}

	loadConds(conds) {
		conds.forEach((cond) => {
			if(cond.conds ?? false) {
				let group_cond_object = new GroupCond();
				group_cond_object.load({
					conds: cond.conds ?? [],
					next_condition: cond.next_condition ?? "and"
				});

				this.conds.push(group_cond_object);
			} else {
				let cond_object = new Cond();
				cond_object.load({
					field_name: cond.field_name ?? "",
					operator: cond.operator ?? "=",
					value: cond.value ?? "",
					next_condition: cond.next_condition ?? "and"
				});

				this.conds.push(cond_object);
			}
		});
	}

	toJSON() {
		return Object.assign({}, {
			conds: this.conds,
			next_condition: this.next_condition,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let conds_html_promises = [];
			this.conds.forEach((cond, index) => {
				if(cond instanceof Cond || cond instanceof GroupCond) {
					conds_html_promises.push(cond.toHTML());
				}
			});

			Promise.allSettled(conds_html_promises).then((responses) => {
				let conds_html = "";
				responses.forEach((response) => {
					if(response.status === "fulfilled") {
						conds_html += response.value;
					}
				});

				resolve(`
					<div class="group-conds-container" data-id="${ this.id }">
						<div class="conds-container">
							${ conds_html }
						</div>
						<div class="next-condition">
							<select data-name="next_condition">
								<option value="and" ${ (this.next_condition === "and") ? `selected` : `` }>${ _x("AND", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</option>
								<option value="or" ${ (this.next_condition === "or") ? `selected` : `` }>${ _x("OR", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</option>
							</select>
						</div>
						<div class="actions-container">
							<div class="row">
								<button type="button" class="add-cond">${ _x("Add condition", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</button>
							</div>
							<div class="row">
								<button type="button" class="del-group-cond">${ _x("Delete", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</button>
							</div>
						</div>
					</div>
				`);
			});
		});
	}

	applyEvents() {
		let groupCondElement = this.getGroupCondElement();

		this.conds.forEach((cond, index) => {
			if(cond instanceof Cond || cond instanceof GroupCond) {
				cond.applyEvents();

				jQuery(cond).off("del-cond").on("del-cond", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delCondById(id);
					}
				});
			}
		});

		if(groupCondElement.length) {
			let next_condition = jQuery(groupCondElement).find("select[data-name='next_condition']");
			jQuery(next_condition).on("change", (event) => {
				this.next_condition = jQuery(event.currentTarget).val();
			});

			let addCond = jQuery(groupCondElement).find("> .actions-container button.add-cond");
			jQuery(addCond).off("click").on("click", (event) => {
				event.preventDefault();
				let condsContainer = jQuery(groupCondElement).find("> .conds-container").first();
				let cond = new Cond();
				cond.toHTML().then((html) => {
					jQuery(condsContainer).append(html);
					cond.applyEvents();
					this.addCond(cond);

					let selects_search = jQuery(condsContainer).find('select.search');
					jQuery(selects_search).multipleSelect("destroy");
					jQuery(selects_search).multipleSelect({
						filter: true,
						formatSelectAll: () => {
							return _x("[Select all]", "JS multipleSelect - formatSelectAll", 'infocob-crm-products');
						},
						formatAllSelected: () => {
							return _x('All selected', "JS multipleSelect - formatSelectAll", 'infocob-crm-products');
						},
						formatCountSelected: (count, total) => {
							return sprintf(_x('%s of %s selected', "JS multipleSelect - formatSelectAll", 'infocob-crm-products'), count, total);
						},
						formatNoMatchesFound: () => {
							return _x('No matches found', "JS multipleSelect - formatSelectAll", 'infocob-crm-products');
						},
					});
				});

				jQuery(cond).off("del-cond").on("del-cond", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delCondById(id);
					}
				});
			});

			let delCond = jQuery(groupCondElement).find("> .actions-container button.del-group-cond");
			jQuery(delCond).off("click").on("click", (event) => {
				event.preventDefault();
				jQuery(groupCondElement).remove();
				jQuery(this).trigger("del-cond", {
					id: this.id
				});
			});

			jQuery(next_condition).trigger("change");
		}
	}

	getGroupCondElement() {
		return jQuery(`#mappings .group-conds-container[data-id='${ this.id }']`).first();
	}

	addCond(value) {
		if(value instanceof Cond) {
			this.conds.push(value);
		} else {
			console.error(_x("Unable to define value", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products'));
		}
	}

	delCond(value) {
		if(value instanceof Cond) {
			for(var i = 0; i < this.conds.length; i++) {
				if(this.conds[i] === value) {
					this.conds.splice(i, 1);
				}
			}
		}
	}

	delCondById(id) {
		this.conds.forEach((cond, index) => {
			if(cond instanceof Cond) {
				if(cond.id === id) {
					this.conds.splice(index, 1);
				}
			}
		});
	}

	get id() {
		return parseInt(this._id, 10);
	}

	get conds() {
		return this._conds;
	}

	set conds(value) {
		if(Array.isArray(value)) {
			this._conds = value;
		} else {
			this._conds = [];
		}
	}

	get next_condition() {
		return this._next_condition;
	}

	set next_condition(value) {
		this._next_condition = String(value);
	}
}
