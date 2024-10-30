import {Post}          from "./Post.js";
import {Cond}          from "./Cond.js";
import {GroupCond}     from "./GroupCond.js";
import {MappingsUtils} from "./MappingsUtils.js";
import {Utils}         from "../../../../Utils.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

export class Row {
	static id = 0;

	constructor() {
		this._id = Row.id;
		this._post = new Post();
		this._conds = [];

		Row.id++;
	}

	load(row) {
		if(row.post) {
			this.loadPost(row.post);
		}
		if(row.conds) {
			this.loadConds(row.conds);
		}
	}

	loadPost(post) {
		let post_object = new Post();
		post_object.load({
			post_type: post.post_type ?? "",
			taxonomy: post.taxonomy ?? "",
			categories: post.categories ?? []
		});

		this.post = post_object;
	}

	loadConds(conds) {
		conds.forEach((cond) => {
			if(cond.conds ?? false) {
				let group_cond_object = new GroupCond();
				group_cond_object.load({
					conds: cond.conds ?? [],
					next_condition: cond.next_condition ?? "and"
				});

				group_cond_object.applyEvents();

				this.conds.push(group_cond_object);
			} else {
				let cond_object = new Cond();
				cond_object.load({
					field_name: cond.field_name ?? "",
					operator: cond.operator ?? "=",
					value: cond.value ?? "",
					next_condition: cond.next_condition ?? "and"
				});

				cond_object.applyEvents();

				this.conds.push(cond_object);
			}
		});
	}

	toJSON() {
		return Object.assign({}, {
			post: this.post,
			conds: this.conds,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let post_html_promise = this.post.toHTML();
			let conds_promises = [];
			this.conds.forEach((cond, index) => {
				if(cond instanceof Cond || cond instanceof GroupCond) {
					conds_promises.push(cond.toHTML());
				}
			});

			Promise.allSettled([post_html_promise, conds_promises]).then((responses) => {
				let post_html = "";
				if(responses[0]) {
					post_html = responses[0].value ?? "";
				}

				let conds_html_promises = [];
				if(responses[1]) {
					conds_html_promises = responses[1].value ?? [];
				}

				Promise.allSettled(conds_html_promises).then((responses) => {
					let conds_html = "";
					responses.forEach((response) => {
						if(response.status === "fulfilled") {
							conds_html += response.value;
						}
					});

					resolve(`
						<div class="row closed" data-id="${ this.id }">
							${ post_html }
							<div class="conds-container">
								${ conds_html }
							</div>
							<div class="actions-container">
								<div class="row">
									<button type="button" class="add-row">${ _x("Add condition", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</button>
								</div>
								<div class="row">
									<button type="button" class="add-group-cond">${ _x("Add group", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</button>
								</div>
								<div class="row">
									<button type="button" class="del-row">${ _x("Delete", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products') }</button>
								</div>
							</div>
						</div>
					`);
				});

			});
		});
	}

	applyEvents() {
		let rowElement = this.getRowElement();

		this.conds.forEach((cond, index) => {
			if(cond instanceof Cond || cond instanceof GroupCond) {
				cond.applyEvents();

				jQuery(cond).off("del-cond").on("del-cond", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						if(cond instanceof Cond) {
							this.delCondById(id);
						} else if(cond instanceof GroupCond) {
							this.delGroupCondById(id);
						}
					}
				});
			}
		});

		if(rowElement.length) {
			let delRow = jQuery(rowElement).find(".actions-container > .row > .del-row");
			jQuery(delRow).off("click").on("click", (event) => {
				event.preventDefault();
				jQuery(rowElement).remove();
				jQuery(this).trigger("del-row", {
					id: this.id
				});
			});

			let addCond = jQuery(rowElement).find(".actions-container > .row > .add-row");
			jQuery(addCond).off("click").on("click", (event) => {
				event.preventDefault();
				let condsContainer = jQuery(rowElement).find("> .conds-container").first();
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

				let btnToggle = jQuery(rowElement).find("button.btn-toggle");
				jQuery(btnToggle).trigger("click", "open");
			});

			let addGroupCond = jQuery(rowElement).find(".actions-container > .row > .add-group-cond");
			jQuery(addGroupCond).off("click").on("click", (event) => {
				event.preventDefault();
				let condsContainer = jQuery(rowElement).find("> .conds-container").first();
				let groupCond = new GroupCond();
				groupCond.toHTML().then((html) => {
					jQuery(condsContainer).append(html);
					groupCond.applyEvents();
					this.addCond(groupCond);
				});

				jQuery(groupCond).off("del-cond").on("del-cond", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delGroupCondById(id);
					}
				});

				let btnToggle = jQuery(rowElement).find("button.btn-toggle");
				jQuery(btnToggle).trigger("click", "open");
			});
		}

		this.post.applyEvents();
	}

	getRowElement() {
		return jQuery(`#mappings .row[data-id='${ this.id }']`).first();
	}

	addCond(value) {
		if(value instanceof Cond || value instanceof GroupCond) {
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

	delGroupCondById(id) {
		this.conds.forEach((cond, index) => {
			if(cond instanceof GroupCond) {
				if(cond.id === id) {
					this.conds.splice(index, 1);
				}
			}
		});
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

	get post() {
		return this._post;
	}

	set post(value) {
		if(value instanceof Post) {
			this._post = value;
		}
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
}
