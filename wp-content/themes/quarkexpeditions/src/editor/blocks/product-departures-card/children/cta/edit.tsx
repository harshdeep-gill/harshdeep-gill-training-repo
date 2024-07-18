/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

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
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'product-departures-card__cta' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ 'quark/button' ],
			template: [
				[ 'quark/button', { isSizeBig: true } ],
			],
		},
	);

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
