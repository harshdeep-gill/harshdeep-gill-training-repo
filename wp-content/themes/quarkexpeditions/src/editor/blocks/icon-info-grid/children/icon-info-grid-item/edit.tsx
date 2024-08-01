/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../../../icons';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Prepare block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'icon-info-grid__item' ),
	} );

	// Prepare inner block props.
	const innerBlockProps = useInnerBlocksProps( {}, {
		allowedBlocks: [ 'core/heading', 'core/paragraph' ],
		template: [ [ 'core/heading', { level: 4 } ], [ 'core/paragraph' ] ],
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps } >
			<InspectorControls>
				<PanelBody title={ __( 'Icon Info Grid Item Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Icon', 'qrk' ) }
						value={ attributes.icon }
						options={ [
							{ label: 'Star', value: 'star' },
						] }
						onChange={ ( value ) => setAttributes( { icon: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div className={ 'icon-info-grid__icon' }>
				{ icons?.[ attributes.icon ] }
			</div>
			<div { ...innerBlockProps } />
		</div>
	);
}
