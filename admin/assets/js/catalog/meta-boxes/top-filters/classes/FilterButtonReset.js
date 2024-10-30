import {FilterUtils} from "./FilterUtils.js";
import {Utils} from "../../../../Utils.js";

const {__, _x, _n, _nx} = wp.i18n;

export class FilterButtonReset {
    static id = 0;

    constructor() {
        this._id = FilterButtonReset.id;
        this._title = {};
        this._langs = [];

        let langsBase64 = jQuery("#top-filters").data("langs");
        this._langs = Utils.decodeConfig(langsBase64);

        FilterButtonReset.id++;
    }

    load(filterPostMeta) {
        this.title = filterPostMeta.title ?? {};
    }

    toJSON() {
        return Object.assign({}, {
            title: this.title,
        });
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let promises = [];

            let titles = ``;
            this.langs.forEach((lang, index) => {
                titles += `<input class="all-witdh multilingual" data-name="title" data-lang="${ lang }" type="text" value="${ this.title[lang] ?? "" }" data-tippy-content="${ sprintf(_x("Title %s", "Admin view catalog post, meta-box 'top-filters'", 'infocob-crm-products'), lang) }" placeholder="${ sprintf(_x("Title %s", "Admin view catalog post, meta-box 'top-filters'", 'infocob-crm-products'), lang) }">`;
            });

            Promise.allSettled(promises).then(() => {
                // language=html
                resolve(`
                    <div class="filter button-reset" data-id="${ this.id }">
						<div class="title margin">
							${ titles }
						</div>
					</div>
                `);
            });
        });
    }

    applyEvents() {
        let top_filter_element = this.getFilterElement();

        let title = jQuery(top_filter_element).find("input[data-name='title']");
        jQuery(title).off("keyup keypress blur change cut paste").on("keyup keypress blur change cut paste", (event) => {
            let title_element = jQuery(event.currentTarget);
            let current_value = jQuery(title_element).val();
            let title_lang = jQuery(title_element).data("lang");

            this.addTitle(current_value, title_lang);
        });
    }

    getFilterElement() {
        return jQuery(`#top-filters .top-filters-container .filter.button-reset[data-id='${ this.id }']`).first();
    }

    get id() {
        return this._id;
    }

    get title() {
        return this._title;
    }

    set title(value) {
        if(!(value instanceof Object)) {
            value = {};
        }

        this._title = value;
        FilterUtils.triggerUpdate();
    }

    addTitle(value, lang) {
        this.title[lang] = String(value);
        FilterUtils.triggerUpdate();
    }

    get langs() {
        return this._langs;
    }
}
