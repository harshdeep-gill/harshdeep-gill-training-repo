/**
 * WordPress dependencies
 */
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as info from '../content-info';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'media-content-card__content-column' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlocksProps = useInnerBlocksProps( {}, {
		allowedBlocks: [ 'core/paragraph', info.name ],
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Media Content Card Column Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Has Heading?', 'qrk' ) }
						checked={ attributes.hasHeading }
						help={ __( 'Does this column have a heading?', 'qrk' ) }
						onChange={ ( hasHeading: boolean ) => setAttributes( { hasHeading } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blocksProps }>
				{ attributes.hasHeading &&
					<RichText
						tagName="p"
						className="h4"
						placeholder={ __( 'Write Heading… ', 'qrk' ) }
						value={ attributes.heading }
						onChange={ ( heading: string ) => setAttributes( { heading } ) }
						allowedFormats={ [] }
					/>
				}
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
