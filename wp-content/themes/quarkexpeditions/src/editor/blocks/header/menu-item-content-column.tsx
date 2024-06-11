/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import '../../../front-end/components/header/css/header.scss';
import '../../../front-end/components/header/css/nav.scss';

/**
 * Block name.
 */
export const name: string = 'quark/header-menu-item-content-column';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Header Menu Item Content Column', 'qrk' ),
	description: __( 'Individual Content Column within Dropdown Content', 'qrk' ),
	parent: [ 'quark/header-menu-item-content' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [
		__( 'menu', 'qrk' ),
		__( 'item', 'qrk' ),
		__( 'content', 'qrk' ),
		__( 'column', 'qrk' ),
	],
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
			className: classnames( className, 'header__nav-item-dropdown-content-column' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlocksProps = useInnerBlocksProps( { ...blockProps }, {
			allowedBlocks: [ 'quark/two-columns', 'quark/header-menu-item-featured-section' ],
			template: [ [ 'core/paragraph', { placeholder: __( 'Write content…', 'qrk' ) } ] ],
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
