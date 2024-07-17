/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as seasonItem from '../season-item';
import * as highlight from '../highlight';

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
		className: classnames( className, 'season-highlights__season' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ seasonItem.name ],
		template: [
			[ seasonItem.name, { hasLightBackground: true } ],
			[ seasonItem.name, {}, [ [ highlight.name ], [ highlight.name ] ] ],
		],
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps } >
			<RichText
				tagName="p"
				className="season-highlights__season-title h4"
				placeholder={ __( 'Write Season Title…', 'qrk' ) }
				value={ attributes.title }
				onChange={ ( title: string ) => setAttributes( { title } ) }
				allowedFormats={ [] }
			/>
			<div { ...innerBlockProps } />
		</div>
	);
}
