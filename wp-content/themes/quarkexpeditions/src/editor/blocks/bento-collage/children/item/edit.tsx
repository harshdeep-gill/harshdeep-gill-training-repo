/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img, LinkControl } = gumponents.components;

/**
 * Edit Component.
 *
 * @param {string}   props               Props.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'bento-collage__card', 'color-context--dark', 'bento-collage__card--' + attributes.size ),
	} );

	// Content classes.
	const contentClasses = classnames( 'bento-collage__card-content', 'bento-collage__card-content--' + attributes.contentPosition );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Bento Collage Item Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Size', 'qrk' ) }
						help={ __( 'Select the size of the card', 'qrk' ) }
						value={ attributes.size }
						options={ [
							{ label: __( 'Small', 'qrk' ), value: 'small' },
							{ label: __( 'Medium', 'qrk' ), value: 'medium' },
							{ label: __( 'Large', 'qrk' ), value: 'large' },
							{ label: __( 'Extra Large', 'qrk' ), value: 'full' },
						] }
						onChange={ ( size: string ) => setAttributes( { size } ) }
					/>
					<SelectControl
						label={ __( 'Content Position', 'qrk' ) }
						help={ __( 'Position of content in the card', 'qrk' ) }
						value={ attributes.contentPosition }
						options={ [
							{ label: __( 'Top', 'qrk' ), value: 'top' },
							{ label: __( 'Bottom', 'qrk' ), value: 'bottom' },
						] }
						onChange={ ( contentPosition: string ) => setAttributes( { contentPosition } ) }
					/>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="full"
						help={ __( 'Choose an image for this item.', 'qrk' ) }
						onChange={ ( image: object ) => setAttributes( { image } ) }
					/>
					<LinkControl
						label={ __( 'Enter URL', 'qrk' ) }
						value={ attributes.link }
						help={ __( 'Enter a URL for this navigation item', 'qrk' ) }
						onChange={ ( link: object ) => setAttributes( { link } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blocksProps }>
				<figure className="bento-collage__image-wrap">
					<Img
						value={ attributes.image }
					/>
				</figure>
				<div className={ contentClasses }>
					<RichText
						tagName="div"
						className="bento-collage__title"
						placeholder={ __( 'Add title…', 'qrk' ) }
						value={ attributes.title || '' }
						allowedFormats={ [] }
						onChange={ ( title ) => setAttributes( { title } ) }
					/>
					<RichText
						tagName="div"
						className="bento-collage__description"
						placeholder={ __( 'Add description…', 'qrk' ) }
						value={ attributes.description || '' }
						allowedFormats={ [] }
						onChange={ ( description ) => setAttributes( { description } ) }
					/>
					<RichText
						tagName="div"
						className="bento-collage__cta"
						placeholder={ __( 'Add CTA Text…', 'qrk' ) }
						value={ attributes.ctaText || '' }
						allowedFormats={ [] }
						onChange={ ( ctaText ) => setAttributes( { ctaText } ) }
					/>
				</div>
			</div>
		</>
	);
}
