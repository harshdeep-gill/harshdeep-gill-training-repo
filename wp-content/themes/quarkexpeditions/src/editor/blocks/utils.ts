/**
 * WordPress Dependencies.
 */
import { __ } from '@wordpress/i18n';

// Regular expression for YouTube video URLs.
export const YOUTUBE_VIDEO_URL_REGEX = /https?:\/\/(?:www\.)?(?:youtube\.com\/(v\/|e\/|watch[\/#?])|youtu\.be\/).*/i;

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

/**
 * Convert a YouTube URL to an Embed URL.
 *
 * @param {string} videoUrl Video URL.
 */
export function convertToEmbedUrl( videoUrl: string ): string {
	// Convert YouTube video url to embed URL.
	const videoId = getYouTubeVideoId( videoUrl );

	// If videoId exists, build embed url.
	if ( videoId ) {
		videoUrl = `https://www.youtube.com/embed/${ videoId }`;
	}

	// Return converted Video URL.
	return videoUrl;
}

/**
 * Extract YouTube video ID from URL.
 *
 * @param {string} videoURL YouTube video URL.
 *
 * @return {string|false} YouTube video ID or false if URL is not matching the regular expression.
 */
export const getYouTubeVideoId = ( videoURL: string ): string | false => {
	// Return false if video URL is not matching the regular expression.
	if ( ! YOUTUBE_VIDEO_URL_REGEX.test( videoURL ) ) {
		// Return false.
		return false;
	}

	// Create URL object.
	const url = new URL( videoURL );

	// If URL object is not valid, return false.
	if ( ! url || ! url.hostname || ! url.pathname || ! url.searchParams ) {
		// Return false.
		return false;
	}

	// Get query variables from URL object.
	const queryVars = Object.fromEntries( url.searchParams );

	/**
	 * If video ID is not found in query variables, return false.
	 * Example: https://www.youtube.com/watch?v=VIDEO_ID
	 */
	if ( queryVars.v || queryVars.vi ) {
		// Return video ID.
		return queryVars.v || queryVars.vi;
	}

	// Get segments from URL object pathname.
	const segments = url.pathname.split( '/' ).filter( Boolean );

	// If no segments are found, return false.
	if ( ! segments.length ) {
		// Return false.
		return false;
	}

	// For YouTube short URLs, video is the first path segment. Example: https://youtu.be/VIDEO_ID
	if ( 'youtu.be' === url.hostname ) {
		// Return video ID.
		return segments[ 0 ];
	}

	/**
	 * For YouTube long URLs, video id is the second path segment. Example: https://www.youtube.com/watch/VIDEO_ID
	 * Other top-level segments indicate non-video URLs. There are examples of URLs having segments including
	 * 'v', 'vi', and 'e' but these do not work anymore. In any case, they are added here for completeness.
	 */
	if ( [ 'watch', 'embed', 'v', 'vi', 'e' ].includes( segments[ 0 ] ) ) {
		// Return video ID.
		return segments[ 1 ];
	}

	// Return false.
	return false;
};

/**
 * Get formatted image details from a media object.
 *
 * @param {Object} media         Media object.
 * @param {string} thumbnailSize Thumbnail size.
 *
 * @return {Object} Formatted image details.
 */
export function getImageDetails( media: Record<string, any>, thumbnailSize: string ): Record<string, any> {
	// If media is not set, return empty object.
	if ( ! media ) {
		// Return empty object.
		return {};
	}

	// Initialize src, width, and height.
	let src, width, height;

	// If media has sizes, get the thumbnail size.
	if ( media.sizes ) {
		// If thumbnail size is not found, set thumbnail size to full.
		if ( ! media.sizes[ thumbnailSize ] ) {
			// Set thumbnail size to full.
			thumbnailSize = 'full';
		}
		width = media.sizes[ thumbnailSize ].width;
		height = media.sizes[ thumbnailSize ].height;
		src = media.sizes[ thumbnailSize ].url;
	}

	// Return formatted image details.
	return {
		id: media.id,
		src,
		width,
		height,
		alt: media.alt,
		caption: media.caption,
		title: media.title,
		size: thumbnailSize,
	};
}
