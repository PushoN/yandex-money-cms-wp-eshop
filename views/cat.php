<?php echo $post->post_content; ?>

<?php if (!empty($subcats)): ?>
<h2><?php _e('Subcategories', $this->_pluginName); ?></h2>
<ul>
<?php foreach ($subcats as $subcat): ?>
	<li><a href="<?php echo get_permalink($subcat->ID); ?>"><?php echo $subcat->post_title; ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($items)): ?>
<h2><?php _e('Items', $this->_pluginName); ?></h2>
<ul>
<?php foreach ($items as $item): ?>
	<li>
		<?php if ($item->image): ?>
		<img src="<?php echo $item->image; ?>" alt="" />
		<?php endif; ?>
		<p>
			<a href="<?php echo get_permalink($item->ID); ?>"><?php echo $item->post_title; ?></a>
			<span><?php echo get_post_meta($item->ID, '_price', true); ?> <?php echo get_option('eshop_currency'); ?></span>
		</p>
		<form action="" method="post">
			<input type="hidden" name="eshop_item_id" value="<?php echo $item->ID; ?>" />
			<input type="text" name="eshop_quant" value="1" />
			<input type="submit" name="eshop_add" value="<?php _e('Add', 'eshop'); ?>" />
		</form>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
