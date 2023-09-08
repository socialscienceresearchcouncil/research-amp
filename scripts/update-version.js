/*eslint-disable no-console */

const fs = require( 'fs-extra' );

async function updateVersion() {
  try {
    const packageJson = await fs.readJson( 'package.json' );
    const version = packageJson.version;

    if ( ! version ) {
      console.error( 'Could not find version number in package.json' );
      process.exit( 1 );
    }

    // Update the version in the plugin header file.
		const loaderFile = 'loader.php';
    let pluginHeaderFile = await fs.readFile( loaderFile, 'utf8' );
    pluginHeaderFile = pluginHeaderFile.replace( /Version:\s*(.+)/, `Version: ${version}` );
    await fs.writeFile( loaderFile, pluginHeaderFile );

    // Update the PHP constant
    let constantFile = await fs.readFile( loaderFile, 'utf8' );
    constantFile = constantFile.replace(/define\('YOUR_PLUGIN_VERSION', '(.+)'\);/, `define('YOUR_PLUGIN_VERSION', '${version}');`);
    await fs.writeFile('../your-constants-file.php', constantFile);

    console.log(`Version updated to ${version}`);
  } catch (error) {
    console.error(`Error updating version: ${error}`);
  }
}

updateVersion();
