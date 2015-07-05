<?php
	/*
	Plugin Name: eShop Yandex.Money
	Plugin URI: https://github.com/yandex-money/yandex-money-cms-wp-eshop
	Description: Online shop with Yandex.Money support.
	Version: 1.2.0
	Author: Yandex.Money
	Author URI: http://money.yandex.ru
   License: https://money.yandex.ru/doc.xml?id=527132
   */

if ( !function_exists( 'add_action' ) ) {
	echo 'Can\'t call directly';
	exit;
}
class yamoney_statistics {
	public function __construct(){
		$this->send();
	}

	private function send()
	{
		global $wp_version;
		$type=get_option('eshop_type');
		if ($type==0)	return;
		$array = array(
			'url' => get_option('siteurl'),
			'cms' => 'wordpress',
			'version' => $wp_version,
			'ver_mod' => '1.2.0',
			'yacms' => false,
			'email' => get_option('admin_email'),
			'shopid' => get_option('eshop_sid'),
			'settings' => array(
				'kassa' => ($type==2)?true:false,
				'p2p'=> ($type==1)?true:false
			)
		);

		$key_crypt = gethostbyname($_SERVER['HTTP_HOST']);
		$array_crypt = $this->crypt_encrypt($array, $key_crypt);

		$url = 'https://statcms.yamoney.ru/';
		$curlOpt = array(
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLINFO_HEADER_OUT => true,
			CURLOPT_POST => true,
		);

		$curlOpt[CURLOPT_HTTPHEADER] = array('Content-Type: application/x-www-form-urlencoded');
		$curlOpt[CURLOPT_POSTFIELDS] = http_build_query(array('data' => $array_crypt));

		$curl = curl_init($url);
		curl_setopt_array($curl, $curlOpt);
		$rbody = curl_exec($curl);
		$errno = curl_errno($curl);
		$error = curl_error($curl);
		$rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
	}
	
	private function crypt_encrypt($data, $key)
	{
		$key = hash('sha256', $key, true);
		$data = serialize($data);
		$init_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		$str = $this->randomString(strlen($key)).$init_vect.mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_CBC, $init_vect);
		return base64_encode($str);
	}

	private function randomString($len)
	{
		$str = '';
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$pool_len = strlen($pool);
		for ($i = 0; $i < $len; $i++) {
			$str .= substr($pool, mt_rand(0, $pool_len - 1), 1);
		}
		return $str;
	}
}
class EShopYandexMoney
{
	private $_version = '1.0';
	private $_pluginPath;
	private $_viewsPath;

	function __construct()
	{
		$this->_pluginPath = plugin_dir_path(__FILE__);
		$this->_viewsPath = $this->_pluginPath.'/views/';
		add_action('init', array($this, 'init'), 1);
		register_activation_hook(__FILE__, array($this, 'install'));
	}

	function getPluginName()
	{
		return $this->_pluginName;
	}
	
	function install()
	{
		global $wpdb;
		$sql = 'CREATE TABLE '.$wpdb->prefix.'eshop_orders (
			`ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`order_date` DATETIME NOT NULL,
			`name` TEXT NOT NULL,
			`phone` TEXT NOT NULL,
			`email` TEXT NOT NULL,
			`address` TEXT NOT NULL,
			`items` MEDIUMTEXT NOT NULL,
			`sum` DECIMAL(9,2) NOT NULL,
			`status` TINYINT UNSIGNED NOT NULL DEFAULT \'0\',
			`payment_data` TEXT NULL
		)';

		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		update_option('eshop_version', $this->_version);
		update_option('eshop_currency', 'руб');
		update_option('eshop_type', 0);
	}

	function init()
	{
		session_start();
		if (!isset($_SESSION['eshop_cart']) || !is_array($_SESSION['eshop_cart'])) $_SESSION['eshop_cart'] = array();

		//load_plugin_textdomain($this->_pluginName, false, $this->_pluginPath.'/languages/');

		add_action('parse_request', array($this, 'checkPayment'));
		//add_action('parse_request', array($this, 'renderMarketYml'));
		add_action('widgets_init', array($this, 'initWidgets'));
		add_filter('wp_get_attachment_url', 'set_url_scheme');
		add_filter('the_content', array($this, 'initContent'));

		$this->initCategories();
		$this->initItems();

		if(is_admin()) {
			add_action('admin_init', array($this, 'adminRegisterSettings'));
			add_action('admin_menu', array($this, 'adminMenu'));
			
		}

		if (!empty($_POST)) $this->checkActions();
	}

	function renderMarketYml()
	{
		if (trim($_SERVER['REQUEST_URI'], '/') == 'eshop/yml') {
			$cats = get_posts(array('post_type' => 'eshop_cat', 'order' => 'ASC'));
			$items = get_posts(array('post_type' => 'eshop_item', 'order' => 'ASC'));
			if (!empty($items)) foreach ($items as &$item) {
				$item->image = $this->getFeaturedImage($item->ID);
			}
			unset($item);
			header('Content-Type: application/xml');
			include($this->_viewsPath.'market_yml.php');
			die();
		}
	}

	function checkPayment()
	{
		global $wpdb;

		if (trim($_SERVER['REQUEST_URI'], '/') == 'eshop/check') {
			$type = get_option('eshop_type');
			if ($type == 1) {
				$hash = sha1($_POST['notification_type'].'&'.$_POST['operation_id'].'&'.$_POST['amount'].
							'&'.$_POST['currency'].'&'.$_POST['datetime'].'&'.$_POST['sender'].'&'.
							$_POST['codepro'].'&'.get_option('eshop_secret').'&'.$_POST['label']);
				if (strtolower($hash) == strtolower($_POST['sha1_hash'])) {
					$order = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'eshop_orders WHERE ID = '.(int)$_POST['label']);
					if ($order && $order->sum == $_POST['withdraw_amount']) {
						$data = array(
							'status' => 1,
							'payment_data' => json_encode(array(
								'operationId' => $_POST['operation_id'],
								'notificationType' => $_POST['notification_type'],
								'amount' => $_POST['amount'],
								'currency' => $_POST['currency'],
								'datetime' => $_POST['datetime'],
								'sender' => $_POST['sender'],
								'withdraw_amount' => $_POST['withdraw_amount']
							))
						);
						$wpdb->update($wpdb->prefix.'eshop_orders', $data, array('ID' => $order->ID));
					}
				}
				die();
			} elseif ($type == 2) {
				$hash = md5($_POST['action'].';'.$_POST['orderSumAmount'].';'.$_POST['orderSumCurrencyPaycash'].';'.
							$_POST['orderSumBankPaycash'].';'.$_POST['shopId'].';'.$_POST['invoiceId'].';'.
							$_POST['customerNumber'].';'.get_option('eshop_password'));
				if (strtolower($hash) != strtolower($_POST['md5'])) {
					$code = 1;
				} else {
					$order = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'eshop_orders WHERE ID = '.(int)$_POST['orderNumber']);
					if (!$order) {
						$code = 200;
					} elseif ($order->sum != $_POST['orderSumAmount']) {
						$code = 100;
					} else {
						$code = 0;
						if ($_POST['action'] == 'paymentAviso') {
							$data = array(
								'status' => 1,
								'payment_data' => json_encode(array(
									'invoiceId' => $_POST['invoiceId'],
									'orderCreatedDatetime' => $_POST['orderCreatedDatetime'],
									'orderSumAmount' => $_POST['orderSumAmount'],
									'orderSumCurrencyPaycash' => $_POST['orderSumCurrencyPaycash'],
									'orderSumBankPaycash' => $_POST['orderSumBankPaycash'],
									'shopSumAmount' => $_POST['shopSumAmount'],
									'shopSumCurrencyPaycash' => $_POST['shopSumCurrencyPaycash'],
									'shopSumBankPaycash' => $_POST['shopSumBankPaycash'],
									'paymentPayerCode' => $_POST['paymentPayerCode'],
									'paymentDatetime' => $_POST['paymentDatetime'],
									'paymentType' => $_POST['paymentType']
								))
							);
							if (!$wpdb->update($wpdb->prefix.'eshop_orders', $data, array('ID' => $order->ID))) {
								$code = 1000;
							}
						}
					}
				}
				header('Content-Type: application/xml');
				include($this->_viewsPath.'response_xml.php');
				die();
			} else {
				die();
			}
		}
	}

	function checkActions()
	{
		global $wpdb, $wp;

		if (isset($_POST['eshop_add']) &&
				isset($_POST['eshop_quant']) && $_POST['eshop_quant'] > 0 &&
				isset($_POST['eshop_item_id'])) {
			$post = get_post($_POST['eshop_item_id']);
			if ($post) {
				$quant = (int)$_POST['eshop_quant'];
				if (!empty($_SESSION['eshop_cart'][$post->ID])) {
					$_SESSION['eshop_cart'][$post->ID]['quant'] += $quant;
				} else {
					$_SESSION['eshop_cart'][$post->ID] = array(
						'name' => $post->post_title,
						'price' => get_post_meta($post->ID, '_price', true),
						'quant' => $quant
					);
				}
			}
			wp_redirect($_SERVER['REQUEST_URI']);
			die();
		}
		if (!empty($_POST['eshop_update']) && !empty($_SESSION['eshop_cart'])) {
			foreach ($_SESSION['eshop_cart'] as $id => $item) {
				$delete = isset($_POST['eshop_delete'][$id])?1:0;
				$quant = isset($_POST['eshop_quant'][$id])?(int)$_POST['eshop_quant'][$id]:0;
				if ($delete || $quant <= 0) {
					unset($_SESSION['eshop_cart'][$id]);
				} elseif ($quant > 0) {
					$_SESSION['eshop_cart'][$id]['quant'] = $quant;
				}
			}
			$postId = url_to_postid($_SERVER['REQUEST_URI']);
			wp_redirect(get_permalink($postId));
			die();
		}
		if (!empty($_POST['eshop_order']) && !empty($_SESSION['eshop_cart'])) {
			$_POST['eshop_name'] = isset($_POST['eshop_name'])?sanitize_text_field($_POST['eshop_name']):'';
			$_POST['eshop_email'] = isset($_POST['eshop_email'])?sanitize_text_field($_POST['eshop_email']):'';
			$_POST['eshop_phone'] = isset($_POST['eshop_phone'])?sanitize_text_field($_POST['eshop_phone']):'';
			$_POST['eshop_address'] = isset($_POST['eshop_address'])?sanitize_text_field($_POST['eshop_address']):'';
			$_POST['payment_type'] = isset($_POST['payment_type'])?sanitize_text_field($_POST['payment_type']):'';
			if (wp_verify_nonce($_POST['eshop_order_nonce'], plugin_basename(__FILE__))) {
				$type = get_option('eshop_type');
				$err = array();
				if (empty($_POST['eshop_name'])) {
					$err['eshop_name'] = __('Required field', 'eshop');
				}
				if (empty($_POST['eshop_email'])) {
					$err['eshop_email'] = __('Required field', 'eshop');
				}
				elseif (!is_email($_POST['eshop_email'])) {
					$err['eshop_email'] = __('Wrong e-mail', 'eshop');
				}
				if (empty($_POST['eshop_phone'])) {
					$err['eshop_phone'] = __('Required field', 'eshop');
				}
				elseif (!is_numeric($_POST['eshop_phone'])) {
					$err['eshop_phone'] = __('Wrong phone', 'eshop');
				}
				if (empty($_POST['eshop_address'])) {
					$err['eshop_address'] = __('Required field', 'eshop');
				}
				if ($type) {
					if (empty($_POST['payment_type'])) {
						$err['payment_type'] = __('Required field', 'eshop');
					} else {
						if ($type == 1) {
							if (!in_array($_POST['payment_type'], array('AC', 'PC'))) $err['payment_type'] = __('Wrong type', 'eshop');
						} else {
							if (!in_array($_POST['payment_type'], array('AC', 'PC', 'GP', 'MC', 'WM','AB','SB','MA','PB'))) $err['payment_type'] = __('Wrong type', 'eshop');
						}
					}
				}
				if (empty($err)) {
					$totalSum = 0;
					foreach ($_SESSION['eshop_cart'] as $item) {
						$totalSum += round($item['quant'] * $item['price'], 2);
					}
					$wpdb->insert($wpdb->prefix.'eshop_orders', array(
						'order_date' => date('Y-m-d H:i:s'),
						'name' => $_POST['eshop_name'],
						'email' => $_POST['eshop_email'],
						'phone' => $_POST['eshop_phone'],
						'address' => $_POST['eshop_address'],
						'items' => json_encode($_SESSION['eshop_cart']),
						'sum' => $totalSum
						), array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
					);
					$id = $wpdb->insert_id;
					if (!$id) wp_die('Internal error while processing order, try again later');
					$order = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'eshop_orders WHERE ID = '.$id);
					$order->items = json_decode($order->items);
					$email = get_option('eshop_orders_email');
					if ($email) {
						$dateFormat = get_option('date_format');
						$timeFormat = get_option('time_format');
						if ($dateFormat && $timeFormat) {
							$order->order_date = date($dateFormat.' '.$timeFormat, strtotime($order->order_date));
						}
						ob_start();
						include($this->_viewsPath.'email.php');
						$message = ob_get_clean();
						$headers = array('Content-Type: text/html');
						wp_mail($email, __('New order', 'eshop'), $message, $headers);
					}

					unset($_SESSION['eshop_cart']);

					$testMode = get_option('eshop_test_mode');
					$paymentType = $_POST['payment_type'];
					if ($type == 1) {
						ob_start();
						include($this->_viewsPath.'form_standard.php');
						$content = ob_get_clean();
						wp_die($content, __('Redirecting'));
					} elseif ($type == 2) {
						ob_start();
						include($this->_viewsPath.'form_protocol.php');
						$content = ob_get_clean();
						wp_die($content, __('Redirecting'));
					} else {
						wp_redirect(get_permalink(get_option('eshop_success_page')));
						die();
					}
				} else {
					$_SESSION['eshop_err'] = $err;
				}
			}
		}
	}

	function initWidgets()
	{
		register_widget("EShopYandexMoneyWidget");
	}

	function initContent($content)
	{
		global $post;
		if ($post->ID == get_settings('eshop_cart_page')) {
			$items = $_SESSION['eshop_cart'];
			$totalPrice = 0;
			$totalQuant = 0;
			if (!empty($items)) foreach ($items as &$item) {
				$item['total_price'] = round($item['price'] * $item['quant'], 2);
				$totalQuant += $item['quant'];
				$totalPrice += $item['total_price'];
			}
			unset($item);
			$nonce = wp_create_nonce(plugin_basename(__FILE__));
			$err = $_SESSION['eshop_err'];
			unset($_SESSION['eshop_err']);
			ob_start();
			include($this->_viewsPath.'cart.php');
			$content = ob_get_clean();
		} elseif ($post->ID == get_settings('eshop_index_page')) {
			$cats = get_posts(array('post_type' => 'eshop_cat', 'post_parent' => 0, 'order' => 'ASC'));
			ob_start();
			include($this->_viewsPath.'index.php');
			$content = ob_get_clean();
		} elseif ($post->post_type == 'eshop_cat') {
			$subcats = get_posts(array('post_type' => 'eshop_cat', 'post_parent' => $post->ID, 'order' => 'ASC'));
			$items = get_posts(array('post_type' => 'eshop_item', 'post_parent' => $post->ID, 'order' => 'ASC'));
			if (!empty($items)) foreach ($items as &$item) {
				$item->image = $this->getFeaturedImage($item->ID);
			}
			unset($item);
			ob_start();
			include($this->_viewsPath.'cat.php');
			$content = ob_get_clean();
		} elseif ($post->post_type == 'eshop_item') {
			ob_start();
			include($this->_viewsPath.'item.php');
			$content = ob_get_clean();
		}
		return $content;
	}

	// categories

	function initCategories()
	{
		register_post_type('eshop_cat', array(
			'labels' => array(
				'name' => __('EShop categories', 'eshop'),
				'singular_name' => __('EShop category', 'eshop'),
				'add_new' => __('Add category', 'eshop'),
				'add_new_item' => __('Add new category', 'eshop'),
				'edit_item' => __('Edit category', 'eshop'),
				'new_item' => __('New category', 'eshop'),
				'view_item' => __('View category', 'eshop'),
				'search_items' => __('Find a category', 'eshop'),
				'not_found' => __('No category found', 'eshop'),
				'parent_item_colon' => '',
				'menu_name' => __('EShop categories', 'eshop')
			),
			'show_in_menu' => false,
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'eshop/category'),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => true,
			'supports' => array('title', 'editor', 'author', 'page-attributes')
		));

		add_filter('manage_eshop_cat_posts_columns', array($this, 'initCategoryChangeColumns'));
		add_action('manage_eshop_cat_posts_custom_column', array($this, 'initCategoryCustomColumns'), 10, 2);
	}

	function initCategoryChangeColumns($cols)
	{
		$cols = array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Title', 'eshop'),
			'items' => __('Items', 'eshop'),
			'author' => __('Author', 'eshop'),
			'date' => __('Date', 'eshop')
		);
		return $cols;
	}

	function initCategoryCustomColumns($column, $postId)
	{
		global $wpdb;
		switch ($column) {
			case 'items':
				$row = $wpdb->get_row('SELECT COUNT(ID) AS counter
							FROM '.$wpdb->posts.'
							WHERE post_parent = '.$postId.'
							AND post_type = "eshop_item"
							ORDER BY menu_order ASC');
				if ($row->counter > 0) {
					echo '<a href="?post_type=eshop_item&category_id='.$postId.'">'.$row->counter.'</a>';
				} else {
					echo $row->counter;
				}
				break;
		}
	}

	// items

	function initItems()
	{
		register_post_type('eshop_item', array(
			'labels' => array(
				'name' => __('EShop items', 'eshop'),
				'singular_name' => __('EShop item', 'eshop'),
				'add_new' => __('Add item', 'eshop'),
				'add_new_item' => __('Add new item', 'eshop'),
				'edit_item' => __('Edit item', 'eshop'),
				'new_item' => __('New item', 'eshop'),
				'view_item' => __('View item', 'eshop'),
				'search_items' => __('Find an item', 'eshop'),
				'not_found' => __('No item found', 'eshop'),
				'parent_item_colon' => '',
				'menu_name' => __('EShop items', 'eshop')
			),
			'show_in_menu' => false,
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'eshop/item'),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'supports' => array('title', 'editor', 'thumbnail', 'author', 'page-attributes', 'comments'),
			'register_meta_box_cb' => array($this, 'initItemCustomFields')
		));

		add_action('save_post', array($this, 'saveItemCategoryMetaBox'), 1, 2);
		add_action('save_post', array($this, 'saveItemPriceMetaBox'), 1, 2);

		add_filter('manage_eshop_item_posts_columns', array($this, 'initItemChangeColumns'));
		add_action('manage_eshop_item_posts_custom_column', array($this, 'initItemCustomColumns'), 10, 2);

		add_filter('restrict_manage_posts', array($this, 'initItemCategoryFilter'));
		add_filter('request', array($this, 'initItemCategoryRequest'));
	}

	function initItemChangeColumns($cols)
	{
		$cols = array(
			'cb' => '<input type="checkbox" />',
			'image' => __('Image', 'eshop'),
			'title' => __('Title', 'eshop'),
			'price' => __('Price', 'eshop'),
			'category' => __('Category', 'eshop'),
			'author' => __('Author', 'eshop'),
			'date' => __('Date', 'eshop')
		);
		return $cols;
	}

	function initItemCustomColumns($column, $postId)
	{
		global $post;
		switch ($column) {
			case 'image':
				$img = $this->getFeaturedImage($postId);
				if ($img) {
					echo '<img src="'.$img.'" width="55" height="55" />';
				}
				break;
			case 'price':
				echo get_post_meta($postId, '_price', TRUE).' '.get_option('eshop_currency');
				break;
			case 'category':
				$cat = get_post($post->post_parent);
				echo $cat->post_title;
				break;
		}
	}

	function initItemCategoryRequest($request)
	{
		global $post_type;

		if (!is_admin() || $post_type != 'eshop_item') return $request;

		$categoryId = isset($_GET['category_id'])?(int)$_GET['category_id']:0;
		if ($categoryId) {
			$request['post_parent'] = $categoryId;
		}

		return $request;
	}

	function initItemCategoryFilter()
	{
		global $post_type;

		if (!is_admin() || $post_type != 'eshop_item') return;

		$cats = get_posts(array('post_type' => 'eshop_cat', 'order' => 'ASC'));
		$struct = array();
		if (!empty($cats)) foreach ($cats as $cat) {
			$struct[(int) $cat->post_parent][] = $cat;
		}

		function printList($struct, $parentId = 0, $level = 0)
		{
			if (!empty($struct[$parentId])) foreach ($struct[$parentId] as $item) {
					echo '<option value="'.$item->ID.'"';
					if ($item->ID == $_GET['category_id']) {
						echo ' selected';
					}
					echo '>'.str_repeat('&mdash;', $level).'&nbsp;'.$item->post_title.'</option>';
					printList($struct, $item->ID, $level + 1);
				}
		}

		echo '<select name="category_id">
				<option value="">'.__('All', 'eshop').'</option>';
		printList($struct, 0);
		echo '</select>';
	}

	function initItemCustomFields()
	{
		add_meta_box(
			'item_category_meta_box',
			__('Category', 'eshop'),
			array($this, 'showItemCategoryMetaBox'),
			'eshop_item',
			'normal',
			'high'
		);
		add_meta_box(
			'item_price_meta_box',
			__('Price', 'eshop'),
			array($this, 'showItemPriceMetaBox'),
			'eshop_item',
			'normal',
			'high'
		);
	}

	function showItemPriceMetaBox()
	{
		global $post;

		echo '<input type="hidden" name="_price_nonce" id="_price_nonce" value="' .wp_create_nonce(plugin_basename(__FILE__)).'" />';
		echo '<input type="text" name="_price" value="'.get_post_meta($post->ID, '_price', true).'" /> '.get_option('eshop_currency');
	}

	function saveItemPriceMetaBox($postId, $post)
	{
		if ($post->post_type != 'eshop_item') {
			return $postId;
		}
		if (!wp_verify_nonce($_POST['_price_nonce'], plugin_basename(__FILE__))) {
			return $postId;
		}
		if (!current_user_can('edit_post', $postId)) {
			return $postId;
		}

		$key = '_price';

		$old = get_post_meta($postId, $key, true);
		$new = (float)str_replace(',', '.', $_POST['_price']);
		if ($new && $new != $old) {
			update_post_meta($postId, $key, $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($postId, $key, $old);
		}
	}

	function showItemCategoryMetaBox()
	{
		global $post;

		echo '<input type="hidden" name="_category_nonce" id="_category_nonce" value="' .wp_create_nonce(plugin_basename(__FILE__)).'" />';

		$cats = get_posts(array('post_type' => 'eshop_cat', 'order' => 'ASC'));
		$struct = array();
		if (!empty($cats)) foreach ($cats as $cat) {
			$struct[(int)$cat->post_parent][] = $cat;
		}

		function printList($struct, $parentId = 0, $level = 0) {
			global $post;
			if (!empty($struct[$parentId])) foreach ($struct[$parentId] as $item) {
				echo '<option value="'.$item->ID.'"';
				if ($item->ID == $post->post_parent) {
					echo ' selected';
				}
				echo '>'.str_repeat('&mdash;', $level).'&nbsp;'.$item->post_title.'</option>';
				printList($struct, $item->ID, $level + 1);
			}
		}

		echo '<select name="_category">';
		printList($struct, 0);
		echo '</select>';
	}

	function saveItemCategoryMetaBox($postId, $post)
	{
		if ($post->post_type != 'eshop_item') {
			return $postId;
		}
		if (!wp_verify_nonce($_POST['_category_nonce'], plugin_basename(__FILE__))) {
			return $postId;
		}
		if (!current_user_can('edit_post', $postId)) {
			return $postId;
		}
		$category = (int)$_POST['_category'];
		if (!$category) {
			return $postId;
		}

		remove_action('save_post', array($this, 'saveItemCategoryMetaBox'), 1);

		$update = array(
			'ID' => $postId,
			'post_parent' => $category
		);
		wp_update_post($update);
	}

	function adminRegisterSettings ()
	{
		register_setting('eshop_settings', 'eshop_test_mode');
		register_setting('eshop_settings', 'eshop_type');

		register_setting('eshop_settings', 'eshop_account');
		register_setting('eshop_settings', 'eshop_secret');
		register_setting('eshop_settings', 'eshop_payment_descr');
		register_setting('eshop_settings', 'eshop_st_payment_type_pc');
		register_setting('eshop_settings', 'eshop_st_payment_type_ac');

		register_setting('eshop_settings', 'eshop_sid');
		register_setting('eshop_settings', 'eshop_scid');
		register_setting('eshop_settings', 'eshop_password');
		register_setting('eshop_settings', 'eshop_success_page');
		register_setting('eshop_settings', 'eshop_fail_page');
		register_setting('eshop_settings', 'eshop_pr_payment_type_pc');
		register_setting('eshop_settings', 'eshop_pr_payment_type_ac');
		register_setting('eshop_settings', 'eshop_pr_payment_type_gp');
		register_setting('eshop_settings', 'eshop_pr_payment_type_mc');
		register_setting('eshop_settings', 'eshop_pr_payment_type_wm');
		register_setting('eshop_settings', 'eshop_pr_payment_type_ab');
		register_setting('eshop_settings', 'eshop_pr_payment_type_sb');
		register_setting('eshop_settings', 'eshop_pr_payment_type_ma');
		register_setting('eshop_settings', 'eshop_pr_payment_type_pb');

		register_setting('eshop_settings', 'eshop_cart_page');
		register_setting('eshop_settings', 'eshop_index_page');
		register_setting('eshop_settings', 'eshop_orders_email');
		register_setting('eshop_settings', 'eshop_currency');
		register_setting('eshop_settings', 'eshop_company');
	}

	function adminMenu()
	{
		add_menu_page(
			__('EShop-YandexMoney', 'eshop'),
			__('EShop-YandexMoney', 'eshop'),
			'manage_options',
			'eshop',
			array(&$this, 'adminOrders')
		);
		add_submenu_page(
			'eshop',
			__('EShop-YandexMoney Orders', 'eshop'),
			__('Orders', 'eshop'),
			'manage_options',
			'eshop',
			array(&$this, 'adminOrders')
		);
		add_submenu_page(
			'eshop',
			__('EShop-YandexMoney Categories', 'eshop'),
			__('Categories', 'eshop'),
			'manage_options',
			'edit.php?post_type=eshop_cat'
		);
		add_submenu_page(
			'eshop',
			__('EShop-YandexMoney Items', 'eshop'),
			__('Items', 'eshop'),
			'manage_options',
			'edit.php?post_type=eshop_item'
		);
		add_submenu_page(
			'eshop',
			__('EShop-YandexMoney Settings', 'eshop'),
			__('Settings', 'eshop'),
			'manage_options',
			'eshop_settings',
			array(&$this, 'adminSettings')
		);
		add_action('update_option_eshop_sid', array( $this, 'after_update_setting' ));
		add_action('update_option_eshop_type', array( $this, 'after_update_setting' ));
	}
	
	function after_update_setting($one){
		new yamoney_statistics();
	}
	
	function adminOrders()
	{
		global $wpdb;

		$limit = 10;
		$currentPage = isset($_GET['paged'])?(int)$_GET['paged']:1;
		if ($currentPage < 1) $currentPage = 1;
		$startFrom = ($currentPage - 1) * $limit;
		$ordersCount = $wpdb->get_var('SELECT COUNT(ID) FROM '.$wpdb->prefix.'eshop_orders');
		$totalPages = ceil($ordersCount / $limit);

		$orders = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'eshop_orders ORDER BY ID DESC LIMIT '.$startFrom.','.$limit);
		if (count($orders) > 0) foreach ($orders as &$order) {
			$order->items = json_decode($order->items);
		}
		unset($order);

		$args = array(
			'base' => '%_%',
			'format' => '?page=eshop&paged=%#%',
			'total' => $totalPages,
			'current' => $currentPage,
			'show_all' => false,
			'end_size' => 1,
			'mid_size' => 2,
			'prev_next' => true,
			'prev_text' => __('«'),
			'next_text' => __('»'),
			'type' => 'plain',
			'add_args' => false,
			'add_fragment' => ''
		);
		$pagination = paginate_links( $args );

		require($this->_viewsPath.'admin_orders.php');
	}
	
	
	
	function adminSettings()
	{
		require($this->_viewsPath.'admin_settings.php');
	}

	function getFeaturedImage($postId)
	{
		$thumbId = get_post_thumbnail_id($postId);
		if ($thumbId) {
			$img = wp_get_attachment_image_src($thumbId, 'thumbnail');
			return $img[0];
		}
	}

}

class EShopYandexMoneyWidget extends WP_Widget
{
	function __construct()
	{
		parent::__construct('eshop_widget', __('EShop Cart', 'eshop'));
	}

	public function widget($args, $instance)
	{
		echo $args['before_widget'];
		echo $args['before_title'].$args['widget_name'].$args['after_title'];
		if ($_SESSION['eshop_cart']) {
			foreach ($_SESSION['eshop_cart'] as $item) {
				echo '<p>'.$item['name'].' <span>'.$item['quant'].'</span></p>';
			}
			echo '<p><a href="'.get_permalink(get_settings('eshop_cart_page')).'">'.__('Go to cart', 'eshop').'</a></p>';
		} else {
			echo '<p>'.__('Cart is empty', 'eshop').'</p>';
		}
		echo $args['after_widget'];
	}
}

$eShopYandexMoney = new EShopYandexMoney();

?>
