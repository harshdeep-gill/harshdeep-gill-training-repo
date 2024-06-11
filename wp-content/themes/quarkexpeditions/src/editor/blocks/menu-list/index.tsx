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
 * Styles.
 */
import '../../../front-end/components/menu-list/style.scss';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as item from './item';

/**
 * Register child block.
 */
registerBlockType( item.name, item.settings );

/**
 * Block name.
 */
export const name: string = 'quark/menu-list';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Menu List', 'qrk' ),
	description: __( 'Add a list of menu items vertically', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'menu', 'qrk' ),
		__( 'list', 'qrk' ),
	],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
		url: {
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
			className: classnames( className, 'menu-list' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: classnames( 'menu-list__list' ),
		}, {
			allowedBlocks: [ item.name ],
			template: [ [ item.name ], [ item.name ], [ item.name ] ],
			orientation: 'vertical',
		} );

		// Return the block's markup.
		return (
			<>
				<Section { ...blockProps }>
					<RichText
						tagName="p"
						className="menu-list__title overline"
						placeholder={ __( 'Write title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
					<ul { ...innerBlockProps } />
				</Section>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
