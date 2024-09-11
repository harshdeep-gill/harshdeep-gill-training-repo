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
		};
	}
}

/**
 * Initialize global store.
 */
initialize( {} );
