import './fake-input.scss';

import classNames from 'classnames';

const FakeInput = ( props ) => {
	const { isSearch, text } = props;

	const divClassnames = classNames( {
		'fake-input-container': true,
		'is-search': isSearch,
	} );

	return <div className={ divClassnames }>{ text }</div>;
};

export default FakeInput;
