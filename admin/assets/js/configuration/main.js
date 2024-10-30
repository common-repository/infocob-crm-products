import {FilterInfocob} from "./meta-boxes/infocob/FilterInfocob.js";
import {MappingsManager} from "./meta-boxes/mappings/MappingsManager.js";
import {Utils} from "../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

var post_id_importing = [];
var previous_value = "";

jQuery(document).ready(function ($) {
    let infocob_type_produit = $("#infocob-type-produit");
    previous_value = $(infocob_type_produit).val();

    $(infocob_type_produit).on("change", onTypeProduitChange);

    $("#icp-start-import").on("click", onClickStartImport);

    firstRequests();
});

function firstRequests() {
    // Cache system
    let first_requests_promises = [];
    first_requests_promises.push(Utils.getPostTypes());
    first_requests_promises.push(Utils.getChampsInfocob(previous_value));

    Promise.allSettled(first_requests_promises).finally(() => {
        Utils.tributeFieldsInfocob(jQuery("#meta-box-infocob table.form-table input.autocomplete-fields-infocob"), [], (previous_value === "TYPEINVENTAIREPRODUIT") ? [previous_value, "FAMILLETYPEINVENTAIRE"] : [previous_value]);
        Utils.tributeFieldsInfocob(jQuery("#meta-box-post table.form-table input.autocomplete-fields-infocob"), [], (previous_value === "TYPEINVENTAIREPRODUIT") ? [previous_value, "FAMILLETYPEINVENTAIRE"] : [previous_value]);
        Utils.tributeFieldsInfocob(jQuery("#meta-box-files table.form-table.cloud-files input.autocomplete-fields-infocob"), [], (previous_value === "TYPEINVENTAIREPRODUIT") ? [previous_value, "FAMILLETYPEINVENTAIRE"] : [previous_value]);
        Utils.tributeFieldsInfocob(jQuery("#meta-box-files table.form-table.local-files input.autocomplete-fields-infocob"), [], (previous_value === "TYPEINVENTAIREPRODUIT") ? [previous_value, "FAMILLETYPEINVENTAIRE"] : [previous_value]);

        jQuery("#meta-box-infocob").trigger("loaded");
        jQuery("#meta-box-mappings").trigger("loaded");
    });
}

function onTypeProduitChange(event) {
    let new_value = event.currentTarget.value;

    // Metabox 'Mappings'
    if (previous_value !== new_value) {
        let mappings_element = jQuery("#mappings > input[type='hidden'][name='mappings']");
        if (mappings_element) {
            let mappingsManager = new MappingsManager();
            mappingsManager.load("");

            mappingsManager.render();
        }
    }

    // Metabox 'Infocob'
    if (previous_value !== new_value) {
        let infocob_filters_element = jQuery("#infocob-filters");
        jQuery(infocob_filters_element).data("module", new_value);
        if (infocob_filters_element.length) {
            let infocob_filters = new FilterInfocob(jQuery(infocob_filters_element).data("module"), "infocob-filters");
            infocob_filters.load("");

            infocob_filters.render();
        }
    }

    if(previous_value !== new_value) {
        firstRequests();
    }

    previous_value = new_value;
}

function onClickStartImport(event) {
    event.preventDefault();
    let buttonElement = jQuery(event.currentTarget);
    let post_id = jQuery(buttonElement).data("post_id");

    if(!post_id_importing.includes(post_id)) {
        Swal.fire({
            title: _x("Start import ?", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products'),
            html: `<strong>${ _x("Make sur to save your configuration before start !", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products') }</strong>`,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: _x("Cancel", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products'),
            confirmButtonText: _x("Start", "Admin view configuration post, meta-box 'post'", 'infocob-crm-products'),
        }).then((result) => {
            if (result.isConfirmed) {
                // let loader = jQuery(buttonElement).parents("tr").find(".icp_last_import.column-icp_last_import");

                post_id_importing.push(post_id);

                jQuery(buttonElement).addClass("active");
                Utils.startImport(post_id).finally(() => {
                    jQuery(buttonElement).removeClass("active");

                    post_id_importing = post_id_importing.filter(function(value, index, arr){
                        return value !== post_id;
                    });
                });
            }
        });
    }
}
