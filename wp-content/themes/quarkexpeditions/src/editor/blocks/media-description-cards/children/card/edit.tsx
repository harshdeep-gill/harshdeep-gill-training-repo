/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { SelectImage } = gumponents.components;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'media-description-cards__card',
		),
	} );

	// Return the block's markup.
	return (
		<>
			<article { ...blockProps }>
				<div className="media-description-cards__media-wrap">
					<SelectImage
						image={ attributes.image }
						placeholder="Choose an image"
						size="medium"
						onChange={ ( image: Object ): void => {
							// Set image.
							setAttributes( { image: null } );
							setAttributes( { image } );
						} }
					/>
				</div>
				<div className="media-description-cards__content">
					<RichText
						tagName="h3"
						className="media-description-cards__title h4"
						placeholder={ __( 'Title here…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
					<div className="media-description-cards__description">
						<RichText
							tagName="p"
							placeholder={ __( 'Description here…', 'qrk' ) }
							value={ attributes.description }
							onChange={ ( description ) => setAttributes( { description } ) }
							allowedFormats={ [] }
						/>
					</div>
				</div>
			</article>
		</>
	);
}
