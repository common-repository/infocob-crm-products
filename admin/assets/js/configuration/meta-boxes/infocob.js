import {FilterInfocob} from "./infocob/FilterInfocob.js";

jQuery(document).ready(function($) {
    jQuery("#meta-box-infocob").on("loaded", () => {
        let infocob_filters_element = $("#infocob-filters");
        if (infocob_filters_element.length) {
            let infocob_filters = new FilterInfocob($(infocob_filters_element).data("module"), "infocob-filters");
            infocob_filters.load($(infocob_filters_element).find("> input[name=infocob-filters]").val());

            infocob_filters.render();
        }
    });
});
