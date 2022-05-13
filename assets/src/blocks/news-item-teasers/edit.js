import { __ } from '@wordpress/i18n'

import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
	Toolbar,
	ToolbarButton,
	ToolbarGroup
} from '@wordpress/components'

import { usePrevious } from '@wordpress/compose'

import {
	BlockControls,
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

import {
	Fragment
} from '@wordpress/element'

import { PostPicker } from '../../components/PostPicker'

import ContentModeControl from '../../components/ContentModeControl'
import PublicationDateToggle from '../../components/PublicationDateToggle'
import LoadMoreToggle from '../../components/LoadMoreToggle'
import NumberOfItemsControl from '../../components/NumberOfItemsControl'

import { GridIcon } from '../../icons/Grid'
import { ListIcon } from '../../icons/List'

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
		numberOfItems,
		order,
		showFeaturedItem,
		showLoadMore,
		showPublicationDate,
		showVariationTypeButtons,
		variationType
	} = attributes

	const { post } = useSelect( ( select ) => {
		let post = {}
		if ( featuredItemId ) {
			post = select( 'research-amp' ).getPost( featuredItemId, 'posts' )
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

	const blockProps = () => {
		let classNames = []

		classNames.push( 'featured-item-id-' + featuredItemId )
		classNames.push( 'content-mode-' + contentMode )
		classNames.push( 'variation-type-' + variationType )

		return useBlockProps( {
			className: classNames
		} )
	}

	const currentlyFeaturedNotice =
		postUrl
		? ( <div className="currently-featured-notice">
					<span>{ __( 'Currently Featured News Item: ', 'research-amp' ) }</span>
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
	const showFeaturedItemToggle = !! showVariationTypeButtons

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Content Settings', 'research-amp' ) }
					>
						<ContentModeControl
							changeCallback={ ( contentMode ) => setAttributes( { contentMode } ) }
							changeProfileIdCallback={ ( contentModeProfileId ) => setAttributes( { contentModeProfileId } ) }
							changeResearchTopicIdCallback={ ( contentModeResearchTopicId ) => setAttributes( { contentModeResearchTopicId } ) }
							glossAuto={ __( 'Show News Items relevant to the current Research Topic or Profile context.', 'research-amp' ) }
							glossAll={ __( 'Pull from all News Items.', 'research-amp' ) }
							glossAdvanced={__( 'Show News Items associated with a specific Research Topic or Profile.', 'research-amp' )}
							labelAuto={ __( 'Relevant News Items', 'research-amp' ) }
							labelAll={ __( 'All News Items', 'research-amp' ) }
							legend={ __( 'Determine which News Items will be shown in this block.', 'research-amp' ) }
							selectedMode={ contentMode }
							selectedProfileId={ contentModeProfileId }
							selectedResearchTopicId={ contentModeResearchTopicId }
						/>
					</PanelBody>
				</Panel>

				{ showFeaturedItemToggle && (
					<Panel>
						<PanelBody
							title={ __( 'Featured News Item', 'research-amp' ) }
						>
							<ToggleControl
								label={ __( 'Show a Featured News Item?', 'research-amp' ) }
								checked={ showFeaturedItem }
								onChange={ ( showFeaturedItem ) => setAttributes( { showFeaturedItem } ) }
							/>

							{ showFeaturedItem && (
								<>
									{ currentlyFeaturedNotice }

									<PostPicker
										onSelectPost={ ( selectedPost ) => setAttributes( { featuredItemId: selectedPost.id } ) }
										label={ __( 'Select a Featured News Item', 'research-amp' ) }
										placeholder={ __( 'Start typing to search.', 'research-amp' ) }
										postTypes={ [ 'posts' ] }
									/>
								</>
							) }
						</PanelBody>
					</Panel>
				) }

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

						<PanelRow>
							<NumberOfItemsControl
								numberOfItems={ numberOfItems }
								onChangeCallback={ ( numberOfItems ) => setAttributes( { numberOfItems } ) }
							/>
						</PanelRow>

						{ showLoadMoreToggle && (
							<PanelRow>
								<LoadMoreToggle
									showLoadMore={ showLoadMore }
									onChangeCallback={ ( showLoadMore ) => setAttributes( { showLoadMore } ) }
								/>
							</PanelRow>
						) }
					</PanelBody>
				</Panel>

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
					</ToolbarGroup>
				</BlockControls>
			) }

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="ramp/news-item-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	)
}
