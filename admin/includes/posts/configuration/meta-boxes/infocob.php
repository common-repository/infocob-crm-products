<table class="form-table">
    <tbody>
		<tr>
			<th>
				<label for="infocob-groupe-droit" data-tippy-content="<?php echo esc_attr_x("Enable rights managements with Infocob (P_DROIT, TIP_DROIT, etc...)", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Rights", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></label>
			</th>
			<td>
				<select name="infocob-groupe-droit" id="infocob-groupe-droit" class="all-witdh">
					<?php foreach(($infocob_groupes_droit ?? []) as $groupe => $libelle): ?>
						<option value="<?php echo esc_attr($groupe); ?>" <?php if(($infocob_groupe_droit ?? 2) === $groupe): ?>selected<?php endif; ?>><?php echo ($groupe !== -1) ? esc_html($groupe) : ""; ?> - <?php echo esc_html($libelle); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
        <tr>
            <th>
                <label for="infocob-type-produit" data-tippy-content="<?php echo esc_attr_x("Table from which you get the data", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Product type", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></label>
            </th>
            <td>
                <select name="infocob-type-produit" id="infocob-type-produit" class="all-witdh" aria-describedby="infocob-type-produit-description">
					<?php foreach(($infocob_types_produit ?? []) as $table => $libelle): ?>
                        <option value="<?php echo esc_attr($table); ?>" <?php if(strcasecmp($infocob_type_produit ?? "", $table) === 0): ?>selected<?php endif; ?>><?php echo esc_html($libelle); ?></option>
					<?php endforeach; ?>
                </select>
				<p class="description" id="infocob-type-produit-description"><?php echo esc_html_x("Warning ! If you change the product type, configurations of step 1 and 2 will be lost !", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></p>
            </td>
        </tr>
		<tr>
			<th>
				<label for="infocob-count-products" data-tippy-content="<?php echo esc_attr_x("Save to update the value", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?>"><?php echo esc_html_x("Products found", "Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?> : <span id="nb_product_count"><?php echo esc_html($infocob_count_products ?? 0); ?></span></label>
			</th>
			<td>
			
			</td>
		</tr>
    </tbody>
</table>

<div id="infocob-filters" class="container-filters" data-module="<?php echo esc_attr($infocob_type_produit ?? ""); ?>">
	<div class="icp-loader active"><div></div></div>
	<input type="hidden" name="infocob-filters" value="<?php echo esc_attr($infocob_filters ?? ""); ?>">
	
	<div class="content-filter">
	
	</div>
	
	<div class="input">
		<button type="button" class="add-row"><?php echo esc_html_x("Add condition","Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></button>
		<button type="button" class="add-group"><?php echo esc_html_x("Add group","Admin view configuration post, meta-box 'infocob'", "infocob-crm-products"); ?></button>
	</div>
</div>
