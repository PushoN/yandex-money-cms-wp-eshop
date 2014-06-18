<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>EShop-YandexMoney Orders</h2>

<?php if (count($orders) > 0): ?>
	<table class="widefat fixed">
		<thead>
			<tr valign="top">
				<th class="manage-column column-columnname" scope="col">№</th>
				<th class="manage-column column-columnname" scope="col"><?php _e('Date', 'eshop'); ?></th>
				<th class="manage-column column-columnname" scope="col"><?php _e('Items', 'eshop'); ?></th>
				<th class="manage-column column-columnname" scope="col"><?php _e('Information', 'eshop'); ?></th>
				<th class="manage-column column-columnname" scope="col"><?php _e('Total', 'eshop'); ?></th>
<?php if (get_option('eshop_type')): ?>
				<th class="manage-column column-columnname" scope="col"><?php _e('Status', 'eshop'); ?></th>
<?php endif; ?>
			</tr>
		</thead>
		<tbody>
<?php foreach ($orders as $num => $order): ?>
			<tr valign="top"<?php if ($num%2): ?> class="alternate"<?php endif; ?>>
				<td class="column-columnname"><?php echo $order->ID; ?></td>
				<td class="column-columnname"><?php echo $order->order_date; ?></td>
				<td class="column-columnname">
<?php foreach ($order->items as $item): ?>
					<table class="widefat fixed">
						<tr>
							<td class="column-columnname"><?php echo $item->name; ?></td>
							<td class="column-columnname"><?php echo $item->price; ?> <?php echo get_option('eshop_currency'); ?></td>
							<td class="column-columnname"><?php echo $item->quant; ?></td>
						</tr>
					</table>
<?php endforeach; ?>
				</td>
				<td class="column-columnname">
					<?php _e('Name', 'eshop'); ?>: <?php echo $order->name; ?><br/>
					<?php _e('Phone', 'eshop'); ?>: <?php echo $order->phone; ?><br/>
					<?php _e('E-Mail', 'eshop'); ?>: <?php echo $order->email; ?><br/>
					<?php _e('Address', 'eshop'); ?>: <?php echo $order->address; ?><br/>
				</td>
				<td class="column-columnname"><?php echo $order->sum; ?> <?php echo get_option('eshop_currency'); ?></td>
<?php if (get_option('eshop_type')): ?>
				<td class="column-columnname">
					<?php if ($order->status): ?>
					<?php _e('Paid', 'eshop'); ?>
					<?php else: ?>
					<?php _e('New', 'eshop'); ?>
					<?php endif; ?>
				</td>
<?php endif; ?>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
	<div class="pagination">
		Total: <?php echo $ordersCount; ?>
		<?php echo $pagination; ?>
	</div>
<?php else: ?>
	<p><?php _e('No orders found', 'eshop'); ?></p>
<?php endif; ?>
</div>

<style>
.pagination {
    background:#E7E6E5;
    background:rgba(255,255,255,0.35);
    padding:7px 10px 5px;
    -webkit-border-radius:5px;
    -moz-border-radius:5px;
    border-radius:5px;
}
.pagination .page-numbers {
    padding:3px 6px;
    font-size:15px;
    font-weight:bold;
	text-decoration: none;
}
.pagination .current {
    color:#000;
}
</style>




