import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	Spinner,
	SelectControl,
	ToggleControl
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

import { Fragment } from '@wordpress/element'

import { PostPicker } from '../../components/PostPicker'
import ResearchTopicSelector from '../../components/ResearchTopicSelector'

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
		featuredItemId,
		researchTopic,
		showFeaturedItem,
		variationType
	} = attributes

	const blockProps = () => {
		let classNames = []

		classNames.push( 'featured-item-id-' + featuredItemId )
		classNames.push( 'research-topic-' + researchTopic )
		classNames.push( 'variation-type-' + variationType )

		return useBlockProps( {
			className: classNames
		} )
	}

	const { post } = useSelect( ( select ) => {
		const post = select( 'ramp' ).getPost( featuredItemId )

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

	const serverSideAtts = Object.assign( {}, attributes, { isEditMode: true } )

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Research Topic', 'ramp' ) }
					>
						<ResearchTopicSelector
							label={ __( 'Select the Research Topic whose Article will be shown in this block.', 'ramp' ) }
							selected={ researchTopic }
							onChangeCallback={ ( researchTopic ) => setAttributes( { researchTopic } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Layout', 'ramp' ) }
					>
						<SelectControl
							label={ __( 'Select a layout', 'ramp' ) }
							options={ [
								{ label: __( 'Grid', 'ramp' ), value: 'grid' },
								{ label: __( 'Columns', 'ramp' ), value: 'columns' },
							] }
							selected={ variationType }
							value={ variationType }
							onChange={ ( variationType ) => setAttributes( { variationType } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Featured Article', 'ramp' ) }
					>
						<ToggleControl
							label={ __( 'Show a Featured Article?', 'ramp' ) }
							checked={ showFeaturedItem }
							help={ showFeaturedItem ? __( 'A Featured Article will be shown.', 'ramp' ) : __( 'No Featured Article will be shown.', 'ramp' ) }
							onChange={ ( showFeaturedItem ) => setAttributes( { showFeaturedItem } ) }
						/>

						{ showFeaturedItem && (
							<Fragment>
								{ currentlyFeaturedNotice }

								<PostPicker
									onSelectPost={ ( selectedPost ) => setAttributes( { featuredItemId: selectedPost.id } ) }
									label={ __( 'Select a Featured Article', 'ramp' ) }
									placeholder={ __( 'Start typing to search.', 'ramp' ) }
									postTypes={ [ 'articles' ] }
								/>
							</Fragment>
						) }
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="ramp/article-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	)
}
