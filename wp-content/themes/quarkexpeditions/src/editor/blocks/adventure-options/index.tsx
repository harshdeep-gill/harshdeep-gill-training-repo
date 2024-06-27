/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { PanelBody } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Styles.
 */
import '../../../front-end/components/info-cards/style.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { TaxonomyRelationshipControl } = gumponents.components;

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * Block name.
 */
export const name: string = 'quark/adventure-options';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Adventure Options', 'qrk' ),
	description: __( 'Adventure Options block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'adventure', 'qrk' ),
		__( 'options', 'qrk' ),
		__( 'posts', 'qrk' ),
	],
	attributes: {
		termIDs: {
			type: 'array',
			default: [],
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
		// Return the markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Adventure Options Options', 'qrk' ) }>
						<TaxonomyRelationshipControl
							label={ __( 'Select Adventure Option Category.', 'et' ) }
							help={ __( 'Select Adventure Option Category', 'et' ) }
							taxonomies="qrk_adventure_option_category"
							value={ attributes.termIDs }
							onSelect={ ( terms: Array<{ term_id: number }> ) => setAttributes( { termIDs: terms.map( ( term ) => term.term_id ) } ) }
							buttonLabel={ __( 'Select Adventure Option Category', 'qrk' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section className={ classnames( className ) }>
					<ServerSideRender
						block={ name }
						attributes={ attributes }
					/>
				</Section>
			</>
		);
	},
	save() {
		// Don't save any markup.
		return null;
	},
};
