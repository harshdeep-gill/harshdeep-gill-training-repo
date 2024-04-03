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
import * as inquiryForm from '../inquiry-form';
import * as twoStepForm from '../form-two-step';

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
	parent: [ 'quark/hero' ],
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
				allowedBlocks: [ inquiryForm.name, twoStepForm.name ],
				template: [ [ twoStepForm.name ] ],
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
