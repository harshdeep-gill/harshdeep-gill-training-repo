/**
 * WordPress dependencies.
 */
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 * TODO: Import as per new block architecture.
 */
import * as lpFormModalCta from '../../../lp-form-modal-cta';

/**
 * Edit component.
 *
 * @param {Object} props           Component properties.
 * @param {Object} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'product-cards__buttons' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ lpFormModalCta.name, 'quark/button' ],
			template: [
				[ lpFormModalCta.name ],
				[ 'quark/button', { isSizeBig: true } ],
			],
		},
	);

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
