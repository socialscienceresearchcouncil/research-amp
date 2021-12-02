import apiFetch from '@wordpress/api-fetch'
import { registerStore } from '@wordpress/data'

const DEFAULT_STATE = {
	blockMarkup: {},
	libraries: {}
}

const STORE_NAME = 'ramp'

const actions = {
	fetchFromAPI( path ) {
		return {
			type: 'FETCH_FROM_API',
			path
		}
	},

	setBlockMarkup( blockType, blockMarkup ) {
		return {
			type: 'SET_BLOCK_MARKUP',
			blockType,
			blockMarkup
		}
	},

	setLibraryInfo( libraryId, libraryInfo ) {
		return {
			type: 'SET_LIBRARY_INFO',
			libraryId,
			libraryInfo
		}
	}
}

const reducer = ( state = DEFAULT_STATE, action ) => {
	switch ( action.type ) {
		case 'SET_BLOCK_MARKUP' :
			return {
				...state,
				blockMarkup: {
					...state.blockMarkup,
					[ action.blockType ]: action.blockMarkup
				}
			}

		case 'SET_LIBRARY_INFO' :
			return {
				...state,
				libraries: {
					...state.libraries,
					[ action.libraryId ]: action.libraryInfo
				}
			}

		default :
			return state
	}
}

const controls = {
	FETCH_FROM_API( action ) {
		return apiFetch( { path: action.path } )
	},
}

const selectors = {
	getLibraryInfo( state, libraryId ) {
		const { libraries } = state
		const libraryInfo = libraries[ libraryId ]

		return libraryInfo
	},

	getBlockMarkup( state, blockType ) {
		const { blockMarkup } = state
		const blockTypeMarkup = blockMarkup.hasOwnProperty( blockType ) ? blockMarkup[ blockType ] : ''
		return blockTypeMarkup
	}
}

const resolvers = {
	*getLibraryInfo( libraryId ) {
		const path = '/ramp/v1/zotero-library/' + libraryId
		const libraryInfo = yield actions.fetchFromAPI( path )
		return actions.setLibraryInfo( libraryId, libraryInfo )
	},

	*getBlockMarkup( blockType ) {
		const path = '/ramp/v1/block-markup/?blockType=' + blockType
		const blockTypeMarkup = yield actions.fetchFromAPI( path )
		return actions.setBlockMarkup( blockType, blockTypeMarkup )
	}
}

const storeConfig = {
	actions,
	reducer,
	controls,
	selectors,
	resolvers
}

registerStore( STORE_NAME, storeConfig )

export { STORE_NAME }
