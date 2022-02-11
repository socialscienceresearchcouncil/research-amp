import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	SelectControl,
	Spinner,
	TextControl,
	ToggleControl
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

import {
	Fragment
} from '@wordpress/element'

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
		classNames.push( 'research-topic-' + researchTopic )
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
						title={ __( 'Research Topic', 'ramp' ) }
					>
						<ResearchTopicSelector
							label={ __( 'Select the Research Topic whose News Items will be shown in this block.', 'ramp' ) }
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
								{ label: __( 'One row', 'ramp' ), value: 'one' },
								{ label: __( 'Two rows', 'ramp' ), value: 'two' },
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
						title={ __( 'Featured News Item', 'ramp' ) }
					>
						<ToggleControl
							label={ __( 'Show a Featured News Item?', 'ramp' ) }
							checked={ showFeaturedItem }
							help={ showFeaturedItem ? __( 'A Featured News Item will be shown.', 'ramp' ) : __( 'No Featured News Item will be shown.', 'ramp' ) }
							onChange={ ( showFeaturedItem ) => setAttributes( { showFeaturedItem } ) }
						/>

						{ showFeaturedItem && (
							<Fragment>
								{ currentlyFeaturedNotice }

								<PostPicker
									onSelectPost={ ( selectedPost ) => setAttributes( { featuredItemId: selectedPost.id } ) }
									label={ __( 'Select a Featured News Item', 'ramp' ) }
									placeholder={ __( 'Start typing to search.', 'ramp' ) }
									postTypes={ [ 'posts' ] }
								/>
							</Fragment>
						) }
					</PanelBody>
				</Panel>
			</InspectorControls>

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
