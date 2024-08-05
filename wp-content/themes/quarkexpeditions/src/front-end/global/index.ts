/**
 * Global JavaScript.
 */
import './phone-number';

/**
 * Styles.
 */
import './index.scss';

/**
 * Internal dependencies.
 */
import './dom-content-loaded';

/**
 * Fetch Partial.
 */
import './fetch-partial';

/**
 * TypeScript Stuff.
 */
declare global {
	interface Window {
		dynamicPhoneNumber: {
			api_endpoint?: string; // eslint-disable-line
		};
	}
}
