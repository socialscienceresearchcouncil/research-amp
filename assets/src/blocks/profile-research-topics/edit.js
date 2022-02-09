import { __ } from '@wordpress/i18n';

import {
	useBlockProps
} from '@wordpress/block-editor';

import { useSelect } from '@wordpress/data';

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
	const blockProps = () => {
		let classNames = [ 'ramp-profile-research-topics' ]

		return useBlockProps( {
			className: classNames
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
			<a
				className="ramp-research-topic-tag"
				href={topic.link}
				key={'research-topic-tag-' + topicIndex}
			>{topic.title.raw}</a>
		)
	} )

	return (
		<div { ...blockProps() }>
			{topicTags}
		</div>
	)
}
