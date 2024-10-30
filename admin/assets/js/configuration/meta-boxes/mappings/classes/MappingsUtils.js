export class MappingsUtils {

	update() {
		let base64Json = Utils.encodeConfig(this.rows);
		jQuery("#mappings > input[name='mappings']").val(base64Json);
	}

	static triggerUpdate() {
		jQuery("#mappings").trigger("update");
	}

}
