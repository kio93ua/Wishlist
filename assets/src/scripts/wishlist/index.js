import Core from '../core'
import { copyToClipboard } from '../utils/clipboard'

class Wishlist extends Core {
  #moveProducts = []

  constructor(options = {}) {
    super('wishlist', options)
  }

  initEvents() {
    super.initEvents()

    this.on('guest', this.onGuestClick)
    this.on('shared-list', this.onShareList)
    this.on('post-list', this.onPostList)
    this.on('delete-list', this.onDeleteList)
    this.on('patch-list', this.onPatchList)
    this.on('loading', this.updateMovedProducts)

    document.querySelectorAll(
      this.options.selectors.createWishlist
    ).forEach(item => {
      item.addEventListener('click', this.createWishlistBtnHandler)
    })

    this.tables.forEach(table => {
      table.addEventListener('click', this.tableClickHandler)
      table.addEventListener('submit', this.tableSubmitHandler)
      table.addEventListener('change', this.updateMovedProducts)
    })
  }

  updateMovedProducts = () => {
    this.tables.forEach(table => {
      const checkboxs = table.querySelectorAll('input[name="move_products"]')

      this.#moveProducts = Array.from(checkboxs)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => parseInt(checkbox.value))
    })
  }

  tableSubmitHandler = async e => {
    e.preventDefault()

    const formData = new FormData(e.target)

    switch (e.target.dataset.name) {
    case 'edit_list':
      await this.patchList(formData.get('id'), JSON.stringify({
        id: formData.get('id'),
        title: formData.get('title')
      }))
      break
    case 'move_products':
      if (this.#moveProducts.length) {
        await this.patchList(formData.get('prev_list'), JSON.stringify({
          // eslint-disable-next-line camelcase
          prev_list: formData.get('prev_list'),
          // eslint-disable-next-line camelcase
          next_list: formData.get('next_list'),
          products: this.#moveProducts
        }))
      } else {
        return
      }

      break
    }

    this.emit('updated')
  }

  tableClickHandler = async e => {
    if (e.target.closest('[data-action]')) {
      e.preventDefault()

      await this.buttonActions(e.target.closest('[data-action]'))
    }

    if (e.target.closest(this.options.selectors.remove)) {
      e.preventDefault()

      await this.removeProductBtn(
        e.target.closest(this.options.selectors.remove)
      )
    }
  }

  buttonActions = async button => {
    const id = button.dataset.id
    const action = button.dataset.action

    this.emit('loading', true, button)

    try {
      switch (action) {
      case 'delete':
        await this.deleteList(id)
        break
      case 'default':
        await this.patchList(id, JSON.stringify({
          default: true
        }))
        break
      case 'share':
        await this.shareList(id)
        break
      }
    } catch (e) {
      super.onError(e)
    } finally {
      this.emit('loading', false, button)
      this.emit('updated')
    }
  }

  removeProductBtn = async button => {
    const productID = parseInt(button.dataset.id)
    const listID = button.dataset.listId

    this.emit('loading', true, button)

    try {
      await this.deleteProduct(productID, JSON.stringify({
        // eslint-disable-next-line camelcase
        list_id: listID
      }))
    } catch (e) {
      this.onError(e)
    } finally {
      this.emit('loading', false, button)
      this.emit('updated')
    }
  }

  createWishlistBtnHandler = async e => {
    e.preventDefault()

    const button = e.currentTarget

    this.emit('loading', true, button)

    try {
      await this.createList()
    } catch (e) {
      super.onError(e)
    } finally {
      this.emit('loading', false, button)
      this.emit('updated')
    }
  }

  shareList = async id => {
    const result = await this.rest.get(`list/share/${id}`)

    if (result.ok) {
      this.emit('shared-list', result)
    } else {
      throw result
    }

    return result
  }

  createList = async () => {
    const result = await this.rest.post('list')

    if (result.ok) {
      this.store = result.data
      this.emit('post-list', result)
    } else {
      throw result
    }

    return result
  }

  patchList = async (id, body) => {
    const result = await this.rest.patch(`list/${id}`, body)

    if (result.ok) {
      this.store = result.data
      this.emit('patch-list', result)
    } else {
      throw result
    }

    return result
  }

  deleteList = async id => {
    const result = await this.rest.delete(`list/${id}`)

    if (result.ok) {
      this.store = result.data
      this.emit('delete-list', result)
    } else {
      throw result
    }

    return result
  }

  onGuestClick = result => {
    this.show(result.message)
    window.location.href = this.options.accountLink
  }

  onShareList = result => {
    copyToClipboard(result.data)
    this.show(result.message)
  }

  onPostList = result => {
    this.show(result.message)
  }

  onDeleteList = result => {
    this.show(result.message, 'warning')
  }

  onPatchList = result => {
    this.show(result.message, 'info')
  }
}

export default Wishlist
