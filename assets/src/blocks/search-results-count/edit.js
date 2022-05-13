import './editor.scss'

import { __ } from '@wordpress/i18n'

import {
	useBlockProps
} from '@wordpress/block-editor'

export default function edit() {
	const blockProps = useBlockProps()
	return (
		<div { ...blockProps }>
			{ __( '1,234 Results for "search terms" in "All content types"', 'research-amp' ) }
		</div>
	)
}
