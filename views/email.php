<table border="1">
	<tr>
		<td><?php _e('Order N', 'eshop'); ?></td>
		<td><?php echo esc_attr($order->ID); ?></td>
	</tr>
	<tr>
		<td><?php _e('Date', 'eshop'); ?></td>
		<td><?php echo esc_attr($order->order_date); ?></td>
	</tr>
	<tr>
		<td><?php _e('Name', 'eshop'); ?></td>
		<td><?php echo esc_attr($order->name); ?></td>
	</tr>
	<tr>
		<td><?php _e('Phone', 'eshop'); ?></td>
		<td><?php echo esc_attr($order->phone); ?></td>
	</tr>
	<tr>
		<td><?php _e('EMail', 'eshop'); ?></td>
		<td><?php echo esc_attr($order->email); ?></td>
	</tr>
	<tr>
		<td><?php _e('Address', 'eshop'); ?></td>
		<td><?php echo esc_attr($order->address); ?></td>
	</tr>
	<tr>
		<td><?php _e('Total', 'eshop'); ?></td>
		<td><?php echo esc_attr($order->sum); ?></td>
	</tr>
</table>

<table border="1">
	<tr>
		<th><?php _e('Name', 'eshop'); ?></th>
		<th><?php _e('Price', 'eshop'); ?></th>
		<th><?php _e('Quantity', 'eshop'); ?></th>
	</tr>
<?php foreach ($order->items as $item): ?>
		<tr>
			<td><?php echo $item->name; ?></td>
			<td><?php echo $item->price; ?></td>
			<td><?php echo $item->quant; ?></td>
		</tr>
<?php endforeach; ?>
</table>