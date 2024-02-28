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
 * Child blocks.
 */
import * as seasonItem from './season-item';

/**
 * Register child blocks.
 */
registerBlockType( seasonItem.name, seasonItem.settings );

/**
 * Block name.
 */
export const name: string = 'quark/season-highlights-season';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Season', 'qrk' ),
	description: __( 'Season section for Season Highlights Block', 'qrk' ),
	parent: [ 'quark/season-highlights' ],
	icon: 'layout',
	category: 'layout',
	keywords: [ __( 'season', 'qrk' ) ],
	attributes: {
		title: {
			type: 'string',
			default: '',
		}
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
			className: classnames( className, 'season-highlights__season' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
			allowedBlocks: [ seasonItem.name ],
			template: [
				[ seasonItem.name ],
				[ seasonItem.name ],
			],
		} );

		// Return the block's markup.
		return (
			<div { ...blockProps } >
				<RichText
				tagName="h4"
				className="season-highlights__season-title"
				placeholder={ __( 'Write Season Title…', 'qrk' ) }
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
