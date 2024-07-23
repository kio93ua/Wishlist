import Core from '../core'

class Compare extends Core {

  constructor(options = {}) {
    super('compare', options)
  }

  initEvents() {
    super.initEvents()

    this.tables.forEach(table => {
      table.addEventListener('click', this.tableClickHandler)
    })
  }

  tableClickHandler = async e => {
    if (e.target.closest('[data-action="clear"]')) {
      e.preventDefault()

      const button = e.target.closest('[data-action="clear"]')

      this.emit('loading', true, button)

      try {
        await this.clearCompare()
      } catch (e) {
        super.onError(e)
      } finally {
        this.emit('loading', false, button)
        this.emit('updated')
      }
    }

    if (e.target.closest(this.options.selectors.remove)) {
      e.preventDefault()

      const button = e.target.closest(this.options.selectors.remove)
      const productID = parseInt(button.dataset.id)

      this.emit('loading', true, button)

      try {
        await this.deleteProduct(productID)
      } catch (e) {
        this.onError(e)
      } finally {
        this.emit('loading', false, button)
        this.emit('updated')
      }
    }

    if (e.target.closest(this.options.selectors.collapse)) {
      e.preventDefault()

      e.target.closest(this.options.selectors.collapse)
        .parentNode
        .classList
        .toggle(this.options.classes.collapse)
    }
  }

  clearCompare = async () => {
    const result = await this.rest.delete()

    if (result.ok) {
      this.store = result.data
      this.emit('cleared', result)
    } else {
      throw result
    }

    return result
  }
}

export default Compare
