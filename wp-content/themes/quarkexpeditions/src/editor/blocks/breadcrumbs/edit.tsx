/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Icon, PanelBody } from '@wordpress/components';

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
		className: classnames(
			className,
			'breadcrumbs',
			attributes.textColor === 'black' ? 'breadcrumbs--light' : 'breadcrumbs--dark'
		),
	} );

	// Get the chevron icon
	const cheveronIcon = icons.chevronLeft ?? <Icon icon="no" />;

	// Text colors.
	const colors: { [key: string]: string }[] = [
		{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
		{ name: __( 'White', 'qrk' ), color: '#ffffff', slug: 'white' },
	];

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Breadcrumb Options', 'qrk' ) }>
					<ColorPaletteControl
						label={ __( 'Color', 'qrk' ) }
						help={ __( 'Select the text color for the Breadcrumb', 'qrk' ) }
						value={ colors.find( ( color ) => color.slug === attributes.textColor )?.color }
						colors={ colors.filter( ( color ) => [ 'white', 'black' ].includes( color.slug ) ) }
						onChange={ ( textColor: {
							color: string;
							slug: string;
						} ): void => {
							// Set the background color attribute.
							if ( textColor.slug && [ 'white', 'black' ].includes( textColor.slug ) ) {
								setAttributes( { textColor: textColor.slug } );
							}
						} }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className="breadcrumbs__breadcrumb">
					<span className="breadcrumbs__breadcrumb-separator">
						{ cheveronIcon }
					</span>
					<span className="breadcrumbs__breadcrumb-title">Home</span>
				</div>
				<div className="breadcrumbs__breadcrumb">
					<span className="breadcrumbs__breadcrumb-separator">
						{ cheveronIcon }
					</span>
					<span className="breadcrumbs__breadcrumb-title">Parent Page</span>
				</div>
				<div className="breadcrumbs__breadcrumb">
					<span className="breadcrumbs__breadcrumb-separator">
						{ cheveronIcon }
					</span>
					<span className="breadcrumbs__breadcrumb-title">Child Page</span>
				</div>
			</div>
		</>
	);
}
