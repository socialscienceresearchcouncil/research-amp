/**
 * Borrowed from https://github.com/fabiankaegy/gutenberg-post-picker
 *
 * Not loaded from NPM due to syntax issues
 */

import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {
	TextControl,
	Button,
	Spinner,
	TextHighlight,
	NavigableMenu,
} from '@wordpress/components';
import { safeDecodeURI, filterURLForDisplay } from '@wordpress/url';
import { decodeEntities } from '@wordpress/html-entities';

const namespace = 'gutenberg-post-picker';

/**
 * Post Picker
 *
 * @param {Object} props react props
 * @return {*} React JSX
 */
export const PostPicker = ( props ) => {
	const {
		disabled = false,
		hideLabelFromVision = false,
		onSelectPost,
		label = '',
		postTypes = [ 'posts', 'pages' ],
		placeholder = '',
	} = props;

	const [ searchString, setSearchString ] = useState( '' );
	const [ searchResults, setSearchResults ] = useState( [] );
	const [ isLoading, setIsLoading ] = useState( false );
	const [ selectedItem, setSelectedItem ] = useState( null );

	function handleItemSelection( post ) {
		onSelectPost( post );
		setSearchResults( [] );
		setSearchString( '' );
	}

	/**
	 * Using the keyword and the list of tags that are linked to the parent block
	 * search for posts that match and return them to the autocomplete component.
	 *
	 * @param {string} keyword search query string
	 */
	const handleSearchStringChange = ( keyword ) => {
		setSearchString( keyword );
		setIsLoading( true );

		Promise.all(
			postTypes.map( ( postType ) =>
				apiFetch( {
					path: `/wp/v2/${ postType }?search=${ keyword }`,
				} )
			)
		).then( ( results ) => {
			setSearchResults(
				results.reduce(
					( result, final ) => [ ...final, ...result ],
					[]
				)
			);
			setIsLoading( false );
		} );
	};

	function handleSelection( item ) {
		if ( item === 0 ) {
			setSelectedItem( null );
		}

		setSelectedItem( item );
	}

	return (
		<div className={ namespace }>
			<NavigableMenu
				onNavigate={ handleSelection }
				orientation={ 'vertical' }
			>
				<TextControl
					disabled={ disabled }
					hideLabelFromVision={ hideLabelFromVision }
					label={ label }
					value={ searchString }
					onChange={ handleSearchStringChange }
					placeholder={ placeholder }
				/>

				{ searchString.length ? (
					<ul
						className={ namespace + '-grid' }
						style={ {
							marginTop: '0',
							marginBottom: '0',
							marginLeft: '0',
							paddingLeft: '0',
							listStyle: 'none',
						} }
					>
						{ isLoading && <Spinner /> }
						{ ! isLoading && ! searchResults.length && (
							<li className={ namespace + '-grid-item' }>
								<Button disabled>
									{ __( 'No Items found', 'research-amp' ) }
								</Button>
							</li>
						) }
						{ searchResults.map( ( post, index ) => {
							if ( ! post.title.rendered.length ) {
								return null;
							}

							return (
								<li
									key={ post.id }
									className={ namespace + '-grid-item' }
									style={ {
										marginBottom: '0',
									} }
								>
									<SearchItem
										onClick={ () =>
											handleItemSelection( post )
										}
										searchTerm={ searchString }
										suggestion={ post }
										isSelected={
											selectedItem === index + 1
										}
									/>
								</li>
							);
						} ) }
					</ul>
				) : null }
			</NavigableMenu>
		</div>
	);
};

export function SelectedPostPreview( props ) {
	const { post, label } = props;

	const uniqueId = `${ post.slug }-preview`;
	return (
		<div
			style={ {
				display: 'flex',
				flexDirection: 'column',
			} }
		>
			<label htmlFor={ uniqueId }>{ label }</label>
			<SearchItem suggestion={ post } onClick={ null } id={ uniqueId } />
		</div>
	);
}

function SearchItem( props ) {
	const {
		suggestion,
		onClick,
		searchTerm = '',
		isSelected = false,
		id = '',
	} = props;

	return (
		<Button
			id={ id }
			onClick={ onClick }
			className={ `block-editor-link-control__search-item is-entity ${
				isSelected && 'is-selected'
			}` }
			style={ {
				borderRadius: '0',
			} }
		>
			<span className="block-editor-link-control__search-item-header">
				<span className="block-editor-link-control__search-item-title">
					<TextHighlight
						text={ decodeEntities( suggestion.title.rendered ) }
						highlight={ searchTerm }
					/>
				</span>
				<span
					aria-hidden={ true }
					className="block-editor-link-control__search-item-info"
				>
					{ filterURLForDisplay( safeDecodeURI( suggestion.link ) ) ||
						'' }
				</span>
			</span>
		</Button>
	);
}
