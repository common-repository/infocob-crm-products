import {Utils} from "../../../Utils.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

export class ThemeFiles {

    constructor(post_type) {
        this._post_type = post_type;
    }

    init() {
        this.applyEvents();
    }

    applyEvents() {
        let file_elements = jQuery("#general-theme-files div.file");

        jQuery(file_elements).each((index, element) => {
            let btn_element = jQuery(element).find("span.btn button");
            if (btn_element.length) {
                jQuery(btn_element).off("click").on("click", {$this: this}, this.onClickGenerate);
            }
        });

        jQuery("#general-theme-files .generate-all").on("click", {$this: this}, this.onClickGenerateAll);
    }

    onClickGenerateAll(event) {
        let btn_element = jQuery(event.currentTarget);
        let $this = event.data.$this;
        if (btn_element.length) {
            let post_id = jQuery(btn_element).data("post_id");

            if (post_id) {
                Swal.fire({
                    title: _x("Replace all files ?", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                    text: _x("The files will be replaced in the active theme", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: _x("Cancel", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                    confirmButtonText: _x("Replaced", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                }).then((result) => {
                    if (result.isConfirmed) {
                        let promises = [];

                        let file_elements = jQuery("#general-theme-files div.file");
                        jQuery(file_elements).each((index, element) => {
                            let btn_element = jQuery(element).find("span.btn button");
                            if (btn_element.length) {
                                let type = jQuery(btn_element).data("type");
                                let file = jQuery(btn_element).data("file");

                                promises.push(Utils.generatedThemeFile(post_id, type, file).then((response) => {
                                    let success = response.success ?? false;
                                    let date = response.date ?? "";

                                    if (success) {
                                        $this.setIcon(btn_element, success);
                                        $this.setDate(btn_element, date.format);
                                    }
                                }));
                            }
                        });

                        Promise.allSettled(promises).then((responses) => {
                            Swal.fire({
                                icon: 'success',
                                title: _x("Files replaced !", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                text: _x("Files have been replaced", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                            });
                        });
                    }
                });

            }
        }
    }

    onClickGenerate(event) {
        let btn_element = jQuery(event.currentTarget);
        let $this = event.data.$this;
        if (btn_element.length) {
            let post_id = jQuery(btn_element).data("post_id");
            let type = jQuery(btn_element).data("type");
            let file = jQuery(btn_element).data("file");
            let generated = (jQuery(btn_element).data("generated") === true);

            if (post_id) {
                if (generated) {
                    Swal.fire({
                        title: _x("Replace file ?", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                        text: _x("The file will be replaced in the active theme", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                        icon: 'warning',
                        showCancelButton: true,
                        cancelButtonText: _x("Cancel", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                        confirmButtonText: _x("Replaced", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Utils.generatedThemeFile(post_id, type, file).then((response) => {
                                let success = response.success ?? false;
                                let date = response.date ?? "";

                                if (success) {
                                    $this.setIcon(btn_element, success);
                                    $this.setDate(btn_element, date.format);

                                    Swal.fire({
                                        icon: 'success',
                                        title: _x("File replaced !", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                        text: _x("The file has been replaced", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: _x("Unable to replace file !", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                    });
                                }
                            }).catch((error) => {
                                console.error(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: _x("Unable to replace file !", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                });
                            })
                        }
                    });

                } else {
                    Swal.fire({
                        title: _x("Generate file ?", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                        text: _x("The file will be created in the active theme", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                        icon: 'warning',
                        showCancelButton: true,
                        cancelButtonText: _x("Cancel", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                        confirmButtonText: _x("Generate", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Utils.generatedThemeFile(post_id, type, file).then((response) => {
                                let success = response.success ?? false;
                                let date = response.date ?? "";

                                if (success) {
                                    $this.setIcon(btn_element, success);
                                    $this.setDate(btn_element, date.format);

                                    Swal.fire({
                                        icon: 'success',
                                        title: _x("File generated !", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                        text: _x("The file has been created", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: _x("Unable to generate file !", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                    });
                                }
                            }).catch((error) => {
                                console.error(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: _x("Unable to generate file !", "Admin view catalog post, meta-box 'general'", 'infocob-crm-products'),
                                });
                            })
                        }
                    });
                }
            }
        }
    }

    setIcon(btn_element, success) {
        let file_element = jQuery(btn_element).parents("div.file").first();
        if (file_element.length) {
            let icon_element = jQuery(file_element).find("div.content span.dashicons");

            if (success) {
                jQuery(btn_element).data("generated", true);
                jQuery(icon_element).removeClass("dashicons-no");
                jQuery(icon_element).addClass("dashicons-yes");
            } else {
                jQuery(icon_element).addClass("dashicons-no");
                jQuery(icon_element).removeClass("dashicons-yes");
            }
        }
    }

    setDate(btn_element, date) {
        let file_element = jQuery(btn_element).parents("div.file").first();
        if (file_element.length) {
            let date_element = jQuery(file_element).find("div.content span.date");

            let content = `(${ sprintf(_x("Last update : %s", "Admin view catalog post, meta-box 'general'", "infocob-crm-products"), date.toString()) })`;
            jQuery(date_element).html(content);
        }
    }

    get post_type() {
        return String(this._post_type);
    }
}
