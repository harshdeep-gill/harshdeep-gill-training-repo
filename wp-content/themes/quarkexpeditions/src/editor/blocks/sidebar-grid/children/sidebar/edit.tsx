/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import { name as tableOfContents } from '../../../table-of-contents';

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
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'sidebar-grid__sidebar', {
			'sidebar-grid__sidebar--sticky': attributes.stickySidebar,
			'sidebar-grid__sidebar--show-on-mobile': attributes.showOnMobile,
		} ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlocksProps = useInnerBlocksProps( { ...blocksProps }, {
		allowedBlocks: [ tableOfContents, 'core/paragraph', 'core/list' ],
		template: [ [ 'core/paragraph', { placeholder: __( 'Sidebar…', 'qrk' ) } ] ],
		templateLock: false,
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Sidebar Grid Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Sticky Sidebar', 'qrk' ) }
						checked={ attributes.stickySidebar }
						help={ __( 'Should the sidebar be sticky on scroll?', 'qrk' ) }
						onChange={ ( stickySidebar: boolean ) => setAttributes( { stickySidebar } ) }
					/>
					<ToggleControl
						label={ __( 'Show on Mobile', 'qrk' ) }
						checked={ attributes.showOnMobile }
						help={ __( 'Should the sidebar be visible on mobile devices?', 'qrk' ) }
						onChange={ ( showOnMobile: boolean ) => setAttributes( { showOnMobile } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<aside { ...innerBlocksProps } />
		</>
	);
}
