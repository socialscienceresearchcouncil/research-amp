import { __ } from '@wordpress/i18n';

import {
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
				'research-topic-tags': true,
				'wp-block-item-research-topics': true
			} )
		} )
	}

	const { associatedIds, researchTopics } = useSelect( ( select ) => {
		const researchTopics = select( 'ramp' ).getResearchTopics()

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
	const matchedTopics = ! templateSlug ? _matchedTopics : [ { title: { raw: __( 'Research Topic', 'ramp' ) } } ]

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

	return (
		<div { ...blockProps() }>
			{topicTags}
		</div>
	)
}
