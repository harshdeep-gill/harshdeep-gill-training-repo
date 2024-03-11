/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
} from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { Icon, PanelBody, SelectControl } from '@wordpress/components';

/**
 * Styles.
 */
import '../../../front-end/components/icon-badge/style.scss';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ColorPaletteControl } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/icon-badge';

/**
 * Background colors.
 */
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
	{ name: __( 'White', 'qrk' ), color: '#fff', slug: 'white' },
	{ name: __( 'Yellow', 'qrk' ), color: '#fdb52b', slug: 'yellow' },
	{ name: __( 'Dark blue', 'qrk' ), color: '#2a5f8c', slug: 'dark-blue' },
	{ name: __( 'Blue', 'qrk' ), color: '#4c8bbf', slug: 'blue' },
	{ name: __( 'Magenta', 'qrk' ), color: '#a26792', slug: 'magenta' },
	{ name: __( 'Gray 90', 'qrk' ), color: '#383d49', slug: 'gray-90' },
	{ name: __( 'Gray 80', 'qrk' ), color: '#454c5b', slug: 'gray-80' },
	{ name: __( 'Gray 70', 'qrk' ), color: '#535b6d', slug: 'gray-70' },
	{ name: __( 'Gray 60', 'qrk' ), color: '#6c768e', slug: 'gray-60' },
	{ name: __( 'Gray 50', 'qrk' ), color: '#868fa3', slug: 'gray-50' },
	{ name: __( 'Gray 40', 'qrk' ), color: '#a8aebd', slug: 'gray-40' },
	{ name: __( 'Gray 30', 'qrk' ), color: '#c9cdd6', slug: 'gray-30' },
	{ name: __( 'Gray 20', 'qrk' ), color: '#dadee5', slug: 'gray-20' },
	{ name: __( 'Gray 10', 'qrk' ), color: '#eceef2', slug: 'gray-10' },
	{ name: __( 'Gray 5', 'qrk' ), color: '#f5f7fb', slug: 'gray-5' },
	{ name: __( 'Success 100', 'qrk' ), color: '#3a735d', slug: 'success-100' },
	{ name: __( 'Success 50', 'qrk' ), color: '#5bb291', slug: 'success-50' },
	{ name: __( 'Success 10', 'qrk' ), color: '#e6f2ee', slug: 'success-10' },
	{ name: __( 'Attention 100', 'qrk' ), color: '#c77413', slug: 'attention-100' },
	{ name: __( 'Attention 50', 'qrk' ), color: '#f29b34', slug: 'attention-50' },
	{ name: __( 'Attention 10', 'qrk' ), color: '#ffe5c7', slug: 'attention-10' },
	{ name: __( 'Error 100', 'qrk' ), color: '#bf483b', slug: 'error-100' },
	{ name: __( 'Error 50', 'qrk' ), color: '#df5748', slug: 'error-50' },
	{ name: __( 'Error 10', 'qrk' ), color: '#fdddd9', slug: 'error-10' },
	{ name: __( 'Information 100', 'qrk' ), color: '#4b5059', slug: 'information-100' },
	{ name: __( 'Information 50', 'qrk' ), color: '#808999', slug: 'information-50' },
	{ name: __( 'Information 10', 'qrk' ), color: '#fafbff', slug: 'information-10' },
];

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Icon Badge', 'qrk' ),
	description: __( 'Display an icon with some text in a badge.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'icon', 'qrk' ),
		__( 'badge', 'qrk' ),
	],
	attributes: {
		icon: {
			type: 'string',
			default: '',
		},
		color: {
			type: 'string',
			default: '',
		},
		text: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: true,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'icon-badge', attributes.color ? `has-background--${ attributes.color }` : 'has-background--attention-100' ),
		} );

		// Prepare icon.
		let selectedIcon: any = '';

		// Set icon.
		if ( attributes.icon && '' !== attributes.icon ) {
			// Kebab-case to camel-case.
			const iconName: string = attributes.icon.replace( /-([a-z])/g, ( _: any, group:string ) => group.toUpperCase() );
			selectedIcon = icons[ iconName ] ?? '';
		}

		// Fallback icon.
		if ( ! selectedIcon || '' === selectedIcon ) {
			selectedIcon = <Icon icon="no" />;
		}

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Icon Badge Options', 'qrk' ) }>
						<ColorPaletteControl
							label={ __( 'Background Color', 'qrk' ) }
							help={ __( 'Select the background color.', 'qrk' ) }
							value={ colors.find( ( color ) => color.slug === attributes.color )?.color }
							colors={ colors }
							onChange={ ( color: {
								color: string;
								slug: string;
							} ): void => {
								// Set the color attribute.
								if ( color.slug ) {
									setAttributes( { color: color.slug } );
								}
							} }
						/>
						<SelectControl
							label={ __( 'Icon', 'qrk' ) }
							help={ __( 'Select the icon.', 'qrk' ) }
							value={ attributes.icon }
							options={ [
								{ label: __( 'Select Icon…', 'qrk' ), value: '' },
								{ label: __( 'Alert', 'qrk' ), value: 'alert' },
							] }
							onChange={ ( icon: string ) => setAttributes( { icon } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps } >
					<span className="icon-badge__icon">
						{ selectedIcon }
					</span>
					<RichText
						tagName="span"
						className={ classnames( 'icon-bade__description' ) }
						placeholder={ __( 'Write badge description…', 'qrk' ) }
						value={ attributes.text }
						onChange={ ( text: string ) => setAttributes( { text } ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
