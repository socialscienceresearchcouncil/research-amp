import {
	Button,
	NavigableMenu,
	Spinner,
	TextControl,
	TextHighlight,
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';
import { useEffect, useRef, useState } from '@wordpress/element';

import { decodeEntities } from '@wordpress/html-entities';

const decodeAllEntities = ( value ) => {
	return decodeEntities( value ).replace( '&amp;', '&' );
};

const SearchItem = ( props ) => {
	const {
		suggestion,
		onClick,
		searchTerm = '',
		isSelected = false,
		id = '',
	} = props;

	let buttonClassName = 'block-editor-link-control__search-item is-entity';
	if ( isSelected ) {
		buttonClassName += ' is-selected';
	}

	return (
		<Button
			id={ id }
			onClick={ onClick }
			className={ buttonClassName }
			style={ { borderRadius: '0' } }
		>
			<span className="block-editor-link-control__search-item-header">
				<span className="block-editor-link-control__search-item-title">
					<TextHighlight
						text={ decodeAllEntities( suggestion.name ) }
						highlight={ searchTerm }
					/>
				</span>
			</span>
		</Button>
	);
};

export const TaxonomyTermSearchControl = ( props ) => {
	const { label, onSelect, taxonomy = '', value = '' } = props;

	const [ searchString, setSearchString ] = useState( value );
	const [ suggestions, setSuggestions ] = useState( [] );
	const [ isLoading, setIsLoading ] = useState( false );
	const [ selectedItem, setSelectedItem ] = useState( null );

	const abortControllerRef = useRef( null );
	const timeoutId = useRef( null );

	const handleSearchStringChange = async ( keyword ) => {
		setSearchString( keyword );

		// Cancel the previous request.
		if ( abortControllerRef.current ) {
			abortControllerRef.current.abort();
		}

		abortControllerRef.current =
			typeof window !== 'undefined' && window.AbortController
				? new window.AbortController()
				: undefined;

		// Clear the timeout if it's already set.
		if ( timeoutId.current ) {
			clearTimeout( timeoutId.current );
		}

		// Set a new timeout
		timeoutId.current = setTimeout( async () => {
			try {
				setIsLoading( true );

				const path = `/wp/v2/${ taxonomy }?search=${ keyword }`;

				const response = await apiFetch( { path } );

				if ( response ) {
					setSuggestions( response );
					setIsLoading( false );
				}
			} catch ( error ) {}
		}, 500 );
	};

	useEffect( () => {
		return () => {
			if ( timeoutId.current ) {
				clearTimeout( timeoutId.current );
			}

			if ( abortControllerRef.current ) {
				abortControllerRef.current.abort();
			}
		};
	}, [] );

	const handleSelectItem = ( item ) => {
		onSelect( decodeAllEntities( item.name ) );
		setSearchString( decodeAllEntities( item.name ) );
		setSuggestions( [] );
	};

	const handleHighlightChange = ( item ) => {
		if ( item === 0 ) {
			setSelectedItem( null );
		}

		setSelectedItem( item );
	};

	return (
		<NavigableMenu
			onNavigate={ handleHighlightChange }
			orientation={ 'vertical' }
		>
			<TextControl
				label={ label }
				value={ searchString }
				onChange={ handleSearchStringChange }
				autoComplete="off"
			/>

			{ searchString.length > 0 && (
				<ul
					style={ {
						marginTop: '0',
						marginBottom: '0',
						marginLeft: '0',
						paddingLeft: '0',
						listStyle: 'none',
					} }
				>
					{ isLoading && <Spinner /> }

					{ suggestions.map( ( term, index ) => {
						return (
							<li
								key={ term.id }
								className={ `foo-grid-item` }
								style={ { marginBottom: 0 } }
							>
								<SearchItem
									onClick={ () => handleSelectItem( term ) }
									searchTerm={ searchString }
									suggestion={ term }
									isSelected={ selectedItem === index + 1 }
								/>
							</li>
						);
					} ) }
				</ul>
			) }
		</NavigableMenu>
	);
};
