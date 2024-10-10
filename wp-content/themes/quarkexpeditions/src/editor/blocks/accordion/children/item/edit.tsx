/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
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
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'accordion__item' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( className, 'accordion__content-inner' ),
	}, {
		template: [
			[ 'core/paragraph', { placeholder: __( 'Content…', 'qrk' ) } ],
		],

		// @ts-ignore
		orientation: 'vertical',
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Accordion Item Options.', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Is Open?', 'qrk' ) }
						checked={ attributes.isOpen }
						onChange={ ( isOpen: boolean ) => setAttributes( { isOpen } ) }
						help={ __( 'Should this accordion item be open by default.', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blocksProps }>
				<div className="accordion__handle">
					<button className="accordion__handle-btn">
						<RichText
							tagName="p"
							className={ 'h5 accordion__handle-btn-text body-text-large' }
							placeholder={ __( 'Write title…', 'qrk' ) }
							value={ attributes.title }
							onChange={ ( title: string ) => setAttributes( { title } ) }
							allowedFormats={ [] }
						/>
						<span className="accordion__handle-icon">
							{ icons.chevronLeft }
						</span>
					</button>
				</div>
				<div className="accordion__content">
					<div { ...innerBlockProps } />
				</div>
			</div>
		</>
	);
}
