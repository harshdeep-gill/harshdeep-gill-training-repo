/**
 * Quark Expeditions Webpack Config.
 */

// External dependencies.
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const WebpackNotifierPlugin = require( 'webpack-notifier' );
const TerserPlugin = require( 'terser-webpack-plugin' );
const WebpackWatchedGlobEntries = require( 'webpack-watched-glob-entries-plugin' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const path = require( 'path' );

// Theme path.
const themePath = `./wp-content/themes/quarkexpeditions`;
const nodeModules = `./node_modules`;

// Config.
module.exports = ( env ) => {
	// Build configuration.
	const buildConfig = {
		entry: {
			global: `${ themePath }/src/front-end/global/index.ts`,
			editor: `${ themePath }/src/editor/index.ts`,
			'editor-custom': `${ themePath }/src/editor/custom.scss`,
		},
		module: {
			rules: [
				{
					test: /\.tsx?$/,
					use: 'ts-loader',
					exclude: /node_modules/,
				},
				{
					test: /\.(sa|sc|c)ss$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {
								url: false,
							},
						},
						{
							loader: 'postcss-loader',
						},
						{
							loader: 'sass-loader',
							options: {
								sassOptions: {
									outputStyle: 'compressed',
								},
							},
						},
					],
				},
			],
		},
		resolve: {
			extensions: [ '*', '.js', '.ts', '.tsx' ],
		},
		output: {
			path: __dirname,
			filename: `${ themePath }/dist/[name].js`,
			publicPath: '/',
		},
		optimization: {
			removeEmptyChunks: true,
			minimize: true,
			minimizer: [ new TerserPlugin( {
				parallel: true,
				terserOptions: {
					format: {
						comments: false,
					},
				},
				extractComments: false,
			} ) ],
		},
		plugins: [
			new RemoveEmptyScriptsPlugin(),
			new MiniCssExtractPlugin( {
				filename: `${ themePath }/dist/[name].css`,
			} ),
			new DependencyExtractionWebpackPlugin( {} ),
		],
		externals: {
			wp: 'wp',
			gumponents: 'gumponents',
			GLightbox: 'GLightbox',
		},
		performance: {
			hints: false,
		},
	};

	// Components config.
	const componentsConfig = {
		...buildConfig,
		entry: WebpackWatchedGlobEntries.getEntries(
			[
				path.resolve( __dirname, `${ themePath }/src/front-end/components/**/index.js` ),
				path.resolve( __dirname, `${ themePath }/src/front-end/components/**/index.ts` ),
				path.resolve( __dirname, `${ themePath }/src/front-end/components/**/style.scss` ),
			],
		),
		output: {
			...buildConfig.output,
			filename: `${ themePath }/dist/components/[name].js`,
		},
		plugins: [
			new RemoveEmptyScriptsPlugin(),
			new MiniCssExtractPlugin( {
				filename: `${ themePath }/dist/components/[name].css`,
			} ),
			new WebpackWatchedGlobEntries(),
		],
	};

	// External libraries config.
	const libConfig = {
		...buildConfig,
		entry: {
			TPSliderElement: `${ themePath }/src/vendor/tp-slider.js`,
			GLightbox: `${ themePath }/src/vendor/glightbox.js`,
		},
		output: {
			...buildConfig.output,
			filename: ( { chunk: { name } } ) => `${ themePath }/dist/vendor/${ name.toLowerCase() }.js`,
			library: {
				type: 'window',
				name: '[name]',
				export: 'default',
			},
		},
		plugins: [
			new MiniCssExtractPlugin( {
				filename: ( { chunk: { name } } ) => `${ themePath }/dist/vendor/${ name.toLowerCase() }.css`,
			} ),
		],
	};

	// Development environment.
	if ( 'development' in env ) {
		buildConfig.plugins.push( new WebpackNotifierPlugin( {
			title: 'Build',
			alwaysNotify: true,
		} ) );
		componentsConfig.plugins.push( new WebpackNotifierPlugin( {
			title: 'Build',
			alwaysNotify: true,
		} ) );
		buildConfig.devtool = 'source-map';
		componentsConfig.devtool = 'source-map';
	}

	// Return combined config.
	return [ buildConfig, componentsConfig, libConfig ];
};
