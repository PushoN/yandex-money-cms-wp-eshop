<?php echo $post->post_content; ?>

<p><?php _e('Price', $this->_pluginName); ?>: <?php echo get_post_meta($post->ID, '_price', true); ?> <?php echo get_option('eshop_currency'); ?></p>

<form action="" method="post">
	<input type="hidden" name="eshop_item_id" value="<?php echo $post->ID; ?>" />
	<input type="text" name="eshop_quant" value="1" />
	<input type="submit" name="eshop_add" value="<?php _e('Add', 'eshop'); ?>" />
</form>