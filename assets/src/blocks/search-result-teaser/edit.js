import './editor.scss'

import { __ } from '@wordpress/i18n'

import ServerSideRender from '@wordpress/server-side-render'

import {
	useBlockProps
} from '@wordpress/block-editor'

export default function edit( {
	attributes,
	context: { postType, postId }
} ) {
	const blockProps = useBlockProps()

	const serverSideAtts = Object.assign( {}, attributes, {
		isEditMode: true,
		postId
	} )

	return (
		<div { ...blockProps }>
			<ServerSideRender
				attributes={ serverSideAtts }
				block="ramp/search-result-teaser"
				httpMethod="GET"
			/>
		</div>
	)
}
