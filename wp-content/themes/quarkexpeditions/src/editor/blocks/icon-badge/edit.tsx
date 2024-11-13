/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import icons from '../icons';
import { getAllBackgroundColors } from '../utils';

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
 * Background colors.
 */
export const colors = getAllBackgroundColors();

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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
				{ '' !== selectedIcon && <span className="icon-badge__icon">
					{ selectedIcon }
				</span>
				}
				<RichText
					tagName="span"
					className={ classnames( 'icon-badge__description' ) }
					placeholder={ __( 'Write badge description…', 'qrk' ) }
					value={ attributes.text }
					onChange={ ( text: string ) => setAttributes( { text } ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}
