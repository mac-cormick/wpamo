<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'wp_amo');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '#XJ+r}N{bMUcG6];i?,GRR27C#FBVFe?tb?=z`+TGo+%ZiyGa)porA>D>yuNGPfz');
define('SECURE_AUTH_KEY',  'uZDk?@8,nNg(]nWG7?+rN u${xmG#$=BX^Cj$V&d.4nYk+;DnUx<@-] U~-iw8//');
define('LOGGED_IN_KEY',    'jifv[:so:rtv<C`7t)elb{knvft_#T4wI$Elrhczw@QT`;2^^7UJNNzy)Sa|RX?i');
define('NONCE_KEY',        '7[(hoF]Bx._m*v=P2) b6lEA@@,>Dd#W>@k4&PL0Ar%!8q@S$W)jOh^ixq~9-54K');
define('AUTH_SALT',        'IfO;&{C?|6nNDM2[[YvTYPHg{VJWxKaXfc$[4Qnil!{Bcr9#yHDIQv^yUSy?O]ch');
define('SECURE_AUTH_SALT', 'cP4-,8=O;!veSW,EF!G9uNj__UBu;+@>k*eHFQ]iHKoR~g)ef39S#89.2HaAR[aV');
define('LOGGED_IN_SALT',   'h^x>,z{~w[Pef:?wx=b7<c_%ql=&DruP<h<x%D;AV.rn#@VpI/+>ro2A>0w]C:tG');
define('NONCE_SALT',       'OOi;k3$1KnzZ$TL&u`f/S |, 75=:b.7?H}zYC*a4uNskwx8GY8O@NE_O@^ [9*f');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
