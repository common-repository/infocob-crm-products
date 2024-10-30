import {PostInventory}      from "./classes/PostInventory.js";
import {PostInventoryUtils} from "./classes/PostInventoryUtils.js";
import {Utils}         from "../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class PostInventoryManager {

	constructor() {
		this._post_inventory = [];
	}

	saveEvent() {
		jQuery("#post-inventory").off("update").on("update", () => {
			let base64Json = Utils.encodeConfig(this);
			jQuery("#post-inventory > input[name='post-inventory']").val(base64Json);
		});
	}

	load(configBase64) {
		jQuery("#post-inventory .icp-loader").addClass("active");

		let config = Utils.decodeConfig(configBase64);

		if(config.post_inventory !== undefined && config.post_inventory.length > 0) {
			config.post_inventory.forEach((config_post_inventory) => {
				let post_inventory = new PostInventory();
				post_inventory.load(config_post_inventory);
				this.addPostInventory(post_inventory);
			});
		}
	}

	render() {
		this.toHTML().then((html) => {
			jQuery("#post-inventory > .content-post-inventory").html(html);

			this.applyEvents();

			let module_infocob = jQuery("#infocob-type-produit").val();
			Utils.tributeFieldsInfocob(jQuery("#post-inventory > .content-post-inventory input.autocomplete-fields-infocob"), [], [module_infocob, "INVENTAIREPRODUIT", "TYPEINVENTAIREPRODUIT", "FAMILLETYPEINVENTAIRE"]);

			jQuery("#post-inventory .icp-loader").removeClass("active");

			Utils.initTooltips();
		});
	}

	applyEvents() {
		this.post_inventory.forEach((post_inventory, index) => {
			if(post_inventory instanceof PostInventory) {
				post_inventory.applyEvents();

				jQuery(post_inventory).off("del-post_inventory").on("del-post_inventory", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostInventoryById(id);
					}
				});
			}
		});

		let addPostInventory = jQuery("#post-inventory > div.actions-container > button.add-post_inventory");
		jQuery(addPostInventory).off("click").on("click", (event) => {
			event.preventDefault();

			let post_inventory = new PostInventory();
			post_inventory.toHTML().then((post_inventory_html) => {
				jQuery("#post-inventory > .content-post-inventory").append(post_inventory_html);
				post_inventory.applyEvents();
				this.addPostInventory(post_inventory);

				jQuery(post_inventory).off("del-post_inventory").on("del-post_inventory", (event, data) => {
					let id = data.id ?? false;
					if(id !== false) {
						this.delPostInventoryById(id);
						PostInventoryUtils.triggerUpdate();
					}
				});
			});
		});

		this.saveEvent();

		Utils.initTooltips();
	}

	toJSON() {
		return Object.assign({}, {
			post_inventory: this.post_inventory,
		});
	}

	toHTML() {
		return new Promise((resolve, reject) => {
			let promises = [];

			this.post_inventory.forEach((post_inventory) => {
				if(post_inventory instanceof PostInventory) {
					promises.push(post_inventory.toHTML());
				}
			});

			if(promises.length > 0) {
				Promise.all(promises).then((responses) => {
					let post_inventory_html = "";
					responses.forEach((html) => {
						post_inventory_html += html;
					});

					resolve(post_inventory_html);
				});
			} else {
				resolve();
			}
		});
	}

	addPostInventory(value) {
		if(value instanceof PostInventory) {
			this.post_inventory.push(value);

			PostInventoryUtils.triggerUpdate();
		} else {
			console.error(_x("Unable to define value", "Admin view configuration post, meta-box 'inventory'", 'infocob-crm-products'));
		}
	}

	delPostInventory(value) {
		if(value instanceof PostInventory) {
			for(var i = 0; i < this.post_inventory.length; i++) {
				if(this.post_inventory[i] === value) {
					this.post_inventory.splice(i, 1);
				}
			}

			PostInventoryUtils.triggerUpdate();
		}
	}

	delPostInventoryById(id) {
		this.post_inventory.forEach((post_inventory, index) => {
			if(post_inventory instanceof PostInventory) {
				if(post_inventory.id === id) {
					this.post_inventory.splice(index, 1);
				}
			}
		});

		PostInventoryUtils.triggerUpdate();
	}

	get post_inventory() {
		return this._post_inventory;
	}

	set post_inventory(value) {
		this._post_inventory = value;

		PostInventoryUtils.triggerUpdate();
	}

}
