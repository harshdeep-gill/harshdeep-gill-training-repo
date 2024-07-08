/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * QuarkFileInput Class.
 */
export default class QuarkFileInput extends HTMLElement {
	/**
	 * Properties
	 */
	private fileInput: HTMLInputElement | null;
	private discardFileBtn: HTMLButtonElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize
		super();
		this.fileInput = null;
		this.discardFileBtn = null;
	}

	/**
	 * Connected callback.
	 */
	connectedCallback(): void {
		// Get the file input.
		this.fileInput = this.querySelector( 'input[type="file"]' );
		this.discardFileBtn = this.querySelector( '.quark-file-input__discard' );

		// Events
		this.fileInput?.addEventListener( 'change', this.handleFileChange.bind( this ) );
		this.discardFileBtn?.addEventListener( 'click', this.handleFileDiscard.bind( this ) );
	}

	/**
	 * Handles the file selection.
	 */
	handleFileChange(): void {
		// Check if file is selected.
		if ( ! ( this.fileInput && this.fileInput.files && 1 === this.fileInput.files.length ) ) {
			// No file selected, remove attribute.
			this.removeAttribute( 'file_selected' );

			// bail.
			return;
		}

		// File is selected, add attribute.
		this.setAttribute( 'file_selected', 'yes' );

		// Get the file info.
		const file = this.fileInput.files.item( 0 );

		// Populate file info.
		this.populateFileInfo( file );
	}

	/**
	 * Handles the file discard button.
	 */
	handleFileDiscard(): void {
		// Check if file is selected.
		if ( ! ( this.fileInput && this.fileInput.files && 1 === this.fileInput.files.length ) ) {
			// bail.
			return;
		}

		// Remove attribute.
		this.removeAttribute( 'file_selected' );

		// Reset the file input.
		this.fileInput.value = '';
	}

	/**
	 * Populate the information of the file in preview.
	 *
	 * @param { File | null } file The file to consider.
	 */
	populateFileInfo( file: File | null ) {
		// Check if no file.
		if ( ! file ) {
			// bail.
			return;
		}

		// File info elements.
		const fileMimetypeElement = this.querySelector( '.quark-file-input__mime-type p' );
		const fileNameElement = this.querySelector( '.quark-file-input__file-name' );
		const fileSizeElement = this.querySelector( '.quark-file-input__file-size' );

		// Null check.
		if ( fileMimetypeElement ) {
			// Set mimetype.
			const mimeTypeSplit = file.type.split( '/' );
			fileMimetypeElement.textContent = mimeTypeSplit[ mimeTypeSplit.length - 1 ];
		}

		// Null check.
		if ( fileNameElement ) {
			// Set mimetype.
			fileNameElement.textContent = file.name.substring( 0, 20 ).concat( '…' );
		}

		// Null check.
		if ( fileSizeElement ) {
			// Initialize file size text.
			let fileSizeText = '';

			// Check file size under 1 MB.
			if ( 1024 ** 2 > file.size ) {
				fileSizeText = ( file.size / 1024 ).toFixed( 2 ).toString() + ' KB';
			} else {
				fileSizeText = ( file.size / ( 1024 ** 2 ) ).toFixed( 2 ).toString() + ' MB';
			}

			// Set the file size text.
			fileSizeElement.textContent = fileSizeText;
		}
	}
}
