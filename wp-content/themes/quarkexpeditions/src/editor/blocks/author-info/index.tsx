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
 * Styles.
 */
import '../../../front-end/components/post-author-info/style.scss';
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { SelectImage } = gumponents.components;

/**
 * Children blocks
 */
import * as authorName from './name';
import * as readTime from './read-time';

/**
 * Register child block.
 */
registerBlockType( authorName.name, authorName.settings );
registerBlockType( readTime.name, readTime.settings );

/**
 * Block name.
 */
export const name: string = 'quark/author-info';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Author Info', 'qrk' ),
	description: __( 'Display a Author Info block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'author', 'qrk' ),
		__( 'info', 'qrk' ),
	],
	attributes: {
		authorImage: {
			type: 'object',
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
			className: classnames(
				className,
				'post-author-info'
			),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ className: 'post-author-info__info' },
			{
				allowedBlocks: [ authorName.name, readTime.name ],
				template: [ [ authorName.name ], [ readTime.name ] ],
				templateLock: 'all',
			}
		);

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<SelectImage
					image={ attributes.authorImage }
					size="thumbnail"
					className="post-author-info__image"
					onChange={ ( authorImage: Object ): void => {
						// Set image.
						setAttributes( { authorImage } );
					} }
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
