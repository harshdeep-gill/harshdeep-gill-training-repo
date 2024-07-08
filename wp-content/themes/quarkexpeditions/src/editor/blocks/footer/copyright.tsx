/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/footer-copyright';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Copyright Text', 'qrk' ),
	description: __( 'Display the copyright text.', 'qrk' ),
	parent: [ 'quark/footer-bottom' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'copyright', 'qrk' ),
		__( 'text', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: classnames( className, 'footer__copyright' ) } );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [ 'core/paragraph' ],
				template: [ [ 'core/paragraph', { placeholder: 'Write Copyright Text…' } ] ],
			}
		);

		// Return the block's markup.
		return ( <div { ...innerBlockProps } /> );
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
