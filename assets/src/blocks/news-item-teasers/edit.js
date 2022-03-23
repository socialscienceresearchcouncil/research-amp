import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
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
		variationType
	} = attributes

	const { post } = useSelect( ( select ) => {
		let post = {}
		if ( featuredItemId ) {
			post = select( 'ramp' ).getPost( featuredItemId, 'posts' )
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
					<span>{ __( 'Currently Featured News Item: ', 'ramp' ) }</span>
					<div><a href={ postUrl }>{ postTitle }</a></div>
				</div>
			)
		: <div />

	const serverSideAtts = Object.assign( {}, attributes, { isEditMode: true } )

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Content Settings', 'ramp' ) }
					>
						<ContentModeControl
							changeCallback={ ( contentMode ) => setAttributes( { contentMode } ) }
							changeProfileIdCallback={ ( contentModeProfileId ) => setAttributes( { contentModeProfileId } ) }
							changeResearchTopicIdCallback={ ( contentModeResearchTopicId ) => setAttributes( { contentModeResearchTopicId } ) }
							glossAuto={ __( 'Show News Items relevant to the current Research Topic or Profile context.', 'ramp' ) }
							glossAll={ __( 'Pull from all News Items.', 'ramp' ) }
							glossAdvanced={__( 'Show News Items associated with a specific Research Topic or Profile.', 'ramp' )}
							labelAuto={ __( 'Relevant News Items', 'ramp' ) }
							labelAll={ __( 'All News Items', 'ramp' ) }
							legend={ __( 'Determine which News Items will be shown in this block.', 'ramp' ) }
							selectedMode={ contentMode }
							selectedProfileId={ contentModeProfileId }
							selectedResearchTopicId={ contentModeResearchTopicId }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Order and Pagination', 'ramp' ) }
					>
						<PanelRow>
							<SelectControl
								label={ __( 'Order', 'ramp' ) }
								options={ [
									{ label: __( 'Alphabetical', 'ramp' ), value: 'alphabetical' },
									{ label: __( 'Recently Added', 'ramp' ), value: 'latest' },
									{ label: __( 'Random', 'ramp' ), value: 'random' }
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

						<PanelRow>
							<LoadMoreToggle
								showLoadMore={ showLoadMore }
								onChangeCallback={ ( showLoadMore ) => setAttributes( { showLoadMore } ) }
							/>
						</PanelRow>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Display Options', 'ramp' ) }
					>
						<PanelRow>
							<PublicationDateToggle
								onChangeCallback={ ( showPublicationDate ) => setAttributes( { showPublicationDate } ) }
								showPublicationDate={ showPublicationDate }
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ ListIcon }
						isActive={ 'list' === variationType }
						label={ __( 'List', 'ramp' ) }
						onClick={ () => setAttributes( { variationType: 'list' } ) }
					/>
					<ToolbarButton
						icon={ GridIcon }
						isActive={ 'grid' === variationType }
						label={ __( 'Grid', 'ramp' ) }
						onClick={ () => setAttributes( { variationType: 'grid' } ) }
					/>
				</ToolbarGroup>
			</BlockControls>

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
