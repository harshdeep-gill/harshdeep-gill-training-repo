/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

/**
 * Styles.
 */
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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
}
