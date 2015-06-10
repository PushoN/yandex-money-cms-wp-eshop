<p><?php _e('Redirecting to payment page', 'eshop'); ?></p>
<form method="post" action="https://<?php if ($testMode == 1): ?>demo<?php endif; ?>money.yandex.ru/eshop.xml" id="payment_form">
<input type="hidden" name="shopId" value="<?php echo get_option('eshop_sid'); ?>" />
<input type="hidden" name="scid" value="<?php echo get_option('eshop_scid'); ?>" />
<input type="hidden" name="sum" value="<?php echo $order->sum; ?>" />
<input type="hidden" name="customerNumber" value="<?php echo $order->id; ?>" />
<input type="hidden" name="orderNumber" value="<?php echo $order->id; ?>" />
<input type="hidden" name="cps_email" value="<?php echo $order->email; ?>" />
<input type="hidden" name="cps_phone" value="<?php echo $order->phone; ?>" />
<input type="hidden" name="shopSuccessURL" value="<?php echo get_permalink(get_option('eshop_success_page')); ?>" />
<input type="hidden" name="shopFailURL" value="<?php echo get_permalink(get_option('eshop_fail_page')); ?>" />
<input type="hidden" name="paymentType" value="<?php echo $paymentType; ?>" />
<input type="hidden" name="cms_name" value="wordpress_eshop" />
<input type="submit" value="<?php _e('Click to continue', 'eshop'); ?>" />
</form>
<script>
window.onload = function () {
	document.getElementById('payment_form').style.display = 'none';
	document.getElementById('payment_form').submit();
}
</script>