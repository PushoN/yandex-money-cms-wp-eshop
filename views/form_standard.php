<p><?php _e('Redirecting to payment page', 'eshop'); ?></p>
<form method="post" action="https://<?php if ($testMode == 1): ?>demo<?php endif; ?>money.yandex.ru/quickpay/confirm.xml" id="payment_form">
<input type="hidden" name="receiver" value="<?php echo get_option('eshop_account'); ?>" />
<input type="hidden" name="formcomment" value="<?php echo get_option('eshop_payment_descr'); ?>" />
<input type="hidden" name="short-dest" value="<?php echo get_option('eshop_payment_descr'); ?>" />
<input type="hidden" name="quickpay-form" value="shop" />
<input type="hidden" name="targets" value="<?php echo get_option('eshop_payment_descr'); ?>" />
<input type="hidden" name="sum" value="<?php echo $order->sum; ?>" />
<input type="hidden" name="label" value="<?php echo $order->ID; ?>" />
<input type="hidden" name="need-fio" value="false" />
<input type="hidden" name="need-email" value="false" />
<input type="hidden" name="need-phone" value="false" />
<input type="hidden" name="need-address" value="false" />
<input type="hidden" name="paymentType" value="<?php echo $paymentType; ?>" />
<input type="submit" value="<?php _e('Click to continue', 'eshop'); ?>" />
</form>
<script>
window.onload = function () {
	document.getElementById('payment_form').style.display = 'none';
	document.getElementById('payment_form').submit();
}
</script>