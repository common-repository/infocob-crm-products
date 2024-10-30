import {FormFilters} from "./classes/FormFilters.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

jQuery(document).ready(function ($) {
    let selectMultiples = jQuery('.infocob-crm-products.filters select[multiple=multiple]');
    if(selectMultiples.length) {
        jQuery(selectMultiples).multipleSelect("refresh");
        jQuery(selectMultiples).multipleSelect("refreshOptions", {
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
    }

    initFiltersTaxonomy();

    initSlidersRange();

    let filtersElements = jQuery('.infocob-crm-products.filters');
    if(filtersElements.length) {
        let post_ids = [];
        jQuery(filtersElements).each((index, filtersElement) => {
            let post_id = jQuery(filtersElement).data("post_id");
            if(post_id !== "") {
                if(!post_ids.includes(post_id)) {
                    post_ids.push(post_id);

                    let formFilters = new FormFilters(post_id);
                    jQuery(".infocob-crm-products.filters input.submit-filter").on("click", () => {
                        window.location.href = formFilters.get_parameters;
                    });

                    jQuery(".infocob-crm-products.filters input.reset").on("click", () => {
                        let url = new URL(window.location.href);
                        let path = url?.pathname ?? "";
                        let regex = new RegExp('(?<path>page\\/[0-9]+\\/?)$', 'mi');
                        url.pathname = path.replace(regex, '');
                        let searchParams = url.searchParams;
                        searchParams.delete("infocob-crm-products");

                        window.location.href = url.href;
                    });
                }
            }

        });
    }
});

function initFiltersTaxonomy() {
    let select = jQuery(".infocob-crm-products.filters div.filter.taxonomy select option");
    jQuery(select).each((index, element) => {
        let html_value = jQuery(element).html();
        let levels = parseInt(jQuery(element).data("level"));
        let nbsp = ``;
        for(let i = 0; i < levels; i++) {
            nbsp += `&nbsp;&nbsp;&nbsp;&nbsp;`;
        }

        jQuery(element).html(nbsp + html_value);
    });
}

function initSlidersRange() {
    let sliders_range = jQuery(".infocob-crm-products .slider-range");
    if (sliders_range.length) {
        sliders_range.each((index, slider_range) => {

            let min = Math.floor(parseFloat(jQuery(slider_range).data("min")));
            let max = Math.ceil(parseFloat(jQuery(slider_range).data("max")));
            let unit = String(jQuery(slider_range).data("unit"));
            let step = Number(jQuery(slider_range).data("step"));

            let value_min = Math.floor(parseFloat(jQuery(slider_range).data("min-value")));
            let value_max = Math.ceil(parseFloat(jQuery(slider_range).data("max-value")));

            jQuery(slider_range).slider({
                range: true,
                min: min,
                max: max,
                step: step,
                values: [value_min, value_max],
                slide: function (event, ui) {
                    let filter = jQuery(event.target).parents(".filter");
                    if (filter.length) {
                        let input = jQuery(filter).find("input.fake");
                        let input_min =  jQuery(filter).find("input.min");
                        let input_max =  jQuery(filter).find("input.max");

                        let current_value_min = ui.values[0];
                        let current_value_max = ui.values[1];

                        if(current_value_max + step > value_max) {
                            current_value_max = value_max;
                        }

                        let value = `${current_value_min} - ${current_value_max}`;
                        if(unit !== "") {
                            value = `${current_value_min} ${unit} - ${current_value_max} ${unit}`;
                        }

                        jQuery(input).val(value);
                        jQuery(input_min).val(current_value_min);
                        jQuery(input_max).val(current_value_max);
                    }
                }
            });

            let filter = jQuery(slider_range).parents(".filter");
            if (filter.length) {
                let input = jQuery(filter).find("input.fake");
                let input_min =  jQuery(filter).find("input.min");
                let input_max =  jQuery(filter).find("input.max");

                let current_value_min = jQuery(slider_range).slider("values", 0);
                let current_value_max = jQuery(slider_range).slider("values", 1);

                if(current_value_max + step > value_max) {
                    current_value_max = value_max;
                }

                let value = `${current_value_min} - ${current_value_max}`;
                if(unit !== "") {
                    value = `${current_value_min} ${unit} - ${current_value_max} ${unit}`;
                }

                jQuery(input).val(value);
                jQuery(input_min).val(current_value_min);
                jQuery(input_max).val(current_value_max);
            }
        });
    }
}
