/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	Button,
	PanelBody,
	PanelRow,
	Placeholder,
	RangeControl,
	SelectControl,
	ToggleControl,
	Modal,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { select } from '@wordpress/data';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;
import icons from '../icons';

/**
 * Internal dependencies.
 */
import metadata from './block.json';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Styles.
 */
import './editor.scss';

/**
 * External components.
 */
const {
	Img,
} = gumponents.components;

/**
 * Child blocks.
 */
import * as card from './children/card';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 * @param {string}   props.clientId      Block client ID.
 */
export default function edit( { className, attributes, setAttributes, clientId }: BlockEditAttributes ): JSX.Element {
	// Set the modal state.
	const [ isModalOpen, setIsModalOpen ] = useState( false );

	// Edit mode.
	const [ editMode, setEditMode ] = useState( false );

	// Modal functions.
	const openModal = () => setIsModalOpen( true );
	const closeModal = () => setIsModalOpen( false );

	// Handle the modal.
	const handleModal = () => {
		// Toggle the modal.
		if ( isModalOpen ) {
			// Close the modal.
			closeModal();
		} else {
			// Open the modal. Default to edit mode.
			openModal();
			setEditMode( true );
		}
	};

	// Get the block's props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'hero-details-card-slider',
			{ 'hero-details-card-slider--edit-mode': editMode },
		),
	} );

	// Inner blocks props.
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'hero-details-card-slider__slides',
		},
		{
			allowedBlocks: [ card.name ],
			template: [
				[ card.name ],
				[ card.name ],
				[ card.name ],
			],

			// @ts-ignore
			orientation: 'horizontal',
			renderAppender: InnerBlocks.ButtonBlockAppender,
		}
	);

	// Inner blocks.
	const innerBlocks = select( 'core/block-editor' ).getBlocksByClientId( clientId )?.[ 0 ]?.innerBlocks;

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Hero Details Card Slider Options', 'qrk' ) }>
					<PanelRow>
						<Button
							variant="primary"
							className="hero-details-card-slider__editor-button"
							onClick={ () => handleModal() }
						>
							{ isModalOpen
								? __( 'Close Editor', 'qrk' )
								: __( 'Open Editor', 'qrk' ) }
						</Button>
					</PanelRow>
					<PanelRow>
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
					</PanelRow>
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
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ 0 === innerBlocks?.length &&
					<Placeholder
						icon="format-image"
						label={ __( 'Hero Card Slider', 'qrk' ) }
						instructions={ __( 'Select the slide items', 'qrk' ) }
					/>
				}
				{ 0 !== innerBlocks?.length &&
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
									value={ innerBlocks?.[ 0 ]?.attributes?.media }
								/>
							</figure>
							<div className="hero-card-slider__overlay" />
							<div className="hero-card-slider__content">
								<div className={ classnames( 'overline', 'hero-card-slider__' + innerBlocks?.[ 0 ]?.attributes?.tagType ) }>
									{ innerBlocks?.[ 0 ]?.attributes?.tagText }
								</div>
								<div className="hero-card-slider__title">
									{ innerBlocks?.[ 0 ]?.attributes?.title }
								</div>
								<div className="hero-card-slider__description">
									{ innerBlocks?.[ 0 ]?.attributes?.descriptionText }
								</div>
							</div>
						</div>
					</div>
				}
			</div>
			{ isModalOpen &&
				<Modal
					title={ __( 'Hero Card Slider Editor', 'qrk' ) }
					className="hero-details-card-slider__modal"
					overlayClassName="hero-details-card-slider__modal-overlay"
					onRequestClose={ closeModal }
					size={ editMode ? 'fill' : 'medium' }
				>
					<Button
						variant="primary"
						className="hero-details-card-slider__editor-button"
						onClick={ () => setEditMode( ( prev ) => ! prev ) }
					>
						{ editMode
							? __( 'View Mode', 'qrk' )
							: __( 'Edit Mode', 'qrk' ) }
					</Button>
					{ editMode ? (
						<div { ...innerBlocksProps } />
					) : (
						<ServerSideRender
							block={ name }
							attributes={ { innerBlocks } }
							EmptyResponsePlaceholder={ () => (
								<Placeholder
									icon="format-image"
									label={ __( 'Hero Card Slider', 'qrk' ) }
									instructions={ __(
										'Please add some items to the hero details card slider.',
										'qrk',
									) }
								/>
							) }
						/>
					)
					}
				</Modal>
			}
		</>
	);
}
