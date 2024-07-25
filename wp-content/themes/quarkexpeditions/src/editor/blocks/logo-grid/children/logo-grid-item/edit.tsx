/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
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
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'logo-grid__logo' ),
	} );

	// Return the block's markup.
	return (
		<figure { ...blocksProps }>
			<SelectImage
				image={ attributes.image }
				className="logo-grid__img"
				size="medium"
				onChange={ ( image: object ): void => {
					// Set attributes.
					setAttributes( { image: null } );
					setAttributes( { image } );
				} }
			/>
		</figure>
	);
}
