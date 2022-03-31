import { __ } from '@wordpress/i18n';

import {
	useBlockProps
} from '@wordpress/block-editor';

import { useSelect } from '@wordpress/data';

import classNames from 'classnames'

import './editor.scss';

export default function edit( {
	attributes,
	setAttributes,
} ) {
	const blockProps = () => {
		return useBlockProps( {
			className: classNames( {
				'research-topic-tags': true,
				'wp-block-profile-research-topics': true
			} )
		} )
	}

	const { associatedIds, researchTopics } = useSelect( ( select ) => {
		const researchTopics = select( 'ramp' ).getResearchTopics()

		const associatedIds = select( 'core/editor' ).getEditedPostAttribute( 'ramp_assoc_topic' )

		return {
			associatedIds,
			researchTopics
		}
	} )

	const matchedTopics = researchTopics.filter( topic => {
		return -1 !== associatedIds.indexOf( topic.associated_term_id )
	} )

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
