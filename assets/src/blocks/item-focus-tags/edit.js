import { __ } from '@wordpress/i18n';

import {
	innerBlocks,
	useBlockProps
} from '@wordpress/block-editor';

import { useSelect } from '@wordpress/data';

import classNames from 'classnames'

import './editor.scss';

export default function edit( {
	attributes,
	context: { templateSlug },
	setAttributes,
} ) {
	const blockProps = () => {
		return useBlockProps( {
			className: classNames( {
				'item-focus-tags': true,
				'wp-block-item-focus-tags': true
			} )
		} )
	}

	const { associatedIds, researchTopics } = useSelect( ( select ) => {
		const researchTopics = select( 'research-amp' ).getResearchTopics()

		const associatedIds = ! templateSlug ? select( 'core/editor' ).getEditedPostAttribute( 'ramp_assoc_topic' ) : []

		return {
			associatedIds,
			researchTopics
		}
	} )

	const _matchedTopics = associatedIds ? researchTopics.filter( topic => {
		return -1 !== associatedIds.indexOf( topic.associated_term_id )
	} ) : []

	// Show a placeholder "Research Topic" when editing a template.
	const matchedTopics = ! templateSlug ? _matchedTopics : [ { title: { raw: __( 'Research Topic', 'research-amp' ) } } ]

	let topicIndex = 0
	const topicTags = matchedTopics.map( topic => {
		topicIndex++
		return (
			<span
				className="ramp-research-topic-tag tag-bubble"
				key={'research-topic-tag-' + topicIndex}
			>{topic.title.raw}</span>
		)
	} )

	const template = [
		[ 'core/post-tags' ]
	]

	return (
		<div { ...blockProps() }>
			<InnerBlocks
				template={ template }
			/>
		</div>
	)
}
