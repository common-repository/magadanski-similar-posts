const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const wplib = [
	'blocks',
	'components',
	'date',
	'editor',
	'element',
	'i18n',
	'utils',
	'data',
	'server-side-renderer'
];

module.exports = {
	entry: [
		path.resolve(__dirname, 'blocks/src/index.js'),
		path.resolve(__dirname, 'blocks/src/edit.scss')
	],
	
	output: {
		path: path.resolve(__dirname, 'blocks/dist'),
		filename: 'index.js'
	},
	
	externals: wplib.reduce((externals, lib) => {
		externals[`@wordpress/${lib}`] = {
			window: ['wp', lib],
		};
		
		return externals;
	}, {
		'react': 'React',
		'react-dom': 'ReactDOM',
	}),
	
	module: {
		rules: [
			{
				exclude: /node_modules/,
				test: /\.jsx?$/,
				loader: 'babel-loader',
				options: {
					presets: ['@babel/preset-env'],
					plugins: ['@babel/plugin-transform-react-jsx'],
					compact: true
				}
			},
			{
				exclude: /node_modules/,
				test: /\.scss$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader
					},
					{
						loader: 'css-loader'
					},
					{
						loader: 'sass-loader'
					}
				]
			}
		]
	},
	
	plugins: [
		new MiniCssExtractPlugin({
			filename: 'edit.css'
		})
	]
};