/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import * as formModalCta from '../form-modal-cta';
import * as iconBadge from '../icon-badge';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Children blocks
 */
import * as overline from './overline';
import * as heroTitle from './hero-title';
import * as heroSubtitle from './hero-subtitle';
import * as heroDescription from './hero-description';

/**
 * Register children blocks
 */
registerBlockType( overline.name, overline.settings );
registerBlockType( heroTitle.name, heroTitle.settings );
registerBlockType( heroSubtitle.name, heroSubtitle.settings );
registerBlockType( heroDescription.name, heroDescription.settings );

/**
 * Block name.
 */
export const name: string = 'quark/hero-content-left';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Content Left', 'qrk' ),
	description: __( 'Left half of hero content.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'hero', 'qrk' ),
		__( 'content', 'qrk' ),
		__( 'left', 'qrk' ),
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
			className: classnames( className, 'hero__left' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [
					iconBadge.name, formModalCta.name, overline.name, heroTitle.name, heroSubtitle.name, heroDescription.name,
				],
				template: [
					[ overline.name ],
					[ heroTitle.name ],
					[ heroSubtitle.name ],
					[ heroDescription.name ],
					[ iconBadge.name, { className: 'hero__tag' } ],
					[ formModalCta.name, { className: 'hero__form-modal-cta color-context--dark' } ],
				],
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
