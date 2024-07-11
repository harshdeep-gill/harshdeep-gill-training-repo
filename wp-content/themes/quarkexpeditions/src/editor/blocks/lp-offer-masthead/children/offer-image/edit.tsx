/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * External dependencies
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { SelectImage } = gumponents.components;

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'lp-offer-masthead__offer-image' ),
	} );

	// Return the block's markup.
	return (
		<figure { ...blockProps } >
			<SelectImage
				image={ attributes.offerImage }
				placeholder="Choose an Offer Image"
				size="large"
				onChange={ ( offerImage: Object ): void => {
					// Set image.
					setAttributes( { offerImage: null } );
					setAttributes( { offerImage } );
				} }
			/>
		</figure>
	);
}
