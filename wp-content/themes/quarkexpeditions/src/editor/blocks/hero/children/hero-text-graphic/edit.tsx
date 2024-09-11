/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const {
	ImageControl,
	Img,
} = gumponents.components;

/**
 * Styles.
 */
import './editor.scss';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	/**
	 * Handle image change.
	 *
	 * @param {Object} image    Image.
	 * @param {number} image.id Image ID.
	 */

	// Set the block properties.
	const blockProps = useBlockProps( {
		className: classnames( className, 'hero__text-graphic' ),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Description Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="large"
						help={ __( 'Choose an image', 'qrk' ) }
						onChange={ ( image: object ) => setAttributes( { image } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } >
				<figure className="hero__text-graphic-wrap">
					<Img
						value={ attributes.image }
					/>
				</figure>
			</div>
		</>
	);
}
