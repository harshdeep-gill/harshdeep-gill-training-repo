/**
 * WordPress dependencies.
 */
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import classNames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) {
	// Get block props.
	const blockProps = useBlockProps( {
		className: classNames( className, 'secondary-navigation__navigation-items' ),
	} );

	// Return markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Options', 'qrk' ) }>
					<LinkControl
						label={ __( 'Enter URL', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter a URL for this navigation item', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<li { ...blockProps }>
				<RichText
					tagName="a"
					className="secondary-navigation__navigation-item-link"
					placeholder={ __( 'Navigation Item…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
				/>
			</li>
		</>
	);
}
