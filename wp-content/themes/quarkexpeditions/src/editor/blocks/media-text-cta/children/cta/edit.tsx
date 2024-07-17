/**
 * WordPress dependencies.
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import * as lpFormModalCta from '../../../lp-form-modal-cta';

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
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'media-text-cta__cta' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ lpFormModalCta.name, 'quark/button' ],
			template: [
				[ lpFormModalCta.name, { backgroundColor: 'black' } ],
			],
		},
	);

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
