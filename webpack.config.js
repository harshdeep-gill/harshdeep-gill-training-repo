/**
 * External dependencies.
 */

// External dependencies.
const path = require( 'path' );
const fg = require( 'fast-glob' );
const { exec } = require( 'child_process' );
const TerserPlugin = require( 'terser-webpack-plugin' );
const WebpackNotifierPlugin = require( 'webpack-notifier' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );
const WebpackWatchedGlobEntries = require( 'webpack-watched-glob-entries-plugin' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

// Theme path.
const themePath = `./wp-content/themes/quarkexpeditions`;

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
			{
				apply: ( compiler ) => {
					/**
					 * After compile, add PHP files to dependencies.
					 * @see <https://github.com/webpack/webpack-dev-server/issues/34#issuecomment-47420992>.
					 */
					compiler.hooks.afterCompile.tap( 'BlocksManifestPlugin', ( compilation ) => {
						// Get PHP files from blocks.
						const phpFiles = fg.sync( path.resolve( __dirname, `${ themePath }/src/editor/blocks/**/*.php` ) );

						// Add PHP files to dependencies.
						if ( Array.isArray( compilation.fileDependencies ) ) {
							phpFiles.map( ( file ) => compilation.fileDependencies.push( file ) );
						} else {
							phpFiles.map( ( file ) => compilation.fileDependencies.add( file ) );
						}
					} );

					// After emit, generate blocks manifest.
					compiler.hooks.afterEmit.tap( 'BlocksManifestPlugin', async () => {
						// Get PHP script to generate blocks manifest.
						const blocksInfoExtractor = path.resolve( __dirname, '.bin/block-manifest-generator.php' );

						// Run PHP script.
						exec( `php ${ blocksInfoExtractor }`, ( error, stdout, stderr ) => {
							// If there is an error.
							if ( error ) {
								// eslint-disable-next-line no-console
								console.error( `[BlocksManifestPlugin] \n ${ error }` );

								// Just return.
								return;
							}

							// If there is an error.
							if ( stderr ) {
								// eslint-disable-next-line no-console
								console.error( `[BlocksManifestPlugin] \n ${ stderr }` );
								// Just return.
								return;
							}

							// eslint-disable-next-line no-console
							console.log( `[BlocksManifestPlugin] \n ${ stdout }` );
						} );
					} );
				},
			},
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
			TPTabsElement: `${ themePath }/src/vendor/tp-tabs.js`,
			GLightbox: `${ themePath }/src/vendor/glightbox.js`,
			TPLightboxElement: `${ themePath }/src/vendor/tp-lightbox.js`,
			TPAccordionItemElement: `${ themePath }/src/vendor/tp-accordion.js`,
			TPMultiSelectElement: `${ themePath }/src/vendor/tp-multi-select.js`,
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
