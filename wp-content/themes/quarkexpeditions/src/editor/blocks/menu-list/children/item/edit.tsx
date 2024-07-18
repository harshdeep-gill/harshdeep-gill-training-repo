/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * External components.
 */
const { gumponents } = window;
const { LinkControl } = gumponents.components;

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'menu-list__item' ),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Menu List Options', 'qrk' ) }>
					<LinkControl
						label={ __( 'Select URL', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter an URL for this item', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<li { ...blockProps }>
				<RichText
					tagName="a"
					className="maybe-link menu-list__item-link"
					placeholder={ __( 'Menu List Item…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
			</li>
		</>
	);
}
