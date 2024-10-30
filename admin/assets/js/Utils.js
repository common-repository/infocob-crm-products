const { __, _x, _n, _nx } = wp.i18n;

var requests_getChampsInfocob = {};
var requests_getChampsInventairesInfocob = {};
var requests_getPostTypes = {};
var requests_getTaxonomiesFromPostType = {};
var requests_getCategoriesFromTaxonomy = {};
var requests_getPostMetaValues = {};
var requests_getAcfFieldsValues = {};
var requests_getLangs = {};
var requests_getAcfFieldGroupsFromPostType = {};
var requests_getAcfFieldsFromGroup = {};
var requests_getAcfRepeaterFieldsFromGroup = {};
var requests_getAcfSubFieldsFromField = {};
var requests_getTranslations = {};

export class Utils {

    static getChampsInfocob(module) {
        return new Promise((resolve, reject) => {
            if(module in requests_getChampsInfocob) {
                resolve(requests_getChampsInfocob[module]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_champs_infocob',
                        security: admin_ajax_utils.security ?? "",
                        module: module
                    }
                }).done((response) => {
                    if(response.success) {
                        requests_getChampsInfocob[module] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getChampsInventairesInfocob() {
        return new Promise((resolve, reject) => {
            if("inventaires" in requests_getChampsInventairesInfocob) {
                resolve(requests_getChampsInventairesInfocob["inventaires"]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_champs_inventaires_infocob',
                        security: admin_ajax_utils.security ?? ""
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getChampsInventairesInfocob["inventaires"] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getPostTypesFromTaxonomy(taxonomy) {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: admin_ajax_utils.url ?? "",
                method: 'POST',
                data: {
                    action: 'get_post_types_from_taxonomy',
                    security: admin_ajax_utils.security ?? "",
                    taxonomy: taxonomy
                }
            }).done((response) => {
                if(response.success) {
                    resolve(response.data);
                } else {
                    console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                    reject();
                }
            });
        });
    }

    static getPostTypes() {
        return new Promise((resolve, reject) => {
            if("post_types" in requests_getPostTypes) {
                resolve(requests_getPostTypes["post_types"]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'GET',
                    data: {
                        action: 'get_post_types',
                        security: admin_ajax_utils.security ?? ""
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getPostTypes["post_types"] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getTaxonomiesFromPostType(post_type) {
        return new Promise((resolve, reject) => {
            if(post_type in requests_getTaxonomiesFromPostType) {
                resolve(requests_getTaxonomiesFromPostType[post_type]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_taxonomies_from_post_type',
                        security: admin_ajax_utils.security ?? "",
                        post_type: post_type
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getTaxonomiesFromPostType[post_type] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getCategoriesFromTaxonomy(taxonomy) {
        return new Promise((resolve, reject) => {
            if(taxonomy in requests_getCategoriesFromTaxonomy) {
                resolve(requests_getCategoriesFromTaxonomy[taxonomy]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_categories_from_taxonomy',
                        security: admin_ajax_utils.security ?? "",
                        taxonomy: taxonomy
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getCategoriesFromTaxonomy[taxonomy] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getPostMetaValues(post_type, meta_keys) {
        return new Promise((resolve, reject) => {
            let meta_keys_string =  meta_keys.join("_");
            if(meta_keys_string === "") {
                meta_keys_string = "---";
            }

            if(post_type in requests_getPostMetaValues && meta_keys_string in requests_getPostMetaValues[post_type]) {
                resolve(requests_getPostMetaValues[post_type][meta_keys_string]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_post_meta_values',
                        security: admin_ajax_utils.security ?? "",
                        post_type: post_type,
                        meta_keys: meta_keys,
                    }
                }).done((response) => {
                    if (response.success) {
                        if(!(post_type in requests_getPostMetaValues)) {
                            requests_getPostMetaValues[post_type] = [];
                        }
                        requests_getPostMetaValues[post_type][meta_keys_string] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getAcfFieldsValues(post_type, acf_fields) {
        return new Promise((resolve, reject) => {
            let acf_fields_string =  acf_fields.join("_");
            if(acf_fields_string === "") {
                acf_fields_string = "---";
            }

            if(post_type in requests_getAcfFieldsValues && acf_fields_string in requests_getAcfFieldsValues[post_type]) {
                resolve(requests_getAcfFieldsValues[post_type][acf_fields_string]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_acf_fields_values',
                        security: admin_ajax_utils.security ?? "",
                        post_type: post_type,
                        acf_fields: acf_fields,
                    }
                }).done((response) => {
                    if (response.success) {
                        if(!(post_type in requests_getAcfFieldsValues)) {
                            requests_getAcfFieldsValues[post_type] = [];
                        }
                        requests_getAcfFieldsValues[post_type][acf_fields_string] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static generatedThemeFile(post_id, type, file) {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: admin_ajax_utils.url ?? "",
                method: 'POST',
                data: {
                    action: 'generated_theme_file',
                    security: admin_ajax_utils.security ?? "",
                    post_id: post_id,
                    type: type,
                    file: file,
                }
            }).done((response) => {
                if(response.success) {
                    resolve(response.data);
                } else {
                    console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                    reject();
                }
            });
        });
    }

    static getLangs(post_type = false) {
        return new Promise((resolve, reject) => {
            if(post_type in requests_getLangs) {
                resolve(requests_getLangs[post_type]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'GET',
                    data: {
                        action: 'get_langs',
                        security: admin_ajax_utils.security ?? "",
                        post_type: post_type
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getLangs[post_type] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getAcfFieldGroupsFromPostType(post_type) {
        return new Promise((resolve, reject) => {
            if(post_type in requests_getAcfFieldGroupsFromPostType) {
                resolve(requests_getAcfFieldGroupsFromPostType[post_type]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_acf_field_groups_from_post_type',
                        security: admin_ajax_utils.security ?? "",
                        post_type: post_type
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getAcfFieldGroupsFromPostType[post_type] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getAcfFieldsFromGroup(post_id) {
        return new Promise((resolve, reject) => {
            if(post_id in requests_getAcfFieldsFromGroup) {
                resolve(requests_getAcfFieldsFromGroup[post_id]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_acf_fields_from_group',
                        security: admin_ajax_utils.security ?? "",
                        post_id: post_id
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getAcfFieldsFromGroup[post_id] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getAcfRepeaterFieldsFromGroup(post_id) {
        return new Promise((resolve, reject) => {
            if(post_id in requests_getAcfRepeaterFieldsFromGroup) {
                resolve(requests_getAcfRepeaterFieldsFromGroup[post_id]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_acf_repeater_fields_from_group',
                        security: admin_ajax_utils.security ?? "",
                        post_id: post_id
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getAcfRepeaterFieldsFromGroup[post_id] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getAcfSubFieldsFromField(post_id, acf_field) {
        return new Promise((resolve, reject) => {
            if(post_id in requests_getAcfSubFieldsFromField && acf_field in requests_getAcfSubFieldsFromField[post_id]) {
                resolve(requests_getAcfSubFieldsFromField[post_id][acf_field]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_acf_sub_fields_from_field',
                        security: admin_ajax_utils.security ?? "",
                        post_id: post_id,
                        acf_field: acf_field
                    }
                }).done((response) => {
                    if (response.success) {
                        if(!(post_id in requests_getAcfSubFieldsFromField)) {
                            requests_getAcfSubFieldsFromField[post_id] = [];
                        }
                        requests_getAcfSubFieldsFromField[post_id][acf_field] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static getSelectOptionsHTML(options = [], selected_values = []) {
        let options_html = ``;
        options.forEach((value, index) => {
            options_html += `<option value="${value}" ${selected_values.includes(value) ? `selected` : ``}>${index}</option>`;
        });
        return options_html;
    }

    static getLogsFile($filename, level = "infos") {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: admin_ajax_utils.url ?? "",
                method: 'GET',
                data: {
                    action: 'get_logs_file',
                    security: admin_ajax_utils.security ?? "",
                    filename: $filename,
                    level: level,
                }
            }).done((response) => {
                if(response.success) {
                    resolve(response.data);
                } else {
                    console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                    reject();
                }
            });
        });
    }

    static getTranslations(translations = []) {
        return new Promise((resolve, reject) => {
            let translations_string =  translations.join("_");
            if(translations_string === "") {
                translations_string = "---";
            }

            if(translations_string in requests_getTranslations) {
                resolve(requests_getTranslations[translations_string]);
            } else {
                jQuery.ajax({
                    url: admin_ajax_utils.url ?? "",
                    method: 'POST',
                    data: {
                        action: 'get_translations',
                        security: admin_ajax_utils.security ?? "",
                        translations: translations,
                    }
                }).done((response) => {
                    if (response.success) {
                        requests_getTranslations[translations_string] = response.data;

                        resolve(response.data);
                    } else {
                        console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                        reject();
                    }
                });
            }
        });
    }

    static startImport(post_id) {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: admin_ajax_utils.url ?? "",
                method: 'POST',
                data: {
                    action: 'start_import',
                    security: admin_ajax_utils.security ?? "",
                    post_id: post_id,
                }
            }).done((response) => {
                if(response.success) {
                    resolve(response.data);
                } else {
                    console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-products'))
                    reject();
                }
            });
        });
    }

    static initTooltips() {
        tippy('[data-tippy-content]', {
            placement: 'top-start',
            arrow: true,
            allowHTML: true,
            interactive: true,
            delay: [250, 200],
        });
    }

    static encodeConfig($config) {
        let json = JSON.stringify($config);
        return window.btoa(encodeURIComponent(json));
    }

    static decodeConfig(configBase64) {
        let json = "{}";
        if(configBase64 !== "" && configBase64 !== undefined) {
            json = window.atob(configBase64);
        }
        try {
            return JSON.parse(decodeURIComponent(json));
        } catch (exception) {
            return JSON.parse(json);
        }
    }

    static dec2hex (dec) {
        return dec.toString(16).padStart(2, "0")
    }

    static generateRandomId(len = false) {
        let arr = new Uint8Array((len || 40) / 2)
        window.crypto.getRandomValues(arr)
        return Array.from(arr, Utils.dec2hex).join('')
    }

    static tributeFieldsInfocob(elements, values = [], modules = []) {
        let promises = [];
        if(modules.length > 0) {
            values = [];
            modules.forEach((module) => {
                promises.push(Utils.getChampsInfocob(module).then((responses) => {
                    for (const [key, value] of Object.entries(responses)) {
                        values.push({
                            key: key,
                            text: value
                        });
                    }
                }));
            });
        }

        Promise.allSettled(promises).then(() => {
            jQuery(elements).each((index, element) => {
                if (element instanceof HTMLElement) {
                    var tribute = new Tribute({
                        trigger: "{{",
                        values: values,
                        autocompleteMode: false,
                        allowSpaces: true,
                        selectTemplate: function (item) {
                            return "{{" + item.original.key + "}}";
                        },
                        menuItemTemplate: function (item) {
                            return item.original.key + " [" + item.original.text + "]";
                        },
                        lookup: function (value, typingText) {
                            return value.key + " " + value.text;
                        },
                        noMatchTemplate: function () {
                            return '<span style="visibility: hidden;"></span>';
                        }
                    });

                    tribute.detach(element);
                    tribute.attach(element);
                }
            });
        });
    }

}
