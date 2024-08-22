/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';
import metadata from './block.json';

/**
 * Children blocks
 */
import * as specificationItem from './children/specification';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Block properties.
	const blockProps = useBlockProps( {
		className: classnames( className, 'quark-specifications', 'specifications' ),
	} );

	// Inner blocks properties.
	const innerBlockProps = useInnerBlocksProps(
		{ className: classnames( className, 'specifications__items', 'grid', 'grid--cols-3' ) },
		{
			allowedBlocks: [ specificationItem.name ],
			template: [ [ specificationItem.name ], [ specificationItem.name ], [ specificationItem.name ] ],
			renderAppender: InnerBlocks.ButtonBlockAppender,
		}
	);

	// Return the block's markup.
	return (
		<Section { ...blockProps }>
			<RichText
				tagName="h2"
				className="specifications__title h4"
				placeholder={ __( 'Write the Title', 'qrk' ) }
				value={ attributes.title }
				onChange={ ( title: string ) => setAttributes( { title } ) }
				allowedFormats={ [] }
			/>
			<div { ...innerBlockProps } />
		</Section>
	);
}
