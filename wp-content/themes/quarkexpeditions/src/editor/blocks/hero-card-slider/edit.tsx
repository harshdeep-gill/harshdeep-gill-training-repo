/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	Placeholder,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;
import icons from '../icons';

/**
 * Styles.
 */
import './editor.scss';

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
	// Get the block's props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Hero Card Slider Options', 'qrk' ) }>
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
							label={ __( 'Interval (In Seconds)', 'qrk' ) }
							value={ attributes.interval }
							onChange={ ( interval ) => setAttributes( { interval } ) }
							min={ 2 }
							max={ 10 }
						/>
					) }
					<ToggleControl
						label={ __( 'Show Controls', 'qrk' ) }
						help={ __( 'Display the slider controls?', 'qrk' ) }
						checked={ attributes.showControls }
						onChange={ ( showControls ) => setAttributes( { showControls } ) }
					/>
					<GalleryControl
						label={ __( 'Slide Items', 'qrk' ) }
						help={ __( 'Select one or more images.', 'qrk' ) }
						size="medium"
						onSelect={ ( value: [] ) => {
							// The block editor doesn't update arrays correctly?
							setAttributes( { items: [] } );

							// Update the items.
							setAttributes( { items: value } );
						} }
						value={ attributes.items }
					/>
					<ToggleControl
						label={ __( 'Is Lightbox?', 'qrk' ) }
						help={ __( 'Display the images in a lightbox on click?', 'qrk' ) }
						checked={ attributes.isLightbox }
						onChange={ ( isLightbox ) => setAttributes( { isLightbox } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
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
							{
								attributes.showControls && (
									<div className="hero-card-slider__arrows">
										<button className="hero-card-slider__arrow-button hero-card-slider__arrow-button--left">
											{ icons.chevronLeft }
										</button>
										<button className="hero-card-slider__arrow-button hero-card-slider__arrow-button--right">
											{ icons.chevronRight }
										</button>
									</div>
								)
							}
							<figure className="hero-card-slider__image">
								<Img
									value={ attributes.items[ 0 ] }
								/>
							</figure>
						</div>
					</div>
				) }
			</div>
		</>
	);
}
