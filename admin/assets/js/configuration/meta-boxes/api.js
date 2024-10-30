import {Utils} from "../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

jQuery(document).ready(function ($) {
    jQuery("#generate-rest-api-key").on("click", onClickGenerateRestApiKey);
});

function onClickGenerateRestApiKey(event) {
    Swal.fire({
        title: _x("Are you sure ?", "add_settings_field", 'infocob-crm-products'),
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: _x("Cancel", "add_settings_field", 'infocob-crm-products'),
        confirmButtonText: _x("Continue", "add_settings_field", 'infocob-crm-products'),
    }).then((result) => {
        if (result.isConfirmed) {
            jQuery("#rest-api-key").val(Utils.generateRandomId());
        }
    });
}
