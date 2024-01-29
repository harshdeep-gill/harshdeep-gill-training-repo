/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/two-columns-column';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Column', 'qrk' ),
	description: __( 'Individual column.', 'qrk' ),
	parent: [ 'quark/two-columns' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'column', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames( className, 'two-columns__column' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlocksProps = useInnerBlocksProps( { ...blocksProps }, {
			template: [ [ 'core/paragraph', { placeholder: __( 'Write content…', 'qrk' ) } ] ],
			templateLock: false,
		} );

		// Return the block's markup.
		return (
			<div { ...innerBlocksProps } />
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
