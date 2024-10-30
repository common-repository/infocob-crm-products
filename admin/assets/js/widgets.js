import {Utils} from "./Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

var post_id_importing = [];

jQuery(document).ready(function($) {
    $("#icp_configuration_dashboard_widget .start-import").on("click", onClickStartImport);
});

function onClickStartImport(event) {
    event.preventDefault();
    let linkElement = jQuery(event.currentTarget);
    let post_id = jQuery(linkElement).data("post_id");

    if(!post_id_importing.includes(post_id)) {
        Swal.fire({
            title: _x("Start import ?", "Admin dashboard", 'infocob-crm-products'),
            text: _x("Do you really want to start the import ?", "Admin dashboard", 'infocob-crm-products'),
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: _x("Cancel", "Admin dashboard", 'infocob-crm-products'),
            confirmButtonText: _x("Start", "Admin dashboard", 'infocob-crm-products'),
        }).then((result) => {
            if (result.isConfirmed) {
                let loader = jQuery(linkElement).parents(".cron").find(".date");

                post_id_importing.push(post_id);

                jQuery(loader).addClass("active");
                Utils.startImport(post_id).finally(() => {
                    jQuery(loader).removeClass("active");

                    post_id_importing = post_id_importing.filter(function(value, index, arr){
                        return value !== post_id;
                    });
                });
            }
        });
    }
}
