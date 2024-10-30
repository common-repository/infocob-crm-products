import {TopFiltersManager} from "./top-filters/TopFiltersManager.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

var previous_post_type = "";

jQuery(document).ready(function($) {
    let post_type = jQuery("#general-post-type");
    let post_type_value = jQuery(post_type).val();

    if(post_type_value !== "") {
        let topFiltersManager = new TopFiltersManager(post_type_value);
        topFiltersManager.load(jQuery("#top-filters > input[type='hidden'][name='top-filters']").val());

        topFiltersManager.render();
    }

    jQuery(post_type).on("change", onChangePostType);

    previous_post_type = post_type_value;
});

function onChangePostType(event) {
    let post_type_value = jQuery(event.currentTarget).val()

    if(post_type_value !== previous_post_type) {
        let topFiltersManager = new TopFiltersManager(post_type_value);
        topFiltersManager.load(jQuery("#top-filters > input[type='hidden'][name='top-filters']").val());

        topFiltersManager.render();
    }

    previous_post_type = post_type_value;
}
