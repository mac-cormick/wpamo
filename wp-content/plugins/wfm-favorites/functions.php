<?php

function wfm_favorites_content($content) {
	if (!is_single() || !is_user_logged_in()) return $content;
	$loader_img_path = plugins_url('/img/222.gif', __FILE__);

	global $post;
	if (wfm_is_favorites($post->ID)) {
		return '<p class="wfm-favorites-link"><span class="wfm-favorites-hidden"><img src="' . $loader_img_path . '" alt=""></span><a href="#" data-action="del">Удалить из Избранного</a></p>' . $content;
	}

	return '<p class="wfm-favorites-link"><span class="wfm-favorites-hidden"><img src="' . $loader_img_path . '" alt=""></span><a href="#" data-action="add">Добавить в Избранное</a></p>' . $content;
}

function wfm_favorites_scripts() {
	if (!is_single() || !is_user_logged_in()) return;

	wp_enqueue_script(
		'wfm-favorites-scripts',
		plugins_url('/js/wfm-favorites-scripts.js', __FILE__),
		['jquery'],
		null,
		true
	);

	wp_enqueue_style(
		'wfm-favorites-scripts',
		plugins_url('/css/wfm-favorites-style.css', __FILE__)
	);

	global $post;
	wp_localize_script(
		'wfm-favorites-scripts',
		'wfmFavorites',
		[
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('wfm-favorites'),
			'postID' => $post->ID
		]
	);
}

function wp_ajax_wfm_add() {
	if (!wp_verify_nonce($_POST['security'], 'wfm-favorites')) {
		wp_die('Error security');
	}

	$post_id = (int)$_POST['postID'];
	$user = wp_get_current_user();

	if (wfm_is_favorites($post_id)) wp_die();

	if (add_user_meta($user->ID, 'wfm-favorites', $post_id)) {
		wp_die('Добавлено!');
	}

	wp_die('Ошибка добавления!');
}

function wp_ajax_wfm_del() {
	if (!wp_verify_nonce($_POST['security'], 'wfm-favorites')) {
		wp_die('Error security');
	}

	$post_id = (int)$_POST['postID'];
	$user = wp_get_current_user();

	if (!wfm_is_favorites($post_id)) wp_die();

	if (delete_user_meta($user->ID, 'wfm-favorites', $post_id)) {
		wp_die('Удалено!');
	}

	wp_die('Ошибка удаления!');
}

function wfm_is_favorites($post_id) {
	$user = wp_get_current_user();
	$favorites = get_user_meta($user->ID, 'wfm-favorites');
	return in_array($post_id, $favorites);
}

function wfm_favorites_dashboard_widget() {
	wp_add_dashboard_widget(
		'wfm_favorites_dashboard',
		'Ваш список избранного',
		'wfm_show_dashboard_widget'
	);
}

function wfm_show_dashboard_widget() {
	$user = wp_get_current_user();
	$favorites = get_user_meta($user->ID, 'wfm-favorites');

	if (!$favorites) {
		echo 'Список пуст!';
		return;
	}

	echo '<ul>';
	foreach ($favorites as $favorite) {
		echo '<li><a href="' . get_permalink($favorite) . '" target="_blank">' . get_the_title($favorite) . '</a></li>';
	}
	echo '</ul>';
}