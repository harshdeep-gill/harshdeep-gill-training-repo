/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	InnerBlocks,
	useBlockProps,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as menuItem from '../menu-item';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'header__primary-nav' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'header__nav-menu' ),
	},
	{
		allowedBlocks: [ menuItem.name ],
		template: [
			[ menuItem.name ],
			[ menuItem.name ],
			[ menuItem.name ],
			[ menuItem.name ],
			[ menuItem.name ],
		],
		renderAppender: InnerBlocks.DefaultBlockAppender,

		// @ts-ignore
		orientation: 'horizontal',
	}
	);

	// Return block.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Header Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Display More button', 'qrk' ) }
						help={ __( 'Show or hide the More button', 'qrk' ) }
						checked={ attributes.hasMoreButton }
						onChange={ ( value ) => setAttributes( { hasMoreButton: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<nav { ...blockProps } >
				<ul { ...innerBlockProps } />
			</nav>
		</>
	);
}
