/**
 * Phone Number.
 */
import { setPhoneNumberRule } from '../store/actions';

// Check if dynamic phone number data exists.
const { dynamicPhoneNumber, zustand } = window;

/**
 * Internal dependencies.
 */
export {};

// Wait for DOM to load.
window.addEventListener(
	'DOMContentLoaded',
	() => {
		// Check if we have dynamic phone number data.
		if ( ! dynamicPhoneNumber || ! dynamicPhoneNumber?.api_endpoint ) {
			// No dynamic phone number data found, return.
			return;
		}

		// Get global state.
		const { getState } = zustand.stores.global;

		// Get phone number rule from global state.
		const { phoneNumberRule }: GlobalState = getState();

		// Check if phone number rule exists.
		if ( phoneNumberRule && phoneNumberRule.phoneNumber && phoneNumberRule.prefix ) {
			// Phone number rule exists, return.
			return;
		}

		// Get phone number for this URL.
		fetch(
			`${ dynamicPhoneNumber.api_endpoint }`,
			{
				method: 'POST',
				cache: 'no-cache',
				headers: {
					'Content-Type': 'application/json',
				},
			}
		)
			.then( ( response ) => response.json() )
			.then( ( rule ) => {
				// Update phone number key.
				rule.phoneNumber = rule.phone_number;

				// Delete phone number key.
				delete rule.phone_number;

				// Set phone number rule in global state.
				setPhoneNumberRule( rule );
			} )
			.catch( () => {
				// Error fetching phone number, return.
			} );
	}
);
