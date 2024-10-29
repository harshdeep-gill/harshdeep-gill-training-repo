/**
 * Campaign tracking.
 */
import { setCampaignIdsFromUrl } from '../store/actions';

/**
 * Internal dependencies.
 */
export {};

// Wait for DOM to load.
window.addEventListener(
	'DOMContentLoaded',
	() => {
		// Set campaign ids from URL.
		setCampaignIdsFromUrl();
	}
);
