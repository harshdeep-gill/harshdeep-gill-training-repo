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
import { quarkGetBackgroundColors } from '../utils';
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
export const colors = quarkGetBackgroundColors();

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
