/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
import icons from '../../../icons';
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
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// Set block attributes.
	const blocksProps = useBlockProps( {
		className: classnames(
			className,
			'link-detail-cards__card',
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Link Detail Card Options', 'qrk' ) }>
					<LinkControl
						label={ __( 'Select URL', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter an URL for this Info item', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blocksProps }>
				<div className="link-detail-cards__title">
					<RichText
						tagName="h3"
						className="h4"
						placeholder={ __( 'Write title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
					<span className="link-detail-cards__chevron">
						{ icons.chevronLeft }
					</span>
				</div>
				<div className="link-detail-cards__description">
					<RichText
						tagName="p"
						placeholder={ __( 'Write description…', 'qrk' ) }
						value={ attributes.description }
						onChange={ ( description: string ) => setAttributes( { description } ) }
						allowedFormats={ [] }
					/>
				</div>
			</div>
		</>
	);
}
