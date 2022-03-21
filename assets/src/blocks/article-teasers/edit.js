import { __ } from '@wordpress/i18n';

import { usePrevious } from '@wordpress/compose'

import {
	Panel,
	PanelBody,
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
import ResearchTopicSelector from '../../components/ResearchTopicSelector'

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
		featuredItemId,
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
			post = select( 'ramp' ).getPost( featuredItemId, 'articles' )
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
					<span>{ __( 'Currently Featured Article: ', 'ramp' ) }</span>
					<div><a href={ postUrl }>{ postTitle }</a></div>
				</div>
			)
		: <div />

	const serverSideAtts = Object.assign( {}, attributes, {
		isEditMode: true,
		forceRefresh: featuredItemId !== usePrevious( featuredItemId ) // Addresses race condition with useSelect() and ServerSideRender()
	} )

	return (
		<>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Research Topic', 'ramp' ) }
					>
					</PanelBody>
				</Panel>

				{ 'featured' === variationType && (
				<Panel>
					<PanelBody
						title={ __( 'Featured Article', 'ramp' ) }
					>
						{ currentlyFeaturedNotice }

						<PostPicker
							onSelectPost={ ( selectedPost ) => setAttributes( { featuredItemId: selectedPost.id } ) }
							label={ __( 'Select a Featured Article', 'ramp' ) }
							placeholder={ __( 'Start typing to search.', 'ramp' ) }
							postTypes={ [ 'articles' ] }
						/>
					</PanelBody>
				</Panel>
				) }
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
					<ToolbarButton
						icon={ FeaturedIcon }
						isActive={ 'featured' === variationType }
						label={ __( 'Featured', 'ramp' ) }
						onClick={ () => setAttributes( { variationType: 'featured' } ) }
					/>
				</ToolbarGroup>
			</BlockControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="ramp/article-teasers"
					className={"featured-item-id-" + featuredItemId}
					httpMethod="GET"
				/>
			</div>
		</>
	)
}
