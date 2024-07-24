/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody, Placeholder,
	RangeControl,
	SelectControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const {
	GalleryControl,
	Img,
} = gumponents.components;

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
		className: classnames(
			className,
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Hero Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Transition Type', 'qrk' ) }
						help={ __( 'Select the transition type', 'qrk' ) }
						value={ attributes.transitionType }
						options={ [
							{ label: __( 'Manual', 'qrk' ), value: 'manual' },
							{ label: __( 'Auto', 'qrk' ), value: 'auto' },
						] }
						onChange={ ( transitionType: string ) => setAttributes( { transitionType } ) }
					/>
					{ 'auto' === attributes.transitionType && (
						<RangeControl
							label={ __( 'Interval', 'qrk' ) }
							value={ attributes.interval }
							onChange={ ( interval ) => setAttributes( { interval } ) }
							min={ 2 }
							max={ 10 }
						/>
					) }
					<GalleryControl
						label={ __( 'Slide Items', 'qrk' ) }
						help={ __( 'Select the slide items', 'qrk' ) }
						size="medium"
						onSelect={ ( value: [] ) => {
							// The block editor doesn't update arrays correctly?
							setAttributes( { items: [] } );

							// Update the items.
							setAttributes( { items: value } );
						} }
						value={ attributes.items }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }>
				{ attributes.items.length === 0 && (
					<Placeholder
						icon="format-image"
						label={ __( 'Hero Card Slider', 'qrk' ) }
						instructions={ __( 'Select the slide items', 'qrk' ) }
					/>
				) }
				{ attributes.items.length > 0 && (
					<div className="hero-card-slider">
						<div className="hero-card-slider__card">
							<figure className="hero-card-slider__image">
								<Img
									value={ attributes.items[ 0 ] }
								/>
							</figure>
						</div>
					</div>
				) }
			</Section>
		</>
	);
}
