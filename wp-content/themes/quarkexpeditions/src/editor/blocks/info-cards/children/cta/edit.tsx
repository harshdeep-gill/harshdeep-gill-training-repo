/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
import icons from '../../../icons';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// Set block attributes.
	const blocksProps = useBlockProps( {
		className: classnames(
			className,
			'info-cards__card-cta',
		),
	} );

	// Return the block's markup.
	return (
		<div { ...blocksProps }>
			<RichText
				className="info-cards__card-cta-text"
				tagName="div"
				placeholder={ __( 'CTA Text', 'qrk' ) }
				value={ attributes.text }
				onChange={ ( text: string ) => setAttributes( { text } ) }
				allowedFormats={ [] }
			/>
			<span className="info-cards__card-cta-icon">
				{ icons.chevronRight }
			</span>
		</div>
	);
}
