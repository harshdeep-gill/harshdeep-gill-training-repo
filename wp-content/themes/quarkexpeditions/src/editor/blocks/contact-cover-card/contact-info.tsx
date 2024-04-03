/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
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
 * Child blocks.
 */
import * as contactInfoItem from './contact-info-item';

/**
 * Register child blocks.
 */
registerBlockType( contactInfoItem.name, contactInfoItem.settings );

/**
 * Block name.
 */
export const name: string = 'quark/contact-cover-card-contact-info';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Contact Info', 'qrk' ),
	description: __( 'Individual content info for contact cover card.', 'qrk' ),
	parent: [ 'quark/contact-cover-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'info', 'qrk' ), __( 'contact', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'contact-cover-card__contact-info', 'body-small' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps },
			{
				allowedBlocks: [ contactInfoItem.name ],
				template: [ [ contactInfoItem.name ], [ contactInfoItem.name ] ],
			} );

		// Return the block's markup.
		return (
			<div { ...innerBlockProps } />
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
