<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>EShop-YandexMoney Settings</h2>
	<p>Любое использование Вами программы означает полное и безоговорочное принятие Вами условий лицензионного договора, размещенного по адресу https://money.yandex.ru/doc.xml?id=527132 (далее – «Лицензионный договор»). Если Вы не принимаете условия Лицензионного договора в полном объёме, Вы не имеете права использовать программу в каких-либо целях.
	</p>
	<form method="post" action="options.php">
<?php
settings_fields('eshop_settings');
$pages = get_pages();
?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e('Shop form type', 'eshop'); ?>
				</th>
				<td>
					<select name="eshop_type">
						<option value="0"><?php _e('Off', 'eshop'); ?></option>
						<option value="1"<?php if(get_option('eshop_type') == 1): ?> selected<?php endif; ?>><?php _e('Standard form (no agreement)', 'eshop'); ?></option>
						<option value="2"<?php if(get_option('eshop_type') == 2): ?> selected<?php endif; ?>><?php _e('Protocol form (with agreement)', 'eshop'); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Shop test mode', 'eshop'); ?>
				</th>
				<td>
					<select name="eshop_test_mode">
						<option value="0"><?php _e('Off', 'eshop'); ?></option>
						<option value="1"<?php if(get_option('eshop_test_mode') == 1): ?> selected<?php endif; ?>><?php _e('On', 'eshop'); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<h3 class="title"><?php _e('Settings for standard form', 'eshop'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e('Shop account', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_account" value="<?php echo esc_attr(get_option('eshop_account')); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Secret', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_secret" value="<?php echo esc_attr(get_option('eshop_secret')); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Payment description', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_payment_descr" value="<?php echo esc_attr(get_option('eshop_payment_descr')); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Payment types', 'eshop'); ?>
				</th>
				<td>
					<input type="checkbox" name="eshop_st_payment_type_pc" id="stPaymentTypePC" value="1"<?php if (get_option('eshop_st_payment_type_pc') == 1): ?> checked<?php endif; ?> />
					<label for="stPaymentTypePC"><?php _e('Yandex Money', 'eshop'); ?></label>
					<input type="checkbox" name="eshop_st_payment_type_ac" id="stPaymentTypeAC" value="1"<?php if (get_option('eshop_st_payment_type_ac') == 1): ?> checked<?php endif; ?> />
					<label for="stPaymentTypeAC"><?php _e('Credit card', 'eshop'); ?></label>
				</td>
			</tr>
		</table>
		<h3 class="title"><?php _e('Settings for protocol form', 'eshop'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e('Shop identificator', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_sid" value="<?php echo esc_attr(get_option('eshop_sid')); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Showcase identificator', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_scid" value="<?php echo esc_attr(get_option('eshop_scid')); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Password', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_password" value="<?php echo esc_attr(get_option('eshop_password')); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Success page', 'eshop'); ?>
				</th>
				<td>
					<select name="eshop_success_page">
						<option value=""><?php _e('Select a page..', 'eshop'); ?></option>
					<?php foreach ($pages as $page): ?>
						<option value="<?php echo $page->ID; ?>"<?php if (get_option('eshop_success_page') == $page->ID): ?> selected="selected"<?php endif; ?>><?php echo $page->post_title; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Fail page', 'eshop'); ?>
				</th>
				<td>
					<select name="eshop_fail_page">
						<option value=""><?php _e('Select a page..', 'eshop'); ?></option>
					<?php foreach ($pages as $page): ?>
						<option value="<?php echo $page->ID; ?>"<?php if (get_option('eshop_fail_page') == $page->ID): ?> selected="selected"<?php endif; ?>><?php echo $page->post_title; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Payment types', 'eshop'); ?>
				</th>
				<td>
					<input type="checkbox" name="eshop_pr_payment_type_pc" id="prPaymentTypePC" value="1"<?php if (get_option('eshop_pr_payment_type_pc') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypePC"><?php _e('Yandex Money', 'eshop'); ?></label>
					<input type="checkbox" name="eshop_pr_payment_type_ac" id="prPaymentTypeAC" value="1"<?php if (get_option('eshop_pr_payment_type_ac') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeAC"><?php _e('Credit card', 'eshop'); ?></label>
					<input type="checkbox" name="eshop_pr_payment_type_gp" id="prPaymentTypeGP" value="1"<?php if (get_option('eshop_pr_payment_type_gp') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeGP"><?php _e('Terminal', 'eshop'); ?></label>
					<input type="checkbox" name="eshop_pr_payment_type_mc" id="prPaymentTypeMC" value="1"<?php if (get_option('eshop_pr_payment_type_mc') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeMC"><?php _e('Mobile phone', 'eshop'); ?></label>
					<input type="checkbox" name="eshop_pr_payment_type_wm" id="prPaymentTypeWM" value="1"<?php if (get_option('eshop_pr_payment_type_wm') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeWM"><?php _e('WebMoney', 'eshop'); ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"></th>
				<td>
					<input type="checkbox" name="eshop_pr_payment_type_ab" id="prPaymentTypeAB" value="1"<?php if (get_option('eshop_pr_payment_type_ab') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeAB"><?php _e('Alfa-Click', 'eshop'); ?></label>
					<input type="checkbox" name="eshop_pr_payment_type_sb" id="prPaymentTypeSB" value="1"<?php if (get_option('eshop_pr_payment_type_sb') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeSB"><?php _e('Sberbank Online', 'eshop'); ?></label>
					<input type="checkbox" name="eshop_pr_payment_type_ma" id="prPaymentTypeMA" value="1"<?php if (get_option('eshop_pr_payment_type_ma') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeMA"><?php _e('MasterPass', 'eshop'); ?></label>
					<input type="checkbox" name="eshop_pr_payment_type_pb" id="prPaymentTypePB" value="1"<?php if (get_option('eshop_pr_payment_type_pb') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypePB"><?php _e('Promsvyazbank', 'eshop'); ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"></th>
				<td>
					<input type="checkbox" name="eshop_pr_payment_type_qw" id="prPaymentTypeQW" value="1"<?php if (get_option('eshop_pr_payment_type_qw') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeQW"><?php _e('Payment via QIWI Wallet', 'eshop'); ?></label>
					
					<input type="checkbox" name="eshop_pr_payment_type_qp" id="prPaymentTypeQP" value="1"<?php if (get_option('eshop_pr_payment_type_qp') == 1): ?> checked<?php endif; ?> />
					<label for="prPaymentTypeQP"><?php _e('Trust payment (Qppi.ru)', 'eshop'); ?></label>
				</td>
			</tr>
		</table>
		<h3 class="title"><?php _e('Common settings', 'eshop'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e('EShop index page', 'eshop'); ?>
				</th>
				<td>
					<select name="eshop_index_page">
						<option value=""><?php _e('Select a page..', 'eshop'); ?></option>
					<?php foreach ($pages as $page): ?>
						<option value="<?php echo $page->ID; ?>"<?php if (get_option('eshop_index_page') == $page->ID): ?> selected="selected"<?php endif; ?>><?php echo $page->post_title; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Cart page', 'eshop'); ?>
				</th>
				<td>
					<select name="eshop_cart_page">
						<option value=""><?php _e('Select a page..', 'eshop'); ?></option>
					<?php foreach ($pages as $page): ?>
						<option value="<?php echo $page->ID; ?>"<?php if (get_option('eshop_cart_page') == $page->ID): ?> selected="selected"<?php endif; ?>><?php echo $page->post_title; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Email for orders', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_orders_email" value="<?php echo esc_attr(get_option('eshop_orders_email')); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Company name', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_company" value="<?php echo esc_attr(get_option('eshop_company')); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Currency name', 'eshop'); ?>
				</th>
				<td>
					<input class="regular-text ltr" type="text" name="eshop_currency" value="<?php echo esc_attr(get_option('eshop_currency')); ?>" />
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Save Changes', $this->_pluginName) ?>" class="button-primary"/>
		</p>
	</form>
</div>
