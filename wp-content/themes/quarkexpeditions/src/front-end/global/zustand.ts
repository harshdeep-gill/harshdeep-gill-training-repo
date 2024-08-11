/**
 * External dependencies.
 */
import { persist } from 'zustand/middleware';
import { createStore } from 'zustand/vanilla';

/**
 * Create stores.
 */
const stores = {};
const zustand = {
	persist,
	create: createStore,
	stores,
};

/**
 * Export Zustand.
 */
window.zustand = zustand;
export default zustand;
