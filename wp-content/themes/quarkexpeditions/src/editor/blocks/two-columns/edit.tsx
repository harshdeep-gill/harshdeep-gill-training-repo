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
 * Styles.
 */
import './editor.scss';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';
import * as column from './children/column';

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
	const blockProps = useBlockProps( {
		className: classnames( className ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'two-columns grid grid--cols-2', {
			'two-columns--has-border': attributes.hasBorder,
		} ),
	}, {
		allowedBlocks: [ column.name ],
		template: [ [ column.name ], [ column.name ] ],
		templateLock: 'all',
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Two Columns Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Has Borders', 'qrk' ) }
						checked={ attributes.hasBorder }
						help={ __( 'Should these columns have borders?', 'qrk' ) }
						onChange={ ( hasBorder: boolean ) => setAttributes( { hasBorder } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }>
				<div { ...innerBlockProps } />
			</Section>
		</>
	);
}
