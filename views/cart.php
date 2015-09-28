<?php echo $post->post_content; ?>

<h2><?php _e('Items', 'eshop'); ?></h2>
<?php if (!empty($items)): ?>
<form action="" method="post">
<table>
	<thead>
		<tr>
			<th><?php _e('Name', 'eshop'); ?></th>
			<th><?php _e('Price', 'eshop'); ?></th>
			<th><?php _e('Quantity', 'eshop'); ?></th>
			<th><?php _e('Total', 'eshop'); ?></th>
			<th><?php _e('Delete', 'eshop'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $id => $item): ?>
		<tr>
			<td><?php echo $item['name']; ?></td>
			<td><?php echo $item['price']; ?> <?php echo get_option('eshop_currency'); ?></td>
			<td><input type="text" name="eshop_quant[<?php echo $id; ?>]" value="<?php echo $item['quant']; ?>" /></td>
			<td><?php echo $item['total_price']; ?> <?php echo get_option('eshop_currency'); ?></td>
			<td><input type="checkbox" name="eshop_delete[<?php echo $id; ?>]" value="1" /></td>
		</tr>
<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2"><?php _e('Total', 'eshop'); ?></td>
			<td><?php echo $totalQuant; ?></td>
			<td><?php echo $totalPrice; ?></td>
			<td><input type="submit" name="eshop_update" value="<?php _e('Update', 'eshop'); ?>"></td>
		</tr>
	</tfoot>
</table>
</form>

<h2><?php _e('Contact information', 'eshop'); ?></h2>
<form action="" method="post">
<input type="hidden" name="eshop_order_nonce" value="<?php echo $nonce; ?>" />
<table>
	<tbody>
		<tr>
			<td><?php _e('Name', 'eshop'); ?></td>
			<td>
				<input type="text" name="eshop_name" value="<?php echo esc_attr($_POST['eshop_name']); ?>" />
				<?php if (isset($err['eshop_name'])): ?><br/><?php echo $err['eshop_name']; ?><?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><?php _e('Phone', 'eshop'); ?></td>
			<td>
				<input type="text" name="eshop_phone" value="<?php echo esc_attr($_POST['eshop_phone']); ?>" />
				<?php if (isset($err['eshop_phone'])): ?><br/><?php echo $err['eshop_phone']; ?><?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><?php _e('EMail', 'eshop'); ?></td>
			<td>
				<input type="text" name="eshop_email" value="<?php echo esc_attr($_POST['eshop_email']); ?>" />
				<?php if (isset($err['eshop_email'])): ?><br/><?php echo $err['eshop_email']; ?><?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><?php _e('Address', 'eshop'); ?></td>
			<td>
				<input type="text" name="eshop_address" value="<?php echo esc_attr($_POST['eshop_address']); ?>" />
				<?php if (isset($err['eshop_address'])): ?><br/><?php echo $err['eshop_address']; ?><?php endif; ?>
			</td>
		</tr>
		<?php if (get_option('eshop_type')): ?>
		<tr>
			<td><?php _e('Payment method', 'eshop'); ?></td>
			<td>
			<?php if (get_option('eshop_type') == 1): ?>
				<?php if (get_option('eshop_st_payment_type_pc') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypePC" value="PC"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'PC'): ?> checked<?php endif; ?> />
				<label for="paymentTypePC"><?php _e('Yandex Money', 'eshop'); ?></label>
				<?php endif; ?>
				<?php if (get_option('eshop_st_payment_type_ac') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeAC" value="AC"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'AC'): ?> checked<?php endif; ?> />
				<label for="paymentTypeAC"><?php _e('Credit card', 'eshop'); ?></label>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (get_option('eshop_type') == 2): ?>
				<?php if (get_option('eshop_pr_payment_type_pc') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypePC" value="PC"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'PC'): ?> checked<?php endif; ?> />
				<label for="paymentTypePC"><?php _e('Yandex Money', 'eshop'); ?></label>
				<?php endif; ?>
				<?php if (get_option('eshop_pr_payment_type_ac') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeAC" value="AC"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'AC'): ?> checked<?php endif; ?> />
				<label for="paymentTypeAC"><?php _e('Credit card', 'eshop'); ?></label>
				<?php endif; ?>
				<?php if (get_option('eshop_pr_payment_type_gp') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeGP" value="GP"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'GP'): ?> checked<?php endif; ?> />
				<label for="paymentTypeGP"><?php _e('Terminal', 'eshop'); ?></label>
				<?php endif; ?>
				<?php if (get_option('eshop_pr_payment_type_mc') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeMC" value="MC"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'MC'): ?> checked<?php endif; ?> />
				<label for="paymentTypeMC"><?php _e('Mobile phone', 'eshop'); ?></label>
				<?php endif; ?>
				<?php if (get_option('eshop_pr_payment_type_wm') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeMC" value="WM"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'WM'): ?> checked<?php endif; ?> />
				<label for="paymentTypeWM"><?php _e('WebMoney', 'eshop'); ?></label>
				<?php endif; ?>
				
				<?php if (get_option('eshop_pr_payment_type_ab') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeAB" value="AB"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'AB'): ?> checked<?php endif; ?> />
				<label for="paymentTypeAB"><?php _e('Alfa-Click', 'eshop'); ?></label>
				<?php endif; ?>
				<?php if (get_option('eshop_pr_payment_type_sb') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeSB" value="SB"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'SB'): ?> checked<?php endif; ?> />
				<label for="paymentTypeSB"><?php _e('Sberbank Online', 'eshop'); ?></label>
				<?php endif; ?>
				<?php if (get_option('eshop_pr_payment_type_ma') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeMA" value="MA"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'MA'): ?> checked<?php endif; ?> />
				<label for="paymentTypeMA"><?php _e('MasterPass', 'eshop'); ?></label>
				<?php endif; ?>
				<?php if (get_option('eshop_pr_payment_type_pb') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypePB" value="PB"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'PB'): ?> checked<?php endif; ?> />
				<label for="paymentTypePB"><?php _e('Promsvyazbank', 'eshop'); ?></label>
				<?php endif; ?>
				
				<?php if (get_option('eshop_pr_payment_type_qw') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeQW" value="QW"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'QW'): ?> checked<?php endif; ?> />
				<label for="paymentTypeQW"><?php _e('QIWI Wallet', 'eshop'); ?></label>
				<?php endif; ?>
				
				<?php if (get_option('eshop_pr_payment_type_qp') == 1): ?>
				<input type="radio" name="payment_type" id="paymentTypeQP" value="QP"<?php if (!empty($_POST['payment_type']) && $_POST['payment_type'] == 'QP'): ?> checked<?php endif; ?> />
				<label for="paymentTypeQP"><?php _e('Trust payment (Qppi.ru)', 'eshop'); ?></label>
				<?php endif; ?>
			<?php endif; ?>

				<?php if (isset($err['payment_type'])): ?><br/><?php echo $err['payment_type']; ?><?php endif; ?>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td colspan="2"><input type="submit" name="eshop_order" value="<?php _e('Order', 'eshop'); ?>"></td>
		</tr>
	</tbody>
</table>
</form>
<?php else: ?>
<p><?php _e('Cart is empty', 'eshop'); ?></p>
<?php endif; ?>
