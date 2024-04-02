/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
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
 * Child blocks.
 */
import * as title from './title';
import * as promotion from './promotion';
import * as help from './help';
import * as cta from './cta';

/**
 * Register child block.
 */
registerBlockType( title.name, title.settings );
registerBlockType( promotion.name, promotion.settings );
registerBlockType( help.name, help.settings );
registerBlockType( cta.name, cta.settings );

/**
 * Block name.
 */
export const name: string = 'quark/offer-cards-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Offer Cards Card', 'qrk' ),
	description: __( 'Individual Card Item for offer cards.', 'qrk' ),
	parent: [ 'quark/offer-cards' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'card', 'qrk' ) ],
	attributes: {
		heading: {
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
	edit( {
		className,
		attributes,
		setAttributes,
	}: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'offer-cards__card' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {},
			{
				allowedBlocks: [
					title.name, promotion.name, help.name, cta.name,
				],
				template: [
					[ title.name ],
					[ promotion.name ],
					[ cta.name ],
					[ help.name ],
				],
			},
		);

		// Return the block's markup.
		return (
			<article { ...blockProps }>
				<RichText
					tagName="div"
					className="offer-cards__heading overline"
					placeholder={ __( 'Write Heading Text…', 'qrk' ) }
					value={ attributes.heading }
					onChange={ ( heading: string ) => setAttributes( { heading } ) }
					allowedFormats={ [] }
				/>
				<div className="offer-cards__content">
					<div { ...innerBlockProps } />
				</div>
			</article>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
