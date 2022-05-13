import { __ } from '@wordpress/i18n';

import {
	useBlockProps
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render'

import { Fragment } from '@wordpress/element'

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
	const { researchTopic } = attributes

	const blockProps = () => {
		let classNames = []

		classNames.push( 'research-topic-' + researchTopic )

		return useBlockProps( {
			className: classNames
		} )
	}

	const serverSideAtts = Object.assign( {}, attributes, { isEditMode: true } )

	return (
		<Fragment>
			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="research-amp/homepage-slides"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	)
}
