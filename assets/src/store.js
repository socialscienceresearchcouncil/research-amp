import apiFetch from '@wordpress/api-fetch'
import { registerStore } from '@wordpress/data'

const DEFAULT_STATE = {
	articles: [],
	libraries: {},
	profiles: [],
	rampPosts: {},
	researchTopics: []
}

const STORE_NAME = 'research-amp'

const actions = {
	fetchFromAPI( path ) {
		return {
			type: 'FETCH_FROM_API',
			path
		}
	},

	setLibraryInfo( libraryId, libraryInfo ) {
		return {
			type: 'SET_LIBRARY_INFO',
			libraryId,
			libraryInfo
		}
	},

	setPost( postId, post ) {
		return {
			type: 'SET_POST',
			postId,
			post
		}
	},

	setArticles( articles ) {
		return {
			type: 'SET_ARTICLES',
			articles
		}
	},

	setProfiles( profiles ) {
		return {
			type: 'SET_PROFILES',
			profiles
		}
	},

	setResearchTopics( researchTopics ) {
		return {
			type: 'SET_RESEARCH_TOPICS',
			researchTopics
		}
	}
}

const reducer = ( state = DEFAULT_STATE, action ) => {
	switch ( action.type ) {
		case 'SET_LIBRARY_INFO' :
			return {
				...state,
				libraries: {
					...state.libraries,
					[ action.libraryId ]: action.libraryInfo
				}
			}

		case 'SET_POST' :
			return {
				...state,
				rampPosts: {
					...state.rampPosts,
					[ action.postId ]: action.post
				}
			}

		case 'SET_ARTICLES' :
			return {
				...state,
				articles: action.articles
			}

		case 'SET_PROFILES' :
			return {
				...state,
				profiles: action.profiles
			}

		case 'SET_RESEARCH_TOPICS' :
			return {
				...state,
				researchTopics: action.researchTopics
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
	getArticles( state ) {
		const { articles } = state
		return articles
	},

	getLibraryInfo( state, libraryId ) {
		const { libraries } = state
		const libraryInfo = libraries[ libraryId ]

		return libraryInfo
	},

	getPost( state, postId ) {
		const { rampPosts } = state
		const post = rampPosts[ postId ]

		return post
	},

	getProfiles( state ) {
		const { profiles } = state

		return profiles
	},

	getResearchTopics( state ) {
		const { researchTopics } = state

		return researchTopics
	},
}

const resolvers = {
	*getLibraryInfo( libraryId ) {
		const path = '/research-amp/v1/zotero-library-info/' + libraryId
		const libraryInfo = yield actions.fetchFromAPI( path )
		return actions.setLibraryInfo( libraryId, libraryInfo )
	},

	*getProfiles() {
		const path = '/wp/v2/profiles?per_page=-1&orderby=title&order=asc&context=edit'
		const profiles = yield actions.fetchFromAPI( path )
		return actions.setProfiles( profiles )
	},

	*getResearchTopics() {
		const path = '/wp/v2/research-topics?per_page=50&orderby=title&order=asc&context=edit'
		const researchTopics = yield actions.fetchFromAPI( path )
		return actions.setResearchTopics( researchTopics )
	},

	*getArticles() {
		const path = '/wp/v2/articles?per_page=100&orderby=title&order=asc'
		const articles = yield actions.fetchFromAPI( path )
		return actions.setArticles( articles )
	},

	*getPost( postId, postType ) {
		const path = '/wp/v2/' + postType + '/' + postId
		const post = yield actions.fetchFromAPI( path )
		return actions.setPost( postId, post )
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
