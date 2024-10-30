import {Utils} from "../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

var post_id_importing = [];

jQuery(document).ready(function($) {
   $(".icp-post-link-start-import").on("click", onClickStartImport);

   $(".icp_post_id.column-icp_post_id").on("click", (event) => {
       let StringToCopy = jQuery(event.currentTarget).text();

       navigator.permissions.query({name: "clipboard-write"}).then(result => {
           if (result.state === "granted" || result.state === "prompt") {
               navigator.clipboard.writeText(StringToCopy);
           }
       });
   });
    tippy(".icp_post_id.column-icp_post_id", {
        content: _x("Copied !", "Admin edit configuration post", 'infocob-crm-products'),
        trigger: 'click',
        placement: 'top-start',
        arrow: false,
        allowHTML: true,
    });
});

function onClickStartImport(event) {
    event.preventDefault();
    let linkElement = jQuery(event.currentTarget);
    let post_id = jQuery(linkElement).data("post_id");

    if(!post_id_importing.includes(post_id)) {
        Swal.fire({
            title: _x("Start import ?", "Admin edit configuration post", 'infocob-crm-products'),
            text: _x("Do you really want to start the import ?", "Admin edit configuration post", 'infocob-crm-products'),
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: _x("Cancel", "Admin edit configuration post", 'infocob-crm-products'),
            confirmButtonText: _x("Start", "Admin edit configuration post", 'infocob-crm-products'),
        }).then((result) => {
            if (result.isConfirmed) {
                let loader = jQuery(linkElement).parents("tr").find(".icp_last_import.column-icp_last_import");

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
