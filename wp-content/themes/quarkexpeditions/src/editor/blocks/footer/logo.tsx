/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import classnames from 'classnames';
import icons from '../icons';

/**
 * Block name.
 */
export const name: string = 'quark/footer-logo';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Site logo', 'qrk' ),
	description: __( 'Display a Site logo in the footer.', 'qrk' ),
	parent: [ 'quark/footer-column' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'site', 'qrk' ),
		__( 'logo', 'qrk' ),
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
		const blockProps = useBlockProps( { className: classnames( className, 'footer__logo' ) } );

		// Return the block's markup.
		return ( <span { ...blockProps }> { icons.logo } </span> );
	},
	save() {
		// Return null;
		return null;
	},
};
