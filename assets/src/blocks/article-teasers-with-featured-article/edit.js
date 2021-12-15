import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	SelectControl
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import { Fragment } from '@wordpress/element'

import { __experimentalNumberControl as NumberControl } from '@wordpress/components';

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

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
		featuredArticleId
	} = attributes

	const blockProps = () => {
		let classNames = []

		// This is here to force the 'dirty' state.
		classNames.push( 'featured-article-id-' + featuredArticleId )

		return useBlockProps( {
			className: classNames
		} )
	}

	const { articles } = useSelect( ( select ) => {
		const articles = select( 'ramp' ).getArticles()

		return {
			articles
		}
	} )

	let articlesOptions = articles.map( ( article ) => {
		return {
			label: article.title.rendered,
			value: article.id
		}
	} )

	articlesOptions.unshift( { label: __( 'Select a Featured Article', 'ramp' ), value: 0 } )

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Featured Article', 'ramp' ) }
					>
						<SelectControl
							label={ __( 'Select an Article for the Featured area', 'ramp' ) }
							value={ featuredArticleId }
							options={ articlesOptions }
							onChange={ ( featuredArticleId ) => setAttributes( { featuredArticleId } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ attributes }
					block="ramp/article-teasers-with-featured-article"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	)
}
