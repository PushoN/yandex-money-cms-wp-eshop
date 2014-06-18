<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>

<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="<?php echo date('Y-m-d H:i'); ?>">
	<shop>
		<name><?php echo esc_attr(get_bloginfo('name')); ?></name>
		<company><?php echo esc_attr(get_option('eshop_company')); ?></company>
		<url> <?php echo esc_attr(site_url()); ?> </url>
		<cpa>0</cpa>
		<currencies>
			<currency id="RUR" rate="1"/>
		</currencies>
		<categories>
<?php if (!empty($cats)) foreach ($cats as $cat): ?>
			<category id="<?php echo $cat->ID; ?>"<?php if ($cat->post_parent): ?> parentId="<?php echo $cat->post_parent; ?>"<?php endif; ?>><?php echo esc_attr($cat->post_title); ?></category>
<?php endforeach; ?>
		</categories>
		<offers>
<?php if (!empty($items)) foreach ($items as $item): ?>
			<offer id="<?php echo $item->ID; ?>" type="vendor.model" available="true" bid="13">
				<url><?php echo esc_attr(get_permalink($item->ID)); ?></url>
				<price><?php echo get_post_meta($item->ID, '_price', true); ?></price>
				<currencyId>RUR</currencyId>
				<categoryId><?php echo $item->post_parent; ?></categoryId >
				<picture><?php echo esc_attr($item->image); ?></picture>
				<vendor> НP </vendor>
				<model><?php echo esc_attr($item->post_title); ?></model>
				<description><?php echo esc_attr($item->post_content); ?></description>
				<cpa>0</cpa>
			</offer>
<?php endforeach; ?>
		</offers>
	</shop>
</yml_catalog>