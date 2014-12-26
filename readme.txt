=== Js Css Include Manager ===
Contributors: gqevu6bsiz
Donate link: http://gqevu6bsiz.chicappa.jp/please-donation/?utm_source=wporg&utm_medium=donate&utm_content=jcim&utm_campaign=1_4_2
Tags: js, javascript, css, include, manage, admin, front, site
Requires at least: 3.6.1
Tested up to: 4.1
Stable tag: 1.4.2
License: GPL2

This plug-in is a will clean the file management. You can only manage the screen. You can also only site the screen.

== Description ==

This plugin allows you to include extra JavaScript or CSS files in your WordPress page.

You can:

* Include as many as you like.
* Choose whether files are to be included on the front-end or the back-end.
* Choose where to include them in the header or the footer.
* Choose conditions to use for inclusion (logged-in users only, admin users only, front page only).
* Choose conditions of you can add.

Please if you want to add conditions.
For example add filter:
`
add_filter( 'jcim_condition' , 'my_conditions' );
function my_conditions( ) {
	$conditions = array(
		array(
			"code" => "current_user_can",
			"val" => "edit_themes",
			"desc" => "Only edit theme role have user",
			"help_link" => "http://codex.wordpress.org/Function_Reference/current_user_can"
		),
		array(
			"code" => "is_admin",
			"val" => "",
			"desc" => "Only admin screen",
			"help_link" => ""
		),
	);
	return $conditions;
}
`

== Installation ==

1. Upload the full directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to `WP-Admin -> Settings -> Js Css Include Manager` to configure the plugin.

== Frequently Asked Questions ==

= A question that someone might have =

= What about foo bar? =

== Screenshots ==

1. Settings Interface
2. Configuration Example
3. Conditions after added of settins interface

== Changelog ==

= 1.4.2 =
* Fixed: Get the current user group.

= 1.4.1 =
* Fixed: Small bug fixes.
* Fixed: Remove useless globals.

= 1.4 =
* Added: Support to Multisite.
* Added: Allows the change of setting user role.
* Updated: Organize sorce files.
* Updated: Delete how to update in ajax.
* Updated: Strinct condition check.

= 1.3.3.1 =
* Fixed: Data update way.

= 1.3.3 =
* Updated: Change the source code.
* Updated: Data save way of settings.
* Updated: Change way to edit of settings.
* Updated: Compatible for WP 3.8.
* Updated: Screen shots.
* Added: Filter for condition of settings.

= 1.3.2 =
* Support on SSL.

= 1.3.1.2 alpha =
* Fixed bug : Active themes directory location settings.
* Added field to data identification for backwards compatibility.

= 1.3.1.1 =
* Translate fix : Japanese.
* Checked Compatibility.
* Added Information of plugin.

= 1.3.1 =
Added a confirmation of Nonce field.

= 1.3 =
Adding capability to add file based on conditions (e.g. user is logged in, user is an admin, etc.).

= 1.2.1 =
Bugfix: Action footer bug fixed.

= 1.2 =
Change the notation of the donation.

= 1.1 =
Change Layout.
Fixed a bug that does not appear in the footer.

= 1.0 =
This is the initial release.

== Upgrade Notice ==

= 1.0 =

== 日本語でのご説明 ==

このプラグインは、よくごちゃごちゃになってしまうjavascriptファイルの読み込みとcssファイルの読み込みを、
管理画面だけ、またはサイトだけに読み込むファイルを、１箇所で管理するためのプラグインです。
サイトで何のファイルを読み込んでいるのか、すっきり。
