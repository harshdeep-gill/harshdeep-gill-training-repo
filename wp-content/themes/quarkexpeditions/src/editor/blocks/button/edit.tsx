/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	Icon,
	RadioControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External dependencies.
 */
const { gumponents } = window;

/**
 * External components.
 */
const {
	LinkControl,
	ColorPaletteControl,
} = gumponents.components;

/**
 * Styles.
 */
import './editor.scss';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'btn',
			attributes.hasIcon ? 'btn--has-icon' : '',
			attributes.isSizeBig ? 'btn--size-big' : '',
			'black' === attributes.backgroundColor ? 'btn--color-black' : '',
			'white' === attributes.backgroundColor ? 'btn--outline' : '',
		),
	} );

	/**
	 * Get appearance options.
	 *
	 * @return {Array} Appearance options.
	 */
	const getAppearanceOptions = (): { label: string, value: string }[] => {
		// Default.
		return [
			{ label: __( 'Solid', 'qrk' ), value: 'solid' },
			{ label: __( 'Outline', 'qrk' ), value: 'outline' },
		];
	};

	/**
	 * Get color options.
	 *
	 * @return {Array} Color options.
	 */
	const getColorOptions = (): { name: string, color: string, slug: string }[] => {
		// Return color options based on the appearance.
		if ( 'outline' === attributes.appearance ) {
			// Return white color.
			return [
				{ name: __( 'White', 'qrk' ), color: '#fff', slug: 'white' },
			];
		}

		// Default.
		return [
			{ name: __( 'Yellow', 'qrk' ), color: '#fdb52b', slug: 'yellow' },
			{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
		];
	};

	/**
	 * Handle background color change.
	 *
	 * @param {string} backgroundColor Background color.
	 */
	const handleBackgroundColorChange = ( backgroundColor: string ) => {
		// Set the appearance based on the background color.
		if ( 'white' === backgroundColor ) {
			setAttributes( { appearance: 'outline' } );
		} else {
			setAttributes( { appearance: 'solid' } );
		}

		// Set the background color attribute.
		setAttributes( { backgroundColor } );
	};

	/**
	 * Handle appearance change.
	 *
	 * @param {string} appearance Appearance.
	 */
	const handleAppearanceChange = ( appearance: string ) => {
		// Set the background color based on the appearance.
		if ( 'outline' === appearance ) {
			setAttributes( { backgroundColor: 'white' } );
		} else {
			setAttributes( { backgroundColor: 'yellow' } );
		}

		// Set the appearance attribute.
		setAttributes( { appearance } );
	};

	// Appearance options.
	const appearanceOptions = getAppearanceOptions();

	// Color options.
	const colors = getColorOptions();

	// Trigger handlers to update the block attributes.
	handleBackgroundColorChange( attributes.backgroundColor );

	// Prepare icon.
	let selectedIcon: any = '';

	// Set icon.
	if ( attributes.icon && '' !== attributes.icon ) {
		// Kebab-case to camel-case.
		const iconName: string = attributes.icon.replace( /-([a-z])/g, ( _: any, group:string ) => group.toUpperCase() );
		selectedIcon = icons[ iconName ] ?? '';
	}

	// Fallback icon.
	if ( ! selectedIcon || '' === selectedIcon ) {
		selectedIcon = <Icon icon="no" />;
	}

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Button Options', 'qrk' ) }>
					<LinkControl
						label={ __( 'Select URL', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter an URL for this Button', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
					<RadioControl
						label={ __( 'Appearance', 'qrk' ) }
						help={ __( 'Select the appearance of the button.', 'qrk' ) }
						selected={ attributes.appearance }
						options={ appearanceOptions }
						onChange={ handleAppearanceChange }
					/>
					<ToggleControl
						label={ __( 'Is Size Big?', 'qrk' ) }
						checked={ attributes.isSizeBig }
						help={ __( 'Is this a big size button?', 'qrk' ) }
						onChange={ ( isSizeBig: boolean ) => setAttributes( { isSizeBig } ) }
					/>
					<ToggleControl
						label={ __( 'Has Icon?', 'qrk' ) }
						checked={ attributes.hasIcon }
						help={ __( 'Does the button have an icon?', 'qrk' ) }
						onChange={ ( hasIcon: boolean ) => setAttributes( { hasIcon } ) }
					/>
					{
						attributes.hasIcon &&
						<>
							<SelectControl
								label={ __( 'Icon', 'qrk' ) }
								help={ __( 'Select the icon.', 'qrk' ) }
								value={ attributes.icon }
								options={ [
									{ label: __( 'Select Icon…', 'qrk' ), value: '' },
									{ label: __( 'Phone', 'qrk' ), value: 'phone' },
								] }
								onChange={ ( icon: string ) => setAttributes( { icon } ) }
							/>
							<RadioControl
								label={ __( 'Icon Position', 'qrk' ) }
								help={ __( 'Select the icon position.', 'qrk' ) }
								selected={ attributes.iconPosition }
								options={ [
									{ label: __( 'Left', 'qrk' ), value: 'left' },
									{ label: __( 'Right', 'qrk' ), value: 'right' },
								] }
								onChange={ ( iconPosition: string ) => setAttributes( { iconPosition } ) }
							/>
						</>
					}
					<ColorPaletteControl
						label={ __( 'Button Color', 'qrk' ) }
						help={ __( 'Select the button color.', 'qrk' ) }
						value={ colors.find( ( color ) => color.slug === attributes.backgroundColor )?.color }
						colors={ colors.filter( ( color ) => [ 'black', 'yellow', 'white' ].includes( color.slug ) ) }
						onChange={ ( backgroundColor: {
							color: string;
							slug: string;
						} ): void => {
							// Set the background color attribute.
							if ( backgroundColor.slug && [ 'black', 'yellow', 'white' ].includes( backgroundColor.slug ) ) {
								setAttributes( { backgroundColor: backgroundColor.slug } );
							}

							// Handle the background color change.
							handleBackgroundColorChange( backgroundColor.slug );
						} }
					/>
				</PanelBody>
			</InspectorControls>
			<button { ...blockProps }>
				{
					'left' === attributes.iconPosition &&
					attributes.hasIcon &&
					<span className="btn__icon btn__icon-left">
						{ selectedIcon }
					</span>
				}
				<RichText
					tagName="span"
					className="btn__content"
					placeholder={ __( 'Button Text…', 'qrk' ) }
					value={ attributes.btnText }
					onChange={ ( btnText ) => setAttributes( { btnText } ) }
					allowedFormats={ [] }
				/>
				{
					'right' === attributes.iconPosition &&
					attributes.hasIcon &&
					<span className="btn__icon btn__icon-right">
						{ selectedIcon }
					</span>
				}
			</button>
		</>
	);
}
