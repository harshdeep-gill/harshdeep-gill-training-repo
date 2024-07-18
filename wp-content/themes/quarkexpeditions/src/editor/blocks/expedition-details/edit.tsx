/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/expedition-details';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'expedition-details color-context--dark typography-spacing'
		),
	} );

	// Return the markup.
	return (
		<div { ...blockProps }>
			<div className="expedition-details__overline overline">
				<span>{ __( 'ANTARCTIC, ARCTIC', 'qrk' ) }x</span>
				<span>{ __( 'From XX days', 'qrk' ) }</span>
				<span>{ __( 'From $ X,XXX USD', 'qrk' ) }</span>
			</div>
			<h2 className="expedition-details__title h1">
				{ __( 'Expedition Name', 'qrk' ) }
			</h2>
			<ul className="expedition-details__regions h4">
				<li className="expedition-details__region">
					{ __( 'Expedition Tag 1', 'qrk' ) }
				</li>
				<li className="expedition-details__region">
					{ __( 'Expedition Tag 2', 'qrk' ) }
				</li>
				<li className="expedition-details__region">
					{ __( 'Expedition Tag', 'qrk' ) }
				</li>
				<li className="expedition-details__region">
					{ __( 'Expedition Tag 4', 'qrk' ) }
				</li>
			</ul>
			<div className="expedition-details__row grid">
				<div className="expedition-details__starting-from">
					<p className="expedition-details__starting-from-label">{ __( 'Starting from', 'qrk' ) }</p>
					<div className="expedition-details__starting-from-content">
						{ __( 'Location Name, Location 2 Name', 'qrk' ) }
					</div>
				</div>
				<div className="expedition-details__ships">
					<p className="expedition-details__ships-label">{ __( 'Ships', 'qrk' ) }</p>
					<div className="expedition-details__ships-content">
						{ __( 'Ship 1, Ship 2', 'qrk' ) }
					</div>
				</div>
			</div>
			<div className="expedition-details__row grid">
				<div className="expedition-details__departures">
					<p className="expedition-details__departures-label">{ __( 'Departures', 'qrk' ) }</p>
					<div className="expedition-details__departures-content">
						<span>{ __( 'XX Departures between', 'qrk' ) }</span>
						<span>{ __( 'Month 20XX to ', 'qrk' ) }</span>
						<span>{ __( 'Month 20XX', 'qrk' ) }</span>
					</div>
				</div>
			</div>
			<div className="expedition-details__cta">
				<a href="#bookings" className="btn btn--color-black btn--size-big" target="_self">
					{ __( 'View All Departures', 'qrk' ) }
				</a>
			</div>
		</div>
	);
}
