
const { execSync } = require('child_process');
const fs = require('fs-extra');

// Directory where the built theme will be placed
const themesDirectory = 'themes';
const themeDirectory = themesDirectory + '/research-amp-theme';

// Remove existing theme files
fs.removeSync( themeDirectory );

// Clone the theme repository
execSync('git clone https://github.com/socialscienceresearchcouncil/research-amp-theme ' + themeDirectory, { stdio: 'inherit' });

// Change directory to the theme repository
process.chdir( themeDirectory );

// Get the latest tagged version
const latestTag = execSync('git describe --abbrev=0 --tags').toString().trim();

// Checkout the latest tagged version
execSync(`git checkout ${latestTag}`, { stdio: 'inherit' });

// Run the theme build process (adjust the build command if needed)
execSync('npm install && npm run build', { stdio: 'inherit' });

console.log('Theme build complete.');
