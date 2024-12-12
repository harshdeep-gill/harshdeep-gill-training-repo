/**
 * Yield to Main Thread.
 *
 * @return {Promise<void>}
 */
async function yieldToMain() {
	return new Promise( ( resolve ) => {
		setTimeout( resolve, 0 );
	} );
}

/**
 * Event listener abstract with yield to main thread.
 *
 * @param {HTMLElement} element
 * @param {string}      event
 * @param {Function}    callback
 * @param {boolean}     useCapture
 *
 * @return {void}
 */
function addEventListenerWithYieldToMain( element: HTMLElement | Document, event: string, callback: () => void, options: boolean | AddEventListenerOptions = false ) {
	if ( ! ( element instanceof HTMLElement ) || typeof callback !== 'function' ) {
		throw new Error( 'Invalid argument' );
	}

	element.addEventListener( event, async () => {
		await yieldToMain();
		callback();
	}, options );
}

// add in window object
window.yieldToMain = yieldToMain as () => Promise<void>;
window.addEventListenerWithYieldToMain = addEventListenerWithYieldToMain;