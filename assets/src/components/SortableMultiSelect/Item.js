import React, {forwardRef} from 'react';

const Item = forwardRef(({id, children, ...props}, ref) => {
  return (
    <div {...props} ref={ref}>
			{children}
    </div>
  )
});

export default Item
