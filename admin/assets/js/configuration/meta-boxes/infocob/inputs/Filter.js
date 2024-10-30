export class Filter {
    
    constructor(index, module) {
        this._id = index;
        this._module = module;
        this._type = "";
        this._value = "";
        this._next_condition = "and";
    }
    
    get id() {
        return Number(this._id);
    }
    
    set id(value) {
        this._id = Number(value);
    }
    
    get module() {
        return String(this._module);
    }
    
    set module(value) {
        this._module = String(value);
    }
    
    get type() {
        return String(this._type);
    }
    
    set type(value) {
        this._type = String(value);
    }
    
    get value() {
        return this._value;
    }
    
    set value(value) {
        this._value = value;
    }
    
    get next_condition() {
        return String(this._next_condition);
    }
    
    set next_condition(value) {
        this._next_condition = String(value);
    }
    
    set(values) {
        for(const [key, value] of Object.entries(values)) {
            if(this.hasOwnProperty(key)) {
                this[key] = value;
            }
        }
    }
    
    toJSON() {
        return {
            type: this.type,
            value: this.value,
            next_condition: this.next_condition,
        }
    }
    
    updateFromElement(field_element) {
        this.value = jQuery(field_element).find("input[data-name=value]").val() ?? "";
        this.next_condition = jQuery(field_element).find("select[data-name=next_condition]").val() ?? "";
    }
}


