<?php

namespace SSRC\RAMP\Zotero;

class Admin {
	public static function init() {
		add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
	}

	public static function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=ssrc_citation',
			__( 'Zotero Settings', 'ramp' ),
			__( 'Zotero Settings', 'ramp' ),
			'manage_options',
			'zotero-settings',
			[ __CLASS__, 'admin_page_cb' ]
		);
	}

	public static function admin_page_cb() {

		echo 'ye';
	}

	public static function print_templates() {
		?>

		<script type="text/html" id="tmpl-input">
			<p>Hey!</p>
			<p>{{data.name}}</p>
		</script>

		<?php
	}


}
