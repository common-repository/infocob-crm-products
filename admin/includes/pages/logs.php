<h1><?php echo esc_html_x("Logs", "Admin view logs", "infocob-crm-products"); ?></h1>

<table class="form-table">
    <tbody>
        <tr>
            <th>
                <label for="logs-file"><?php echo esc_html_x("Files", "Admin view logs", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <select id="logs-file" class="all-witdh">
                    <option value=""></option>
				    <?php foreach(($logs ?? []) as $level => $files): ?>
	                    <?php foreach(($files ?? []) as $file): ?>
                            <option data-level="<?php echo esc_attr($level); ?>" value="<?php echo esc_attr($file["filename_without_ext"] ?? ""); ?>"><?php echo esc_html("[" . $level . "] " . $file["filename_without_ext"] ?? ""); ?></option>
                        <?php endforeach; ?>
				    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </tbody>
</table>

<div id="container-logs">
	<table id="logs" class="display nowrap" style="width:100%">
		<thead>
			<tr>
				<th></th>
				<th><?php echo esc_html_x("Message", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Type", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Infocob code", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Post ID", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Lang", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Date", "Admin view logs", "infocob-crm-products"); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th></th>
				<th><?php echo esc_html_x("Message", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Type", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Infocob code", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Post ID", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Lang", "Admin view logs", "infocob-crm-products"); ?></th>
				<th><?php echo esc_html_x("Date", "Admin view logs", "infocob-crm-products"); ?></th>
			</tr>
		</tfoot>
	</table>
</div>
