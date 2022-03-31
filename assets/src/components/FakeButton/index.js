import './fake-button.scss'

import classNames from 'classnames'

const FakeButton = (props) => {
	const {
		buttonStyle = 'primary',
		text
	} = props

	const divClassnames = classNames( {
		'fake-button-container': true,
		[`is-style-${buttonStyle}`]: true,
		'wp-block-button': true
	} )

	return (
		<div className={ divClassnames }>
			<div className="wp-block-button__link">
				{ text }
			</div>
		</div>
	)
}

export default FakeButton
