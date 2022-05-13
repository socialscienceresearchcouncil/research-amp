import { __ } from '@wordpress/i18n';

import { usePrevious } from '@wordpress/compose'

import {
	Panel,
	PanelBody,
	PanelRow,
	Spinner,
	SelectControl,
	ToggleControl,
	Toolbar,
	ToolbarButton,
	ToolbarGroup
} from '@wordpress/components'

import {
	BlockControls,
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

import { PostPicker } from '../../components/PostPicker'
import ContentModeControl from '../../components/ContentModeControl'
import PublicationDateToggle from '../../components/PublicationDateToggle'
import HorizontalSwipeToggle from '../../components/HorizontalSwipeToggle'
import LoadMoreToggle from '../../components/LoadMoreToggle'
import NumberOfItemsControl from '../../components/NumberOfItemsControl'

import { GridIcon } from '../../icons/Grid'
import { ListIcon } from '../../icons/List'
import { FeaturedIcon } from '../../icons/Featured'

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @return {WPElement} Element to render.
 */
export default function edit( {
	attributes,
	setAttributes,
} ) {
	const {
		contentMode,
		contentModeProfileId,
		contentModeResearchTopicId,
		featuredItemId,
		horizontalSwipe,
		numberOfItems,
		order,
		showLoadMore,
		showPublicationDate,
		showVariationTypeButtons,
		variationType
	} = attributes

	const blockProps = () => {
		let classNames = []

		classNames.push( 'featured-item-id-' + featuredItemId )
		classNames.push( 'content-mode-' + contentMode )
		classNames.push( 'variation-type-' + variationType )

		return useBlockProps( {
			className: classNames
		} )
	}

	const { post } = useSelect( ( select ) => {
		let post = {}
		if ( featuredItemId ) {
			post = select( 'research-amp' ).getPost( featuredItemId, 'articles' )
		}

		return {
			post
		}
	}, [ featuredItemId ] )

	let postUrl, postTitle
	if ( post && post.hasOwnProperty( 'title' ) ) {
		postTitle = post.title.rendered
		postUrl = post.link
	}

	const currentlyFeaturedNotice =
		postUrl
		? ( <div className="currently-featured-notice">
					<span>{ __( 'Currently Featured Article: ', 'research-amp' ) }</span>
					<div><a href={ postUrl }>{ postTitle }</a></div>
				</div>
			)
		: <div />

	const serverSideAtts = Object.assign( {}, attributes, {
		isEditMode: true,
		forceRefresh: featuredItemId !== usePrevious( featuredItemId ) // Addresses race condition with useSelect() and ServerSideRender()
	} )

	// Use showVariationTypeButtons as a heuristic for showing other toggles
	const showPublicationDateToggle = !! showVariationTypeButtons
	const showLoadMoreToggle = !! showVariationTypeButtons

	return (
		<>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Content Settings', 'research-amp' ) }
					>
						<ContentModeControl
							changeCallback={ ( contentMode ) => setAttributes( { contentMode } ) }
							changeProfileIdCallback={ ( profileObj ) => setAttributes( { contentModeProfileId: profileObj.id } ) }
							changeResearchTopicIdCallback={ ( contentModeResearchTopicId ) => setAttributes( { contentModeResearchTopicId } ) }
							glossAuto={ __( 'Show Articles relevant to the current Research Topic or Profile context.', 'research-amp' ) }
							glossAll={ __( 'Pull from all Articles.', 'research-amp' ) }
							glossAdvanced={__( 'Show Articles associated with a specific Research Topic or Profile.', 'research-amp' )}
							labelAuto={ __( 'Relevant Articles', 'research-amp' ) }
							labelAll={ __( 'All Articles', 'research-amp' ) }
							legend={ __( 'Determine which Articles will be shown in this block.', 'research-amp' ) }
							selectedMode={ contentMode }
							selectedProfileId={ contentModeProfileId }
							selectedResearchTopicId={ contentModeResearchTopicId }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Order and Pagination', 'research-amp' ) }
					>
						<PanelRow>
							<SelectControl
								label={ __( 'Order', 'research-amp' ) }
								options={ [
									{ label: __( 'Alphabetical', 'research-amp' ), value: 'alphabetical' },
									{ label: __( 'Recently Added', 'research-amp' ), value: 'latest' },
									{ label: __( 'Random', 'research-amp' ), value: 'random' }
								] }
								value={ order }
								onChange={ ( order ) => setAttributes( { order } ) }
							/>
						</PanelRow>

						{ 'featured' !== variationType && (
							<PanelRow>
								<NumberOfItemsControl
									numberOfItems={ numberOfItems }
									onChangeCallback={ ( numberOfItems ) => setAttributes( { numberOfItems } ) }
								/>
							</PanelRow>
						) }

						{ 'featured' !== variationType && showLoadMoreToggle && (
							<PanelRow>
								<LoadMoreToggle
									numberOfItems={ numberOfItems }
									showLoadMore={ 'featured' === variationType ? false : showLoadMore }
									onChangeCallback={ ( showLoadMore ) => setAttributes( { showLoadMore } ) }
								/>
							</PanelRow>
						) }
					</PanelBody>
				</Panel>

				{ 'featured' === variationType && (
				<Panel>
					<PanelBody
						title={ __( 'Featured Article', 'research-amp' ) }
					>
						{ currentlyFeaturedNotice }

						<PostPicker
							onSelectPost={ ( selectedPost ) => setAttributes( { featuredItemId: selectedPost.id } ) }
							label={ __( 'Select a Featured Article', 'research-amp' ) }
							placeholder={ __( 'Start typing to search.', 'research-amp' ) }
							postTypes={ [ 'articles' ] }
						/>
					</PanelBody>
				</Panel>
				) }

				{ showPublicationDateToggle && (
					<Panel>
						<PanelBody
							title={ __( 'Display Options', 'research-amp' ) }
						>
							<PanelRow>
								<PublicationDateToggle
									onChangeCallback={ ( showPublicationDate ) => setAttributes( { showPublicationDate } ) }
									showPublicationDate={ showPublicationDate }
								/>
							</PanelRow>

							{ 'grid' === variationType && (
								<PanelRow>
									<HorizontalSwipeToggle
										onChangeCallback={ ( horizontalSwipe ) => setAttributes( { horizontalSwipe } ) }
										horizontalSwipe={ horizontalSwipe }
									/>
								</PanelRow>
							) }
						</PanelBody>
					</Panel>
				) }
			</InspectorControls>

			{ showVariationTypeButtons && (
				<BlockControls>
					<ToolbarGroup>
						<ToolbarButton
							icon={ ListIcon }
							isActive={ 'list' === variationType }
							label={ __( 'List', 'research-amp' ) }
							onClick={ () => setAttributes( { variationType: 'list' } ) }
						/>
						<ToolbarButton
							icon={ GridIcon }
							isActive={ 'grid' === variationType }
							label={ __( 'Grid', 'research-amp' ) }
							onClick={ () => setAttributes( { variationType: 'grid' } ) }
						/>
						<ToolbarButton
							icon={ FeaturedIcon }
							isActive={ 'featured' === variationType }
							label={ __( 'Featured', 'research-amp' ) }
							onClick={ () => setAttributes( { variationType: 'featured' } ) }
						/>
					</ToolbarGroup>
				</BlockControls>
			) }

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="research-amp/article-teasers"
					className={"featured-item-id-" + featuredItemId}
					httpMethod="GET"
				/>
			</div>
		</>
	)
}
