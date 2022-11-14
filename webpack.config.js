const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require('path');
// const CopyPlugin = require("copy-webpack-plugin");

// Configuration object.
const config = {
  ...defaultConfig,
	entry: {
    // '../blocks/songs/songs': './src/blocks/songs/songs.js',
    // '../blocks/events/events': './src/blocks/events/events.js',
    '../includes/includes': './src/includes/includes.js',
    '../public/public': './src/public/public.js',
    '../admin/admin': './src/admin/admin.js',
	},
	output: {
    filename: '[name].js',
    // Specify the path to the JS files.
    path: path.resolve( __dirname, 'build' )
	},
}

// Export the config object.
module.exports = config;
