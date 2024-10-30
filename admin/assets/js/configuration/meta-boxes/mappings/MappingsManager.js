import {Row} from "./classes/Row.js";
import {MappingsUtils} from "./classes/MappingsUtils.js";
import {Utils} from "../../../Utils.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

var post_types_loaded = [];
var taxonomies_loaded = [];

export class MappingsManager {

	constructor() {
		this._rows = [];
	}

	saveEvent() {
		let mappingsElement = jQuery("#mappings");
		jQuery(mappingsElement).off("update").on("update", () => {
			let base64Json = Utils.encodeConfig(this);
			jQuery("#mappings > input[name='mappings']").val(base64Json);
		});

		jQuery(mappingsElement).off("add-row").on("add-row", (event, row_id) => {
			if(row_id !== undefined) {
				let existing_row = this.getRowById(row_id);
				if(existing_row instanceof Row) {
					let row = new Row();
					row.load(existing_row);
					row.toHTML().then((html) => {
						jQuery("#mappings > .content-mappings").append(html);
						row.applyEvents();
						this.addRow(row);

						jQuery(row).off("del-row").on("del-row", (event, data) => {
							let id = data.id ?? false;
							if(id !== false) {
								this.delRowById(id);
							}
						});

						jQuery(`#mappings > .content-mappings .row[data-id="${row.id}"] select[multiple=multiple]`).multipleSelect("refresh");

						jQuery([document.documentElement, document.body]).animate({
							scrollTop: jQuery(`#mappings > .content-mappings .row[data-id="${row.id}"]`).offset().top - 100
						}, 2000);
					});
				}
			}
		});

		jQuery("#post").off("submit.icp").on("submit.icp", () => {
			MappingsUtils.triggerUpdate();
		});
	}

	load(configBase64) {
		jQuery("#mappings > .content-mappings").html("");
		jQuery("#mappings .icp-loader").addClass("active");
		let config = Utils.decodeConfig(configBase64);

		if(config.rows !== undefined && config.rows.length > 0) {
			config.rows.forEach((config_row) => {
				let row = new Row();
				row.load(config_row);
				this.addRow(row);

				if("post" in row) {
					if("post_type" in row.post) {
						post_types_loaded.push(row.post.post_type);
					}
					if("taxonomy" in row.post) {
						taxonomies_loaded.push(row.post.taxonomy);
					}
				}
			});

			post_types_loaded = post_types_loaded.filter((v, i, a) => a.indexOf(v) === i); // Unique array
			taxonomies_loaded = taxonomies_loaded.filter((v, i, a) => a.indexOf(v) === i); // Unique array
		}
	}

	render() {
		this.toHTML().then((html) => {
			jQuery("#mappings > .content-mappings").html(html);

			this.applyEvents();

			jQuery('#mappings select[multiple=multiple]').multipleSelect("refresh");
			let selects_search = jQuery("#mappings > .content-mappings select.search");
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

			jQuery("#mappings .icp-loader").removeClass("active");
		});
	}

	applyEvents() {
		this.rows.forEach((row, index) => {
			if(row instanceof Row) {
				row.applyEvents();

				jQuery(row).off("del-row").on("del-row", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delRowById(id);
					}
				});
			}
		});

		let addRow = jQuery("#mappings > div.actions-container > button.add-row");
		jQuery(addRow).off("click").on("click", (event) => {
			event.preventDefault();

			let row = new Row();
			row.toHTML().then((html) => {
				jQuery("#mappings > .content-mappings").append(html);
				row.applyEvents();
				this.addRow(row);

				jQuery(row).off("del-row").on("del-row", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delRowById(id);
					}
				});

				jQuery(`#mappings > .content-mappings .row[data-id="${row.id}"] select[multiple=multiple]`).multipleSelect("refresh");
			});
		});

		this.saveEvent();
	}

	toJSON() {
		return Object.assign({}, {
			rows: this.rows,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let first_requests_promises = [];

			// Use to caching the first request
			post_types_loaded.forEach((post_type) => {
				first_requests_promises.push(Utils.getTaxonomiesFromPostType(post_type));
			});
			taxonomies_loaded.forEach((taxonomy) => {
				first_requests_promises.push(Utils.getCategoriesFromTaxonomy(taxonomy));
			});

			Promise.allSettled(first_requests_promises).finally(() => {
				let promises = [];

				this.rows.forEach((row) => {
					if (row instanceof Row) {
						promises.push(row.toHTML());
						// @TODO
						/*
						/*.then(() => {
							jQuery(`#mappings > .content-mappings .row[data-id="${row.id}"] select[multiple=multiple]`).multipleSelect("refresh");
							let selects_search = jQuery(`#mappings > .content-mappings .row[data-id="${row.id}"] select.search`);
							jQuery(selects_search).multipleSelect({
								filter: true
							});
						})*/
					}
				});

				if (promises.length > 0) {
					Promise.all(promises).then((responses) => {
						let rows_html = "";
						responses.forEach((row_html) => {
							rows_html += row_html;
						});

						resolve(rows_html);
					});
				} else {
					resolve();
				}
			}).catch(() => {
				resolve();
			});
		});
	}

	addRow(value) {
		if(value instanceof Row) {
			this.rows.push(value);

		} else {
			console.error(_x("Unable to define value", "Admin view configuration post, meta-box 'mappings'", 'infocob-crm-products'));
		}
	}

	delRow(value) {
		if(value instanceof Row) {
			for( var i = 0; i < this.rows.length; i++){
				if ( this.rows[i] === value) {
					this.rows.splice(i, 1);
				}
			}
		}
	}

	delRowById(id) {
		this.rows.forEach((row, index) => {
			if(row instanceof Row) {
				if(row.id === id) {
					this.rows.splice(index, 1);
				}
			}
		});
	}

	getRowById(id) {
		let findRow = false;
		this.rows.forEach((row, index) => {
			if(row instanceof Row) {
				if(row.id === id) {
					findRow = this.rows[index];
				}
			}
		});

		return findRow;
	}

	get rows() {
		return this._rows;
	}

	set rows(value) {
		this._rows = value;
	}
}
