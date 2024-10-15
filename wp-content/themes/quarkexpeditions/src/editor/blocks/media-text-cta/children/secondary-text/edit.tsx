/**
 * WordPress dependencies.
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit component.
 *
 * @param {Object} props           Component properties.
 * @param {Object} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blocksProps = useBlockProps( {
		className: classnames( className, 'media-text-cta__secondary-text' ),
	} );

	// Set inner blocks props.
	const innerBlocksProps = useInnerBlocksProps( blocksProps, {
		allowedBlocks: [ 'core/paragraph', 'core/list' ],
		template: [ [ 'core/paragraph' ] ],
	} );

	// Return the block's markup.
	return (
		<div { ...innerBlocksProps } />
	);
}
