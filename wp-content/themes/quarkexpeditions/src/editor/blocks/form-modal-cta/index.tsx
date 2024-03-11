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
export const name: string = 'quark/form-modal-cta';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Form modal CTA', 'qrk' ),
	description: __( 'A CTA to open up a form modal.', 'qrk' ),
	icon: 'button',
	category: 'widgets',
	keywords: [ __( 'cta', 'qrk' ), __( 'form', 'qrk' ), __( 'modal', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(

			// eslint-disable-next-line react-hooks/rules-of-hooks
			{ ...useBlockProps(), className: classnames( className, 'form-modal-cta' ) },
			{
				allowedBlocks: [ 'core/buttons' ],
				template: [ [ 'core/buttons' ] ],
				templateLock: 'all',
			},
		);

		// Return the block's markup.
		return (
			<div { ...innerBlockProps } />
		);
	},
	save() {
		// Don't save anything.
		return <InnerBlocks.Content />;
	},
};
