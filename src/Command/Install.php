<?php

namespace SSRC\RAMP\Command;

use \WP_CLI_Command;

class Install extends WP_CLI_Command {
	public function __invoke( $args, $assoc_args ) {
		$installer = new \SSRC\RAMP\Install();

		$installer->install();
	}
}
