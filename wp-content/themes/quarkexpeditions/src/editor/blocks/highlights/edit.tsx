/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as item from './children/highlight-item';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Prepare block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'highlights' ),
	} );

	// Prepare inner block props.
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ item.name ],
		template: [ [ item.name ], [ item.name ], [ item.name ], [ item.name ], [ item.name ], [ item.name ] ],
	} );

	// Return the block's markup.
	return (
		<>
			<Section { ...blockProps } >
				<RichText
					tagName="h2"
					className="highlights__title h5"
					placeholder={ __( 'Write Title…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
				<div { ...innerBlockProps } />
				<RichText
					tagName="p"
					className="highlights__info body-small"
					placeholder={ __( 'Write info…', 'qrk' ) }
					value={ attributes.info }
					onChange={ ( info: string ) => setAttributes( { info } ) }
					allowedFormats={ [] }
				/>
			</Section>
		</>
	);
}
