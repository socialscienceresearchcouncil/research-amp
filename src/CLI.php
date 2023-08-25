<?php

namespace SSRC\RAMP;

use WP_CLI;

class CLI {
	public function init() {
		WP_CLI::add_command( 'ramp install', '\SSRC\RAMP\Command\Install' );
	}
}
