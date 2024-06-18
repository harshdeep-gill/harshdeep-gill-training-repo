/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	RichText,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Children blocks
 */
import * as navigationItem from './navigation-item';

/**
 * Register children blocks.
 */
registerBlockType( navigationItem.name, navigationItem.settings );

/**
 * Block name.
 */
export const name: string = 'quark/footer-navigation';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Navigation', 'qrk' ),
	description: __( 'Display the footer navigation container.', 'qrk' ),
	parent: [ 'quark/footer-top', 'quark/footer-middle', 'quark/footer-bottom' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'navigation', 'qrk' ),
	],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'footer__accordion' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ className: classnames( className, 'footer__navigation' ) },
			{
				allowedBlocks: [ navigationItem.name ],
				template: [ [ navigationItem.name ], [ navigationItem.name ] ],
			}
		);

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<p>
					<RichText
						tagName="span"
						className="footer__navigation-title"
						placeholder={ __( 'Write Navigation Title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
				</p>
				<ul { ...innerBlockProps } />
			</div>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
