<table class="form-table">
    <tbody>
        <tr>
            <th>
                <label for="api-authorize-ip" data-tippy-content="<?php use Infocob\CRM\Products\Admin\Classes\Tools;
	
					echo esc_attr_x("IP addresses allowed to start the import", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Authorize IP", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <input name="api-authorize-ip" type="text" id="api-authorize-ip" class="all-witdh" value="<?php echo esc_attr($api_authorize_ip ?? ""); ?>" placeholder="XX.XXX.XXX.XXX; XX.XXX.XXX.XXX" aria-describedby="api-authorize-ip-description">
				<p class="description" id="api-authorize-ip-description"><?php echo esc_html_x("Your current IP address is", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?>&nbsp;<?php echo esc_html($my_ip ?? ""); ?></p>
            </td>
        </tr>
		<tr>
			<th>
				<label for="api-cron-enable" data-tippy-content="<?php echo esc_attr_x("Enable automatic import task", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Enable CRON task", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="api-cron-enable" type="checkbox" id="api-cron-enable" value="1" <?php if($api_cron_enable ?? true): ?>checked<?php endif; ?>>
			</td>
		</tr>
		<tr>
			<th>
				<label for="api-cron-recurrence"><?php echo esc_html_x("CRON recurrence", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<select name="api-cron-recurrence" id="api-cron-recurrence" class="all-witdh">
					<?php foreach (($api_cron_recurrences ?? []) as $recurrence => $values): ?>
						<option value="<?php echo esc_attr($recurrence ?? ""); ?>" <?php echo ($recurrence === ($api_cron_recurrence ?? "")) ? "selected" : ""; ?>><?php echo esc_html($values["display"] ?? ""); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php if(($api_cron_date ?? false) !== false): ?>
			<tr>
				<th>
					<label for="api-cron-date"><?php echo esc_html_x("Next start at", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?></label>
				</th>
				<td>
					<input type="text" id="api-cron-date" class="all-witdh" min="" value="<?php echo esc_attr($api_cron_date ?? ""); ?>" readonly>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th>
				<label for="rest-api-key-enable"><?php echo esc_html_x("Enable API key", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="rest-api-key-enable" type="checkbox" id="rest-api-key-enable" value="1" <?php if($rest_api_key_enable ?? false): ?>checked<?php endif; ?>>
			</td>
		</tr>
		<tr>
			<th>
				<label for="rest-api-key" data-tippy-content="<?php echo esc_attr_x("Authorization Bearer token needed to access to the API", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?>"><?php echo esc_html_x("API key", "Admin view configuration post, meta-box 'api'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<fieldset>
					<label for="import-rest-api-key" class="all-witdh">
						<input name="rest-api-key" type="text" id="rest-api-key" class="all-witdh" value="<?php echo esc_attr($rest_api_key ?? ""); ?>" aria-describedby="rest-api-key-description">
					</label>
					<br>
					<button id="generate-rest-api-key" type="button"><?php _ex("Generate", "add_settings_field", "infocob-crm-products"); ?></button>
				</fieldset>
			</td>
		</tr>
    </tbody>
</table>

