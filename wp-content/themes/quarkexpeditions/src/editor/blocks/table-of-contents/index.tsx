/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { RichText, useBlockProps } from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/table-of-contents/style.scss';
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/table-of-contents';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Table of Contents', 'qrk' ),
	description: __( 'Add a Table of contents block.', 'qrk' ),
	category: 'layout',
	parent: [ 'quark/sidebar-grid-sidebar' ],
	keywords: [
		__( 'table', 'qrk' ),
		__( 'of', 'qrk' ),
		__( 'contents', 'qrk' ),
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
		const blockProps = useBlockProps( { className: classnames( className, 'table-of-contents', 'grid' ) } );

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<h3 className="h4 table-of-contents__title">
					<RichText
						tagName="span"
						placeholder={ __( 'Write Table of Contents Title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
				</h3>
				<ul className="table-of-contents__list">
					<li className={ classnames( 'table-of-contents__list-item', 'table-of-contents__list-item--active' ) } >
						<div className="table-of-contents__list-item-title" />
						<div className="table-of-contents__list-item-title" />
					</li>
					<li className="table-of-contents__list-item">
						<div className="table-of-contents__list-item-title" />
						<div className="table-of-contents__list-item-title" />
					</li>
					<li className="table-of-contents__list-item">
						<div className="table-of-contents__list-item-title" />
						<div className="table-of-contents__list-item-title" />
					</li>
					<li className="table-of-contents__list-item">
						<div className="table-of-contents__list-item-title" />
						<div className="table-of-contents__list-item-title" />
					</li>
					<li className="table-of-contents__list-item">
						<div className="table-of-contents__list-item-title" />
						<div className="table-of-contents__list-item-title" />
					</li>
				</ul>
			</div>
		);
	},
	save() {
		// No markup to save.
		return null;
	},
};
