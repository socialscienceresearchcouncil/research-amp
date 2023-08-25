<?php

spl_autoload_register(
	function ( $class_name ) {
		$prefix   = 'SSRC\\RAMP\\';
		$base_dir = __DIR__ . '/src/';

		// Does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}

		// Get the relative class name.
		$relative_class = substr( $class_name, $len );

		// Swap directory separators and namespace to create filename.
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		// If the file exists, require it.
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
