<?php echo $post->post_content; ?>

<?php if (!empty($cats)): ?>
<h2><?php _e('Categories', $this->_pluginName); ?></h2>
<ul>
<?php foreach ($cats as $cat): ?>
	<li><a href="<?php echo get_permalink($cat->ID); ?>"><?php echo $cat->post_title; ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
