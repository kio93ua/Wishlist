/**
 * Run scripts on document ready
 */

/* global dcawCompare */
/* global dcawWishlist */
/* global dcawGeneral */

import Wishlist from './wishlist'
import Compare from './compare'
import dropdown from './dropdown'

document.addEventListener('DOMContentLoaded', () => {

  dropdown.mount()

  if (typeof dcawCompare === 'object' && typeof dcawGeneral === 'object') {
    const compare = new Compare({ ...dcawGeneral, ...dcawCompare })

    compare.mount()
  }

  if (typeof dcawWishlist === 'object' && typeof dcawGeneral === 'object') {
    const wishlist = new Wishlist({ ...dcawGeneral, ...dcawWishlist })

    wishlist.mount()
  }

})
