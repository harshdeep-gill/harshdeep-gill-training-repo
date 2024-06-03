/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
} from '@wordpress/blocks';
import {
	PanelBody,
	Placeholder,
	RadioControl,
	ToggleControl,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import {
	InspectorControls,
} from '@wordpress/block-editor';

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
const {
	PostRelationshipControl,
	TaxonomyRelationshipControl,
	MultiSelectControl,
} = gumponents.components;

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * Block name.
 */
export const name: string = 'quark/blog-post-cards';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Blog Post Cards', 'qrk' ),
	description: __( 'Blog Post Cards Block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'blog', 'qrk' ),
		__( 'post', 'qrk' ),
		__( 'cards', 'qrk' ),
	],
	attributes: {
		selection: {
			type: 'string',
			default: 'manual',
		},
		layout: {
			type: 'string',
			default: 'collage',
		},
		ids: {
			type: 'array',
			default: [],
		},
		taxonomies: {
			type: 'array',
			default: [],
		},
		termIds: {
			type: 'array',
			default: [],
		},
		isMobileCarousel: {
			type: 'boolean',
			default: true,
		},
		hasOfferTag: {
			type: 'boolean',
			default: false,
		},
		totalPosts: {
			type: 'number',
			default: 5,
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
		// Initialize.
		let allOptionsSelected = false;

		// Check if all options are selected.
		if ( 'recent' === attributes.selection ) {
			allOptionsSelected = true;
		} else if ( 'manual' === attributes.selection && 0 !== attributes.ids.length ) {
			allOptionsSelected = true;
		} else if ( 'byTerms' === attributes.selection && 0 !== attributes.termIds.length && 0 !== attributes.taxonomies.length ) {
			allOptionsSelected = true;
		}

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Related Posts Options', 'qrk' ) }>
						<RadioControl
							label={ __( 'Layout', 'qrk' ) }
							help={ __( 'Select the layout of the cards', 'qrk' ) }
							selected={ attributes.layout }
							options={ [
								{ label: __( 'Grid', 'qrk' ), value: 'grid' },
								{ label: __( 'Collage', 'qrk' ), value: 'collage' },
							] }
							onChange={ ( layout: string ) => setAttributes( { layout } ) }
						/>
						<SelectControl
							label={ __( 'Selection', 'qrk' ) }
							help={ __( 'Select how you would like to select posts', 'qrk' ) }
							value={ attributes.selection }
							options={ [
								{ label: __( 'Manual', 'qrk' ), value: 'manual' },
								{ label: __( 'By Terms', 'qrk' ), value: 'byTerms' },
								{ label: __( 'Recent', 'qrk' ), value: 'recent' },
							] }
							onChange={ ( selection: string ) => setAttributes( { selection } ) }
						/>
						{
							'manual' === attributes.selection &&
							<PostRelationshipControl
								label={ __( 'Select Blog Posts', 'qrk' ) }
								help={ __( 'Select blog posts', 'qrk' ) }
								postTypes="post"
								value={ attributes.ids }
								onSelect={ ( blogPosts: any ) => setAttributes( { ids: blogPosts.map( ( post: any ) => post.ID ) } ) }
								button={ __( 'Select Blog Posts', 'qrk' ) }
							/>
						}
						{ 'byTerms' === attributes.selection &&
							<MultiSelectControl
								label={ __( 'Taxonomies', 'qrk' ) }
								help={ __( 'Select one or more taxonomies', 'qrk' ) }
								options={ [
									{ label: 'Tags', value: 'post_tag' },
									{ label: 'Categories', value: 'category' },
								] }
								value={ attributes.taxonomies }
								onChange={ ( taxonomies: any ) => setAttributes( { taxonomies } ) }
								placeholder={ __( 'Select taxonomies', 'qrk' ) }
							/>
						}
						{
							attributes.taxonomies.length > 0 &&
							<TaxonomyRelationshipControl
								label={ __( 'Select Terms' ) }
								help={ __( 'Select posts associated to these terms', 'qrk' ) }
								taxonomies={ attributes.taxonomies }
								value={ attributes.termIds }
								onSelect={ ( terms: any ) => setAttributes( { termIds: terms.map( ( term: any ) => term.term_id ) } ) }
							/>
						}
						{
							( 'byTerms' === attributes.selection || 'recent' === attributes.selection ) &&
								<TextControl
									label={ __( 'Total Posts', 'qrk' ) }
									help={ __( 'Enter the total number of posts', 'qrk' ) }
									value={ attributes.totalPosts }
									type="number"
									onChange={ ( totalPosts ) => {
										// Check if totalPosts is not empty.
										if ( '' === totalPosts ) {
											totalPosts = '0';
										} else {
											setAttributes( { totalPosts } );
										}
									} }
								/>
						}
						<ToggleControl
							label={ __( 'Is Mobile Carousel?', 'qrk' ) }
							checked={ attributes.isMobileCarousel }
							help={ __( 'Show the blog posts in a carousel on mobile devices', 'qrk' ) }
							onChange={ ( isMobileCarousel: boolean ) => setAttributes( { isMobileCarousel } ) }
						/>
						<ToggleControl
							label={ __( 'Has \'New Offer\' Tag?', 'qrk' ) }
							checked={ attributes.hasOfferTag }
							help={ __( 'Show the \'New Offer\' Tag', 'qrk' ) }
							onChange={ ( hasOfferTag: boolean ) => setAttributes( { hasOfferTag } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section className={ classnames( className ) }>
					{
						allOptionsSelected
							? (
								<ServerSideRender
									block={ name }
									attributes={ attributes }
								/>
							) : (
								<Placeholder icon="layout" label={ __( 'Blog Post Cards', 'qrk' ) }>
									<p>{ __( 'Select the blog posts to be displayed.', 'qrk' ) }</p>
								</Placeholder>
							)
					}
				</Section>
			</>
		);
	},
	save() {
		// Don't save any markup.
		return null;
	},
};
