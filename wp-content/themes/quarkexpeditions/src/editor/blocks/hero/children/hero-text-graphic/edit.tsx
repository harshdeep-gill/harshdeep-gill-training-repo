/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

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
		className: classnames(
			className,
			'hero__text-graphic',
			attributes.imageSize ? `hero__text-graphic--size-${ attributes.imageSize }` : '',
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Hero Text Graphic Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="large"
						help={ __( 'Choose an image', 'qrk' ) }
						onChange={ ( image: object ) => setAttributes( { image } ) }
					/>
					<SelectControl
						label={ __( 'Image Size', 'qrk' ) }
						help={ __( 'Select the image size from these options', 'qrk' ) }
						value={ attributes.imageSize }
						options={ [
							{ label: __( 'Large', 'qrk' ), value: 'large' },
							{ label: __( 'Medium', 'qrk' ), value: 'medium' },
							{ label: __( 'Small', 'qrk' ), value: 'small' },
						] }
						onChange={ ( imageSize: string ) => setAttributes( { imageSize } ) }
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
