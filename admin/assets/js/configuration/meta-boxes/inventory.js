import {FilterInventory} from "./inventory/FilterInventory.js";
import {PostInventoryManager} from "./inventory/PostInventoryManager.js";

jQuery(document).ready(function($) {
    let inventory_filters_element = $("#inventory-filters");
    if(inventory_filters_element.length) {
        let inventory_filters = new FilterInventory($(inventory_filters_element).data("module"), "inventory-filters");
        inventory_filters.load($(inventory_filters_element).find("> input[name=inventory-filters]").val());

        inventory_filters.render();
    }

    if(jQuery("#post-inventory").length) {
        let postInventoryManager = new PostInventoryManager();
        postInventoryManager.load(jQuery("#post-inventory > input[type='hidden'][name='post-inventory']").val());

        postInventoryManager.render();
    }
});
