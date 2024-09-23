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
			'hero__circle-badge',
			'color-context--dark',
		),
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<p>
				<RichText
					tagName="span"
					placeholder={ __( 'Write Circle badge text…', 'qrk' ) }
					value={ attributes.text }
					onChange={ ( text: string ) => setAttributes( { text } ) }
					allowedFormats={ [] }
				/>
			</p>
		</div>
	);
}
