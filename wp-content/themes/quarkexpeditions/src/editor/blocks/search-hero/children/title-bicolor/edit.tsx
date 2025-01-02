/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set the block properties.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'search-hero__title',
			'search-hero__title--bicolor',
			attributes.usePromoFont ? 'font-family--promo' : ''
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Title Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Use Promo Font', 'qrk' ) }
						checked={ attributes.usePromoFont }
						onChange={ ( usePromoFont ) => setAttributes( { usePromoFont } ) }
						help={ __( 'Should this text be in the Promo Font?', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<h1 { ...blockProps }>
				<RichText
					tagName="span"
					className="search-hero__title--white-text"
					placeholder={ __( 'Write the white text…', 'qrk' ) }
					value={ attributes.whiteText }
					onChange={ ( whiteText ) => setAttributes( { whiteText } ) }
					allowedFormats={ [] }
				/>
				&nbsp;
				<RichText
					tagName="span"
					className="search-hero__title--yellow-text"
					placeholder={ __( 'Write the yellow text…', 'qrk' ) }
					value={ attributes.yellowText }
					onChange={ ( yellowText ) => setAttributes( { yellowText } ) }
					allowedFormats={ [] }
				/>
			</h1>
		</>
	);
}
