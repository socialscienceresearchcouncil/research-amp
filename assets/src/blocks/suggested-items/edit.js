import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'

import classNames from 'classnames'

import {
	InnerBlocks,
	RichText,
	useBlockProps
} from '@wordpress/block-editor'

import { useSelect } from '@wordpress/data'

import { store as coreStore } from '@wordpress/core-data'

export default function edit( {
	context: { postType, postId },
	attributes,
	setAttributes
} ) {
	const {
		headingText,
		itemType,
		numberOfItems
	} = attributes

	const blockProps = useBlockProps({
		className: [ 'sidebar-section' ]
	})

	const getTeaserBlockType = ( itemType ) => {
		switch ( itemType ) {
			case 'news-item' :
				return 'ramp/news-item-teasers'

			case 'article' :
			default :
				return 'ramp/article-teasers'
		}
	}

	const getDefaultHeadingText = ( itemType ) => {
		switch ( itemType ) {
			case 'news-item' :
				return __( 'Suggested News Items', 'ramp' )

			case 'article' :
			default :
				return __( 'Suggested Articles', 'ramp' )
		}
	}

	const teaserBlockType = getTeaserBlockType( itemType )

	const defaultHeadingText = getDefaultHeadingText( itemType )
	const headingTextValue = headingText.length ? headingText : defaultHeadingText

	const teaserBlockAtts = {
		contentMode: 'all',
		numberOfItems,
		order: 'recent',
		variationType: 'list-mini'
	}

	const innerBlocksTemplate = [
		[ teaserBlockType, teaserBlockAtts ]
	]

	return (
		<>
			<div { ...blockProps }>
				<>
					<RichText
						className="sidebar-section-title"
						onChange={ (headingText) => setAttributes( { headingText } ) }
						tagName="h3"
						value={ headingTextValue }
					/>

					<div className="section-content">
						<InnerBlocks template={ innerBlocksTemplate } />
					</div>
				</>
			</div>
		</>
	)
}
