/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
} from '@wordpress/blocks';
import {
	RichText,
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
export const name: string = 'quark/lp-footer-featured-on';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Featured On Title', 'qrk' ),
	description: __( 'Footer featured on title block.', 'qrk' ),
	parent: [ 'quark/lp-footer-column' ],
	icon: 'grid-view',
	category: 'layout',
	keywords: [ __( 'heading', 'qrk' ), __( 'title', 'qrk' ) ],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'lp-footer__featured-on' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {}, {
			allowedBlocks: [ 'core/paragraph' ], // TODO:: Use quark/logo-grid once its ready.
			template: [
				[ 'core/paragraph' ],
			],
		} );

		// Return the block's markup.
		return (
			<div { ...blockProps } >
				<RichText
					tagName="h5"
					placeholder={ __( 'Write title…', 'tcs' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
				<div { ...innerBlockProps } />
			</div>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
