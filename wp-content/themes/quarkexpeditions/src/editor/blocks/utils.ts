/**
 * WordPress Dependencies.
 */
import { __ } from '@wordpress/i18n';

/**
 * Returns the background colors available.
 */
export function getAllBackgroundColors(): { [key: string]: string }[] {
	// Return the values.
	return [
		{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
		{ name: __( 'White', 'qrk' ), color: '#fff', slug: 'white' },
		{ name: __( 'Yellow', 'qrk' ), color: '#fdb52b', slug: 'yellow' },
		{ name: __( 'Dark blue', 'qrk' ), color: '#2a5f8c', slug: 'dark-blue' },
		{ name: __( 'Blue', 'qrk' ), color: '#4c8bbf', slug: 'blue' },
		{ name: __( 'Magenta', 'qrk' ), color: '#a26792', slug: 'magenta' },
		{ name: __( 'Gray 90', 'qrk' ), color: '#383d49', slug: 'gray-90' },
		{ name: __( 'Gray 80', 'qrk' ), color: '#454c5b', slug: 'gray-80' },
		{ name: __( 'Gray 70', 'qrk' ), color: '#535b6d', slug: 'gray-70' },
		{ name: __( 'Gray 60', 'qrk' ), color: '#6c768e', slug: 'gray-60' },
		{ name: __( 'Gray 50', 'qrk' ), color: '#868fa3', slug: 'gray-50' },
		{ name: __( 'Gray 40', 'qrk' ), color: '#a8aebd', slug: 'gray-40' },
		{ name: __( 'Gray 30', 'qrk' ), color: '#c9cdd6', slug: 'gray-30' },
		{ name: __( 'Gray 20', 'qrk' ), color: '#dadee5', slug: 'gray-20' },
		{ name: __( 'Gray 10', 'qrk' ), color: '#eceef2', slug: 'gray-10' },
		{ name: __( 'Gray 5', 'qrk' ), color: '#f5f7fb', slug: 'gray-5' },
		{ name: __( 'Success 100', 'qrk' ), color: '#3a735d', slug: 'success-100' },
		{ name: __( 'Success 50', 'qrk' ), color: '#5bb291', slug: 'success-50' },
		{ name: __( 'Success 10', 'qrk' ), color: '#e6f2ee', slug: 'success-10' },
		{ name: __( 'Attention 100', 'qrk' ), color: '#c77413', slug: 'attention-100' },
		{ name: __( 'Attention 50', 'qrk' ), color: '#f29b34', slug: 'attention-50' },
		{ name: __( 'Attention 10', 'qrk' ), color: '#ffe5c7', slug: 'attention-10' },
		{ name: __( 'Error 100', 'qrk' ), color: '#bf483b', slug: 'error-100' },
		{ name: __( 'Error 50', 'qrk' ), color: '#df5748', slug: 'error-50' },
		{ name: __( 'Error 10', 'qrk' ), color: '#fdddd9', slug: 'error-10' },
		{ name: __( 'Information 100', 'qrk' ), color: '#4b5059', slug: 'information-100' },
		{ name: __( 'Information 50', 'qrk' ), color: '#808999', slug: 'information-50' },
		{ name: __( 'Information 10', 'qrk' ), color: '#fafbff', slug: 'information-10' },
	];
}
