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
 * Zustand.
 */
import './zustand';

/**
 * Global Store.
 */
import './store';
import { initialize } from './store/actions';

/**
 * TypeScript Stuff.
 */
declare global {
	interface Window {
		dynamicPhoneNumber: {
			api_endpoint?: string; // eslint-disable-line
			default_phone_number?: string; // eslint-disable-line
		};
	}
}

/**
 * Initialize global store.
 */
initialize( {} );

/**
 * Global JavaScript.
 */
import './phone-number';
import './tracking';
import './performance';