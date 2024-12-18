/**
 * Yield to Main Thread.
 *
 * @return {Promise<void>}
 */
async function yieldToMain(): Promise<void> {
	// Return a promise.
	return new Promise( ( resolve ) => {
		// Set timeout to resolve.
		setTimeout( resolve, 0 );
	} );
}

/**
 * Event listener abstract with yield to main thread.
 *
 * @param {HTMLElement} element
 * @param {string}      event
 * @param {Function}    callback
 * @param {boolean}     options
 * @return {void}
 */
function addEventListenerWithYieldToMain( element: HTMLElement | Document, event: string, callback: () => void, options: boolean | AddEventListenerOptions = false ): void {
	// Check if element is valid.
	if ( ! ( element instanceof HTMLElement ) || typeof callback !== 'function' ) {
		// Throw error.
		throw new Error( 'Invalid argument' );
	}

	// Add event listener.
	element.addEventListener( event, async () => {
		// Yield to main thread.
		await yieldToMain();
		callback();
	}, options );
}

// add in window object
window.yieldToMain = yieldToMain as () => Promise<void>;
window.addEventListenerWithYieldToMain = addEventListenerWithYieldToMain;
