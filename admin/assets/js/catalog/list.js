const {__, _x, _n, _nx} = wp.i18n;

jQuery(document).ready(function($) {
    $(".icp_shortcode_left_filters, .icp_shortcode_right_filters, .icp_shortcode_top_filters").on("click", (event) => {
        let StringToCopy = jQuery(event.currentTarget).text();

        navigator.permissions.query({name: "clipboard-write"}).then(result => {
            if (result.state === "granted" || result.state === "prompt") {
                navigator.clipboard.writeText(StringToCopy);
            }
        });
    });
    tippy(".icp_shortcode_left_filters, .icp_shortcode_right_filters, .icp_shortcode_top_filters", {
        content: _x("Copied !", "Admin edit catalog post", 'infocob-crm-products'),
        trigger: 'click',
        placement: 'top-start',
        arrow: false,
        allowHTML: true,
    });
});
