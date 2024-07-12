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
 * Internal dependencies.
 */
import * as formTwoStep from '../form-two-step';
import * as formTwoStepCompact from '../form-two-step-compact';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/hero-content-right';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Content Right', 'qrk' ),
	description: __( 'Right half of hero content.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'hero', 'qrk' ),
		__( 'content', 'qrk' ),
		__( 'right', 'qrk' ),
	],
	attributes: {},
	parent: [ 'quark/hero-content' ],
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'hero__right' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [ formTwoStep.name, formTwoStepCompact.name ],
				template: [ [ formTwoStep.name ] ],
			}
		);

		// Return the block's markup.
		return <div { ...innerBlockProps } />;
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
