/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import './editor.scss';

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
	// Set Block Props
	const blockProps = useBlockProps( {
		className: classnames( className, 'quarks-template-title' ),
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<RichText
				className="template-title"
				tagName="h1"
				placeholder={ __( 'Write the title', 'qrk' ) }
				value={ attributes.title }
				onChange={ ( title: string ) => setAttributes( { title } ) }
				allowedFormats={ [] }
			/>
		</div>
	);
}
