<h1 class="title cloud-files"><?php echo esc_html_x("Cloud files", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>&ensp;<?php if(!\Infocob\CRM\Products\Admin\Classes\Infocob\Tools\CloudFichierManager::hasCloudService()): echo "(" . esc_html_x("Cloud files Infocob not configured", "Admin view configuration post, meta-box 'files'", "infocob-crm-products") . ")"; endif; ?></h1>
<table class="form-table cloud-files <?php if($files_use_local ?? false): ?>disabled<?php endif; ?>">
    <tbody>
        <tr>
            <th>
                <label for="files-use-cloud" data-tippy-content="<?php echo esc_attr_x("You must choose between cloud and local, you can't enable both", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Use cloud files", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <input name="files-use-cloud" type="checkbox" id="files-use-cloud" value="1" <?php if($files_use_cloud ?? true): ?>checked<?php endif; ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="files-cloud-photos-folder" data-tippy-content="<?php echo esc_attr_x("Folder from which you will get the files (FC_REPERTOIRE)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Photos folder", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <input name="files-cloud-photos-folder" type="text" id="files-cloud-photos-folder" class="all-witdh" value="<?php echo esc_attr($files_cloud_photos_folder ?? ""); ?>">
            </td>
        </tr>
		<tr>
			<th>
				<label for="files-cloud-photos-filename" data-tippy-content="<?php echo esc_attr_x("Filter files to import by defining a pattern that match the files you want (regex)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Photos filename", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="files-cloud-photos-filename" type="text" id="files-cloud-photos-filename" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_cloud_photos_filename ?? ""); ?>">
			</td>
		</tr>
        <tr>
            <th>
                <label for="files-cloud-photos-ext" data-tippy-content="<?php echo esc_attr_x("Files corresponding to these extensions will be imported (empty to ignore)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Photos extensions", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <select name="files-cloud-photos-ext[]" id="files-cloud-photos-ext" class="all-witdh" multiple="multiple">
                    <option value="png" <?php if(in_array("png", $files_cloud_photos_ext ?? [])): ?>selected<?php endif; ?>>PNG</option>
                    <option value="jpg" <?php if(in_array("jpg", $files_cloud_photos_ext ?? [])): ?>selected<?php endif; ?>>JPG</option>
					<option value="pdf" <?php if(in_array("pdf", $files_cloud_photos_ext ?? [])): ?>selected<?php endif; ?>>PDF</option>
					<option value="doc" <?php if(in_array("doc", $files_cloud_photos_ext ?? [])): ?>selected<?php endif; ?>>DOC</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>
                <label for="files-cloud-photos-order" data-tippy-content="<?php echo esc_attr_x("Attach meta data to the media with the meta key '_infocob_fc_order'",  "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Photos order", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <select name="files-cloud-photos-order" id="files-cloud-photos-order" class="all-witdh search" aria-describedby="files-cloud-photos-order-description">
					<?php foreach(($files_champs_cloudfichier ?? []) as $code => $libelle): ?>
                        <option value="<?php echo esc_attr($code); ?>" <?php if(strcasecmp($files_cloud_photos_order ?? "FC_NOMFICHIER", $code) === 0): ?>selected<?php endif; ?>><?php echo esc_html($code); ?> [<?php echo esc_html($libelle); ?>]</option>
					<?php endforeach; ?>
                </select>
                <p class="description" id="files-cloud-photos-order-description"><?php echo esc_html_x("Infocob field to look up to define the photos order", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></p>
            </td>
        </tr>
		<tr>
			<th>
				<label for="files-cloud-photos-name" data-tippy-content="<?php echo esc_attr_x("Ex : {{ FC_NOMFICHIER }}, {{ P_NOM }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Photos name", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="files-cloud-photos-name[<?php echo esc_attr($language); ?>]" type="text" id="files-cloud-photos-name-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_cloud_photos_name[$language] ?? "{{ P_NOM }} - {{ FC_NOMFICHIER }}"); ?>" placeholder="{{ P_NOM }} - {{ FC_NOMFICHIER }}" data-tippy-content="<?php echo esc_attr_x("Photos name (" . $language . ")", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="files-cloud-photos-name-update" type="checkbox" id="files-cloud-photos-name-update" value="1" <?php if ($files_cloud_photos_name_update ?? true): ?>checked<?php endif; ?>>
					<label for="files-cloud-photos-name-update"><?php echo esc_html_x("Update", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="files-cloud-photos-alt-text" data-tippy-content="<?php echo esc_attr_x("Ex : {{ FC_NOMFICHIER }}, {{ P_NOM }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Photos alternative text", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="files-cloud-photos-alt-text[<?php echo esc_attr($language); ?>]" type="text" id="files-cloud-photos-alt-text-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob alternative-text" value="<?php echo esc_attr($files_cloud_photos_alt_text[$language] ?? "{{ P_NOM }} - {{ FC_NOMFICHIER }}"); ?>" placeholder="{{ P_NOM }} - {{ FC_NOMFICHIER }}" data-tippy-content="<?php echo esc_attr_x("Alternative text (" . $language . ")", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="files-cloud-photos-alt-text-update" type="checkbox" id="files-cloud-photos-alt-text-update" value="1" <?php if ($files_cloud_photos_alt_text_update ?? true): ?>checked<?php endif; ?>>
					<label for="files-cloud-photos-alt-text-update"><?php echo esc_html_x("Update", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="files-cloud-photos-legend" data-tippy-content="<?php echo esc_attr_x("Ex : {{ FC_NOMFICHIER }}, {{ P_NOM }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Photos legend", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="files-cloud-photos-legend[<?php echo esc_attr($language); ?>]" type="text" id="files-cloud-photos-legend-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_cloud_photos_legend[$language] ?? "{{ P_NOM }} - {{ FC_NOMFICHIER }}"); ?>" placeholder="{{ P_NOM }} - {{ FC_NOMFICHIER }}" data-tippy-content="<?php echo esc_attr_x("Photos legend (" . $language . ")", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="files-cloud-photos-legend-update" type="checkbox" id="files-cloud-photos-legend-update" value="1" <?php if ($files_cloud_photos_legend_update ?? true): ?>checked<?php endif; ?>>
					<label for="files-cloud-photos-legend-update"><?php echo esc_html_x("Update", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="files-cloud-photos-description" data-tippy-content="<?php echo esc_attr_x("Ex : {{ FC_NOMFICHIER }}, {{ P_NOM }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Photos description", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="files-cloud-photos-description[<?php echo esc_attr($language); ?>]" type="text" id="files-cloud-photos-description-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_cloud_photos_description[$language] ?? "{{ P_TEXTEVITRINE }}"); ?>" placeholder="{{ P_TEXTEVITRINE }}" data-tippy-content="<?php echo esc_attr_x("Photos description (" . $language . ")", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="files-cloud-photos-description-update" type="checkbox" id="files-cloud-photos-description-update" value="1" <?php if ($files_cloud_photos_description_update ?? true): ?>checked<?php endif; ?>>
					<label for="files-cloud-photos-description-update"><?php _ex("Update", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<h1><?php _ex("Photos cloud meta", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></h1>
<div id="photos-cloud-meta" class="container-post-meta" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>">
	<div class="icp-loader active">
		<div></div>
	</div>
	<input type="hidden" name="photos-cloud-meta" value="<?php echo esc_attr($photos_cloud_meta ?? ""); ?>">
	
	<div class="content-post-meta">
	
	</div>
	
	<div class="actions-container">
		<button type="button" class="add-post_meta"><?php _ex("Add photo meta", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></button>
	</div>
</div>

<div class="border-bottom"></div>

<table class="form-table cloud-files <?php if($files_use_local ?? false): ?>disabled<?php endif; ?>">
	<tbody>
        <tr>
            <th>
                <label for="files-cloud-files-folder" data-tippy-content="<?php echo esc_attr_x("Folder from which you will get the files (FC_REPERTOIRE)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files folder", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <input name="files-cloud-files-folder" type="text" id="files-cloud-files-folder" class="all-witdh" value="<?php echo esc_attr($files_cloud_files_folder ?? ""); ?>">
            </td>
        </tr>
		<tr>
			<th>
				<label for="files-cloud-files-filename" data-tippy-content="<?php echo esc_attr_x("Filter files to import by defining a pattern that correspond to the files you want (regex)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files filename", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="files-cloud-files-filename" type="text" id="files-cloud-files-filename" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_cloud_files_filename ?? ""); ?>">
			</td>
		</tr>
        <tr>
            <th>
                <label for="files-cloud-files-ext" data-tippy-content="<?php echo esc_attr_x("Files corresponding to these extensions will be imported (empty to ignore)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files extensions", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <select name="files-cloud-files-ext[]" id="files-cloud-files-ext" class="all-witdh" multiple="multiple">
					<option value="png" <?php if(in_array("png", $files_cloud_files_ext ?? [])): ?>selected<?php endif; ?>>PNG</option>
					<option value="jpg" <?php if(in_array("jpg", $files_cloud_files_ext ?? [])): ?>selected<?php endif; ?>>JPG</option>
					<option value="pdf" <?php if(in_array("pdf", $files_cloud_files_ext ?? [])): ?>selected<?php endif; ?>>PDF</option>
					<option value="doc" <?php if(in_array("doc", $files_cloud_files_ext ?? [])): ?>selected<?php endif; ?>>DOC</option>
                </select>
            </td>
        </tr>
		<tr>
			<th>
				<label for="files-cloud-files-order" data-tippy-content="<?php echo esc_attr_x("Attach meta data to the media with the meta key '_infocob_fc_order'",  "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files order", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<select name="files-cloud-files-order" id="files-cloud-files-order" class="all-witdh search" aria-describedby="files-cloud-files-order-description">
					<?php foreach(($files_champs_cloudfichier ?? []) as $code => $libelle): ?>
						<option value="<?php echo esc_attr($code); ?>" <?php if(strcasecmp($files_cloud_files_order ?? "FC_NOMFICHIER", $code) === 0): ?>selected<?php endif; ?>><?php echo esc_html($code); ?> [<?php echo esc_html($libelle); ?>]</option>
					<?php endforeach; ?>
				</select>
				<p class="description" id="files-cloud-files-order-description"><?php _ex("Infocob field to look up to define the files order", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></p>
			</td>
		</tr>
		<tr>
			<th>
				<label for="files-cloud-files-name" data-tippy-content="<?php echo esc_attr_x("Ex : {{ FC_NOMFICHIER }}, {{ P_NOM }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files name", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="files-cloud-files-name[<?php echo esc_attr($language); ?>]" type="text" id="files-cloud-files-name-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_cloud_files_name[$language] ?? "{{ P_NOM }} - {{ FC_NOMFICHIER }}"); ?>" placeholder="{{ P_NOM }} - {{ FC_NOMFICHIER }}" data-tippy-content="<?php echo esc_attr_x("Files name (" . $language . ")", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="files-cloud-files-name-update" type="checkbox" id="files-cloud-files-name-update" value="1" <?php if ($files_cloud_files_name_update ?? true): ?>checked<?php endif; ?>>
					<label for="files-cloud-files-name-update"><?php _ex("Update", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="files-cloud-files-alt-text" data-tippy-content="<?php echo esc_attr_x("Ex : {{ FC_NOMFICHIER }}, {{ P_NOM }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files alternative text", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="files-cloud-files-alt-text[<?php echo esc_attr($language); ?>]" type="text" id="files-cloud-files-alt-text-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob alternative-text" value="<?php echo esc_attr($files_cloud_files_alt_text[$language] ?? "{{ P_NOM }} - {{ FC_NOMFICHIER }}"); ?>" placeholder="{{ P_NOM }} - {{ FC_NOMFICHIER }}" data-tippy-content="<?php echo esc_attr_x("Alternative text (" . $language . ")", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="files-cloud-files-alt-text-update" type="checkbox" id="files-cloud-files-alt-text-update" value="1" <?php if ($files_cloud_files_alt_text_update ?? true): ?>checked<?php endif; ?>>
					<label for="files-cloud-files-alt-text-update"><?php _ex("Update", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="files-cloud-files-legend" data-tippy-content="<?php echo esc_attr_x("Ex : {{ FC_NOMFICHIER }}, {{ P_NOM }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files legend", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="files-cloud-files-legend[<?php echo esc_attr($language); ?>]" type="text" id="files-cloud-files-legend-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_cloud_files_legend[$language] ?? "{{ P_NOM }} - {{ FC_NOMFICHIER }}"); ?>" placeholder="{{ P_NOM }} - {{ FC_NOMFICHIER }}" data-tippy-content="<?php echo esc_attr_x("Files legend (" . $language . ")", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="files-cloud-files-legend-update" type="checkbox" id="files-cloud-files-legend-update" value="1" <?php if ($files_cloud_files_legend_update ?? true): ?>checked<?php endif; ?>>
					<label for="files-cloud-files-legend-update"><?php _ex("Update", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="files-cloud-files-description" data-tippy-content="<?php echo esc_attr_x("Ex : {{ FC_NOMFICHIER }}, {{ P_NOM }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files description", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<?php foreach ($languages ?? [] as $language): ?>
				<td>
					<input name="files-cloud-files-description[<?php echo esc_attr($language); ?>]" type="text" id="files-cloud-files-description-<?php echo esc_attr($language); ?>" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_cloud_files_description[$language] ?? "{{ P_TEXTEVITRINE }}"); ?>" placeholder="{{ P_TEXTEVITRINE }}" data-tippy-content="<?php echo esc_attr_x("Files description (" . $language . ")", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>">
				</td>
			<?php endforeach; ?>
			<td>
				<div class="sub-field">
					<input name="files-cloud-files-description-update" type="checkbox" id="files-cloud-files-description-update" value="1" <?php if ($files_cloud_files_description_update ?? true): ?>checked<?php endif; ?>>
					<label for="files-cloud-files-description-update"><?php _ex("Update", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
				</div>
			</td>
		</tr>
    </tbody>
</table>

<h1><?php _ex("Files cloud meta", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></h1>
<div id="files-cloud-meta" class="container-post-meta" data-langs="<?php echo esc_attr($b64JsonLanguages ?? ""); ?>">
	<div class="icp-loader active">
		<div></div>
	</div>
	<input type="hidden" name="files-cloud-meta" value="<?php echo esc_attr($files_cloud_meta ?? ""); ?>">
	
	<div class="content-post-meta">
	
	</div>
	
	<div class="actions-container">
		<button type="button" class="add-post_meta"><?php _ex("Add file meta", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></button>
	</div>
</div>

<div class="border-bottom"></div>

<h1 class="title local-files"><?php _ex("Local files (not recommended)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></h1>
<table class="form-table local-files <?php if($files_use_cloud ?? false): ?>disabled<?php endif; ?>">
    <tbody>
        <tr>
            <th>
                <label for="files-use-local" data-tippy-content="<?php echo esc_attr_x("You must choose between cloud and local, you can't enable both", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Use local files", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <input name="files-use-local" type="checkbox" id="files-use-local" value="1" <?php if($files_use_local ?? true): ?>checked<?php endif; ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="files-local-photos-path" data-tippy-content="<?php echo esc_attr_x("Folder path from which you will get the files (ex: public_html/photos)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Photos path", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <input name="files-local-photos-path" type="text" id="files-local-photos-path" class="all-witdh" value="<?php echo esc_attr($files_local_photos_path ?? ""); ?>">
            </td>
        </tr>
		<tr>
			<th>
				<label for="files-local-photos-name" data-tippy-content="<?php echo esc_attr_x("Ex : {{ P_NOM }}, {{ P_CONSTRUCTEUR }}, etc...)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Photos name", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<input name="files-local-photos-name" type="text" id="files-local-photos-name" class="all-witdh autocomplete-fields-infocob" value="<?php echo esc_attr($files_local_photos_name ?? ""); ?>" placeholder="{{ P_NOM }}-{{ P_CONTRAT }}">
			</td>
		</tr>
	</tbody>
</table>

<div class="border-bottom"></div>

<table class="form-table local-files <?php if($files_use_cloud ?? false): ?>disabled<?php endif; ?>">
	<tbody>
        <tr>
            <th>
                <label for="files-local-files-path" data-tippy-content="<?php echo esc_attr_x("Folder path from which you will get the files (ex: public_html/fichiers/pdf)", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?>"><?php _ex("Files path", "Admin view configuration post, meta-box 'files'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <input name="files-local-files-path" type="text" id="files-local-files-path" class="all-witdh" value="<?php echo esc_attr($files_local_files_path ?? ""); ?>">
            </td>
        </tr>
    </tbody>
</table>
