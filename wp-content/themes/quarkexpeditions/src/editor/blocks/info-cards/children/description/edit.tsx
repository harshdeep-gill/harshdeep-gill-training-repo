/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Edit component.
 *
 * @param {Object} props           Component properties.
 * @param {Object} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set the block's properties.
	const blockProps = useBlockProps( {
		className: classnames( className, 'info-cards__card-description' ),
	} );

	// Set the inner block's properties.
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ 'core/paragraph' ],
			template: [ [ 'core/paragraph', { placeholder: __( 'Write Description…', 'qrk' ) } ] ],
		},
	);

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
