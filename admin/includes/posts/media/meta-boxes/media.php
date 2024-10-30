<table class="form-table">
    <tbody>
		<tr>
			<th>
				<label for="icp-media-order"><?php echo esc_html_x("Display order", "Admin view media post, meta-box 'media", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="icp-media-order" type="number" id="icp-media-order" class="all-witdh" value="<?php echo esc_attr($media_order ?? 0); ?>">
			</td>
		</tr>
        <tr>
            <th>
                <label for="icp-media-infocob-code"><?php echo esc_html_x("Infocob code", "Admin view media post, meta-box 'media", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <input type="text" id="icp-media-infocob-code" class="all-witdh" value="<?php echo esc_attr($media_infocob_code ?? ""); ?>" readonly>
            </td>
        </tr>
		<tr>
			<th>
				<label for="icp-media-upload-date"><?php echo esc_html_x("Upload date", "Admin view media post, meta-box 'media", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input type="text" id="icp-media-upload-date" class="all-witdh" value="<?php echo esc_attr($media_upload_date ?? ""); ?>" readonly>
			</td>
		</tr>
    </tbody>
</table>

