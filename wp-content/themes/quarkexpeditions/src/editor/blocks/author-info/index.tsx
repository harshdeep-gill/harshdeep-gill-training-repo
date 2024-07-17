/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
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

/**
 * Block name.
 */
export const name: string = 'quark/author-info';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
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
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames(
				className,
				'post-author-info'
			),
		} );

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<div className="post-author-info__image"></div>
				<div className="post-author-info__info">
					<p className="post-author-info__name">{ __( 'Author Name', 'qrk' ) }</p>
					<p className="post-author-info__duration">{ __( 'X min read', 'qrk' ) }</p>
				</div>
			</div>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
