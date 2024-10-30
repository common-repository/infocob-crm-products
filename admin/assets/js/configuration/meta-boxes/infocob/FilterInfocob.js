import {Filter} from "./inputs/Filter.js";
import {FilterRow} from "./inputs/FilterRow.js";
import {FilterGroup} from "./inputs/FilterGroup.js";
import Sortable from "../../../../../../node_modules/sortablejs/modular/sortable.complete.esm.js";
import {Utils} from "../../../Utils.js";

const {__, _x, _n, _nx, sprintf} = wp.i18n;

export class FilterInfocob {

    constructor(module, id) {
        this._module = module;
        this._id = id;
        this._filters = [];
    }

    load(configBase64) {
        jQuery("#infocob-filters > .content-filter").html("");
        jQuery("#infocob-filters .icp-loader").addClass("active");
        let config = Utils.decodeConfig(configBase64);
        this.loadFilters(config ?? []);
    }

    loadFilters(config, id_group = false) {
        for(const [key, value] of Object.entries(config)) {
            if(value instanceof Object) {
                this.loadFilter(value, key, id_group);
            }
        }
    }

    loadFilter(field_config, index, id_group = false) {
        let field = this.getFieldByType(field_config.type, index);

        if(field instanceof Filter) {
            for(const [key, value] of Object.entries(field_config)) {
                if(value instanceof Object) {
                    if(field instanceof FilterGroup && key === "value") {
                        this.filters.push(field);
                        this.loadFilters(value, index);
                    }
                } else {
                    field[key] = value;
                }
            }
        }

        if(id_group !== false) {
            this.filters[id_group].value.push(field);
        } else if(!(field instanceof FilterGroup)) {
            this.filters.push(field);
        }
    }

    render() {
        this.toHTML().then((html) => {
            jQuery("#" + this.id).find("> .content-filter").html(html);

            this.addFieldsEvents();
            this.recalculateIndexes();

            jQuery("#infocob-filters .icp-loader").removeClass("active");
        });
    }

    save() {
        let base64Json = Utils.encodeConfig(this.filters);
        jQuery(`#${this.id} > input[name=infocob-filters]`).val(base64Json);
    }

    addFieldsEvents() {
        jQuery(`#${ this.id } .add-row`).off("click").on("click", {this: this}, this.onAddRowEvent);
        jQuery(`#${ this.id } .del-row`).off("click").on("click", {this: this}, this.onDelRowEvent);

        jQuery(`#${ this.id } .add-group`).off("click").on("click", {this: this}, this.onAddGroupEvent);
        jQuery(`#${ this.id } .del-group`).off("click").on("click", {this: this}, this.onDelGroupEvent);

        jQuery(`#${ this.id } .content-filter input, #${ this.id } .content-filter select`).off("change").on("change", {this: this}, this.onChangeInputEvent);

        // @TODO drag and drops
        let $this = this;
        let inventaire = jQuery(`#${ this.id } > .content-filter`)[0];
        Sortable.create(inventaire, {
            handle: ".handle",
            group: {
                name: 'row',
                put: ['group'],
            },
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            sort: true,
            onMove: function(event) {
                if(jQuery(event.dragged).hasClass("group-filter") && jQuery(event.to).parents(".group-filter").first().hasClass("group-filter")) {
                    return false;
                }
            },
            onUpdate: function(event) {
                $this.recalculateIndexes();
                $this.onSortFieldsUpdate(event);
            },
        });

        let groups = jQuery(`#${ this.id } > .content-filter .group-filter > .content-filter`);
        for(let i = 0; i < groups.length; i++) {
            Sortable.create(groups[i], {
                handle: ".handle",
                group: {
                    name: 'group',
                    pull: ['row', 'group'],
                    put: ['row', 'group'],
                },
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onAdd: function(event) {
                    $this.onSortGroupsAdd(event);
                    $this.recalculateIndexes();
                },
                onUpdate: function(event) {
                    $this.onSortGroupsUpdate(event);
                    $this.recalculateIndexes();
                },
                onRemove: function(event) {
                    $this.onSortGroupsRemove(event);
                    $this.recalculateIndexes();
                }
            });
        }

        let selects_search = jQuery('#infocob-filters select.search');
        jQuery(selects_search).multipleSelect("destroy");
        jQuery(selects_search).multipleSelect({
            filter: true,
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

    onChangeInputEvent(event) {
        let $this = event.data.this;
        let group = jQuery(event.currentTarget).parents(".group-filter");

        if(group.length > 0) {
            let group_index = jQuery(group).data("id");
            updateInputField(event, $this.filters[group_index]);

            let field = $this.filters[group_index];
            if(field instanceof FilterGroup) {
                let field_element = jQuery(event.currentTarget).parents(".group-filter").first();
                field.updateFromElement(field_element);
            }
        } else {
            updateInputField(event, $this);
        }

        $this.save();

        function updateInputField(event, $this) {
            let field_element = jQuery(event.currentTarget).parents(".row-filter").first();
            let field_index = jQuery(event.currentTarget).parents(".row-filter").first().data("id");

            let field;
            if($this.value) {
                field = $this.value[field_index];
            } else {
                field = $this.filters[field_index];
            }

            if(field instanceof Filter) {
                field.updateFromElement(field_element);
            }
        }
    }

    onSortGroupBy(event) {
        let old_index = event.oldIndex;
        let new_index = event.newIndex;

        let group = jQuery(event.item).parents(".group-container");

        if(group.length > 0) {
            let group_index = jQuery(group).first().parent(".row-filter").data("id");
            indexGroupBy(event, this.filters[group_index]);
        } else {
            indexGroupBy(event, this);
        }

        function indexGroupBy(event, $this) {
            let field_index = jQuery(event.from).parents(".row-filter").first().data("id");

            let old_field = $this.filters[field_index].group_by.group_by[old_index];
            $this.filters[field_index].group_by.group_by[old_index] = $this.filters[field_index].group_by.group_by[new_index];
            $this.filters[field_index].group_by.group_by[new_index] = old_field;
        }
    }

    onSortGroupsAdd(event) {
        let old_index = event.oldIndex;
        let new_index = event.newIndex;

        let parent_index = jQuery(event.to).parents(".group-filter").first().data("id");

        this.filters[parent_index].value.splice(new_index, 0, this.filters[old_index]);
        this.filters.splice(old_index, 1);
    }

    onSortGroupsRemove(event) {
        let old_index = event.oldIndex;
        let new_index = event.newIndex;

        let parent_index = jQuery(event.from).parents(".group-filter").first().data("id");

        let old_field = this.filters[parent_index].value[old_index];
        this.filters[parent_index].value.splice(old_index, 1);
        this.filters.splice(new_index, 0, old_field);
    }

    onSortGroupsUpdate(event) {
        let old_index = event.oldIndex;
        let new_index = event.newIndex;

        let parent_index = jQuery(event.item).parents(".group-filter").first().data("id");

        let old_field = this.filters[parent_index].value[old_index];
        this.filters[parent_index].value.splice(old_index, 1);
        this.filters[parent_index].value.splice(new_index, 0, old_field);
    }

    onSortFieldsUpdate(event) {
        let old_index = event.oldIndex;
        let new_index = event.newIndex;

        let old_field = this.filters[old_index];
        this.filters[old_index] = this.filters[new_index];
        this.filters[new_index] = old_field;

        this.recalculateIndexes();
    }

    onAddGroupEvent(event) {
        let $this = event.data.this;

        let index = jQuery(`#${ $this.id } > .content-filter > .filter`).length;
        let field = new FilterGroup(index, $this.module);
        $this.filters.push(field);

        field.toHTML().then((html) => {
            jQuery(`#${ $this.id } > .content-filter`).append(html);

            $this.addFieldsEvents();

            $this.recalculateIndexes();
        });
    }

    onDelGroupEvent(event) {
        let $this = event.data.this;

        $this.filters.forEach((field, index) => {
            if(field instanceof Filter) {
                let field_element = jQuery(event.currentTarget).parents(".group-filter").first();

                if(index === jQuery(field_element).data("id")) {
                    $this.filters.splice(index, 1);
                    jQuery(field_element).remove();
                }
            }
        });

        $this.recalculateIndexes();
    }

    onAddRowEvent(event) {
        let $this = event.data.this;
        let index = jQuery(`#${ $this.id } > .content-filter > .filter`).length;
        let field = new FilterRow(index, $this.module);
        $this.filters.push(field);

        field.toHTML().then((html) => {
            jQuery(`#${ $this.id } > .content-filter`).append(html);

            $this.addFieldsEvents();

            $this.recalculateIndexes();
        });
    }

    onDelRowEvent(event) {
        let $this = event.data.this;

        $this.filters.forEach((field, index) => {
            if(field instanceof Filter) {
                let field_element = jQuery(event.currentTarget).parents(".row-filter").first();

                if(jQuery(field_element).parents(".group-filter").first().length > 0) {
                    let index_parent = jQuery(field_element).parents(".group-filter").first().data("id");
                    let field_group = $this.filters[index_parent].value[index];
                    if(field_group instanceof Filter) {
                        if(index === jQuery(field_element).data("id")) {
                            $this.filters[index_parent].value.splice(index, 1);
                            jQuery(field_element).remove();
                        }
                    }
                } else {
                    if(index === jQuery(field_element).data("id")) {
                        $this.filters.splice(index, 1);
                        jQuery(field_element).remove();
                    }
                }
            }
        });

        $this.recalculateIndexes();
    }

    onChangeTypeFieldEvent(event) {
        let $this = event.data.this;
        let group = jQuery(event.currentTarget).parents(".group-container");

        if(group.length > 0) {
            let group_index = jQuery(group).first().parent(".row-filter").data("id");
            changeTypeField(event, $this.filters[group_index], $this.getFieldByType);
        } else {
            changeTypeField(event, $this, $this.getFieldByType);
        }

        function changeTypeField(event, $this, getFieldByType) {
            let field_element = jQuery(event.currentTarget).parents(".row-filter").first();
            let field_index = jQuery(event.currentTarget).parents(".row-filter").first().data("id");

            let type_selected = jQuery(field_element).find(".field-type").val();
            let new_field = getFieldByType(type_selected, field_index);
            $this.filters.splice(field_index, 1, new_field);
            jQuery(field_element).replaceWith(new_field.toHTML());
        }

        $this.addFieldsEvents();

        $this.recalculateIndexes();
    }

    recaculateIndexesHeaderGroup() {
        let filters = jQuery(`#${ this.id } > .header-group-container .header-group`);

        indexesFieldsElements(filters);
        indexesFieldsData(this.header_group.header_group);

        function indexesFieldsElements(filters) {
            jQuery(filters).each((i, field_element) => {
                jQuery(field_element).data("id", i);
                jQuery(field_element).attr("data-id", i);
            });
        }

        function indexesFieldsData(filters) {
            filters.forEach((field, index) => {
                if(field instanceof HeaderGroup) {
                    field.id = index;
                }
            });
        }
    }

    onAddGroupByEvent(event) {
        let $this = event.data.this;

        $this.filters.forEach((field, index) => {
            if(field instanceof FilterGroup) {
                let field_element = jQuery(event.currentTarget).parents(".row-filter").first();

                if(index === jQuery(field_element).data("id")) {
                    let index_group_by = jQuery(field_element).find(".group-by").length;

                    let group_by_list = new GroupByList();
                    if(group_by_list instanceof GroupByList) {
                        let group_by = new GroupBy(index_group_by);
                        group_by_list.addGroupBy(group_by);
                        field.group_by.addGroupBy(group_by);
                    }

                    $this.filters.splice(index, 1, field);
                    jQuery(group_by_list.toHTML()).insertBefore(jQuery(field_element).find(".add-group-by"));
                }
            }
        });

        $this.addGroupByEvents();

        $this.recalculateIndexes();
    }

    onDelGroupByEvent(event) {
        let $this = event.data.this;
        let field_element = jQuery(event.currentTarget).parents(".row-filter").first();

        $this.filters.forEach((field, index_1) => {
            if(field instanceof FilterGroup) {
                if(index_1 === jQuery(field_element).data("id")) {
                    let group_by_list = field.group_by;
                    if(group_by_list instanceof GroupByList) {
                        let group_by_element = jQuery(event.currentTarget).parents(".group-by").first();
                        group_by_list.group_by.forEach((group_by, index_2) => {
                            if(group_by instanceof GroupBy) {
                                if(index_2 === jQuery(group_by_element).data("id")) {
                                    group_by_list.group_by.splice(index_2, 1);
                                    $this.filters[index_1].group_by = group_by_list;
                                    jQuery(group_by_element).remove();
                                }
                            }
                        });
                    }
                }
            }
        });

        $this.recalculateIndexes();
    }

    recalculateIndexes() {
        let filters = jQuery(`#${ this.id } > .content-filter > .filter`);
        let $this = this;

        indexesFieldsElements(filters);
        indexesFieldsData(this.filters);
        $this.save();

        function indexesFieldsElements(filters) {
            jQuery(filters).each((i, field_element) => {
                if(jQuery(field_element).hasClass("row-filter")) {
                    jQuery(field_element).data("id", i);
                    jQuery(field_element).attr("data-id", i);
                } else if(jQuery(field_element).hasClass("group-filter")) {
                    jQuery(field_element).data("id", i);
                    jQuery(field_element).attr("data-id", i);

                    indexesFieldsElements(jQuery(field_element).find(".row-filter"));
                }
            });
        }

        function indexesFieldsData(filters) {
            filters.forEach((field, index) => {
                if(field instanceof Filter) {
                    field.id = index;

                    if(field instanceof FilterGroup) {
                        indexesFieldsData(field.value);
                    }
                }
            });
        }
    }

    getFieldByType(type, index) {
        let field;
        switch(type) {
            case 'row':
                field = new FilterRow(index, this.module);
                break;

            case 'group':
                field = new FilterGroup(index, this.module);
                break;

            default:
                field = new FilterRow(index, this.module);
                break;
        }

        return field;
    }

    toHTML() {
        return new Promise((resolve, reject) => {
            let filtersPromises = [];
            this.filters.forEach((field) => {
                filtersPromises.push(field.toHTML());
            });

            Promise.all(filtersPromises).then((responses) => {
                let html = "";
                responses.forEach((filters) => {
                    html += filters;
                });
                resolve(html);
            });
        });
    }

    toJSON() {
        return {
            filters: this.filters,
        };
    }

    updateFromElement(field_element) {

    }

    get id() {
        return String(this._id);
    }

    set id(value) {
        this._id = String(value);
    }

    get filters() {
        return this._filters;
    }

    set filters(value) {
        this._filters = value;
    }

    get module() {
        return this._module;
    }

    set module(value) {
        this._module = value;
    }
}
