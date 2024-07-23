import eventBus from '../eventBus'
import restApi from '../services/restApi'
import messageHub from '../messageHub'

class Core {
  #Event = eventBus()
  #Message = messageHub()
  #Options = {}
  #Rest
  #Store = []
  #Counters = []
  #Tables = []
  #Name

  constructor(name, options = {}) {
    this.#Options = options
    this.#Name = name

    const { nonce, rest } = this.#Options
    this.#Rest = restApi(rest + '/' + name, { nonce })
  }

  mount = () => {
    this.#Rest.get()
      .then(result => {
        if (result.ok) {
          this.#Store = result.data

          this.initCounters()
          this.initTables()
          this.initEvents()

          this.emit('mount')
        }
      })
  }

  initEvents() {
    this.on('loading', this.onLoading)
    this.on('max', this.onMaximum)
    this.on('post-product', this.onPostProduct)
    this.on('delete-product', this.onDeleteProduct)
    this.on('mount', this.updateCounters)
    this.on('updated', this.updateCounters)
    this.on('updated', this.updateTables)

    document.addEventListener('click', this.documentClickHandler)
  }

  onMaximum = result => {
    this.show(result.message, 'info')
  }

  onDeleteProduct = result => {
    this.show(result.message, 'info')
  }

  onPostProduct = result => {
    this.show(result.message)
  }

  initCounters = () => {
    const { selectors, classes } = this.#Options

    document.querySelectorAll(selectors.counters).forEach(parent => {
      const span = document.createElement('span')
      span.classList.add(classes.counter, classes.empty)
      this.#Counters.push(span)
      parent.appendChild(span)
    })
  }

  updateCounters = () => {
    const length = this.#Store.reduce((acc, data) => {
      if (data.products) {
        return acc + data.products.length
      }
    }, 0)

    const { classes: { empty } } = this.#Options

    this.#Counters.forEach(counter => {
      if (length) {
        counter.textContent = length.toString()
        counter.classList.remove(empty)
      } else {
        counter.classList.add(empty)
        counter.textContent = ''
      }
    })
  }

  initTables = () => {
    document.querySelectorAll(this.#Options.selectors.tables).forEach(table => {
      this.#Tables.push(table)
    })
  }

  updateTables = () => {
    this.#Tables.forEach(async table => {
      this.emit('loading', true, table)

      const result = await this.rest.get('table')
      table.innerHTML = result.data

      this.emit('loading', false, table)
    })
  }

  onLoading = (loading = true, elem = null) => {
    if (elem instanceof Element) {
      let method = loading ? 'add' : 'remove'
      elem.classList[method](this.#Options.classes.loading)
    }
  }

  documentClickHandler = async event => {
    const { selectors: { button } } = this.#Options

    if (event.target.closest(button)) {
      event.preventDefault()

      const { selectors: { button }, classes: { active } } = this.#Options

      const $button = event.target.closest(button)
      const productID = parseInt($button.dataset.id)

      this.emit('loading', true, $button)

      try {
        let inStore = this.#Store.some(data => {
          if (data.products && data.products.includes(productID)) {
            return true
          }
        })

        if (inStore) {
          // stored product
          $button.classList.remove(active)
          await this.deleteProduct(productID)
        } else {
          // new product
          await this.postProduct(productID)
          $button.classList.add(active)
        }
      } catch (e) {
        this.onError(e)
      } finally {
        this.emit('loading', false, $button)
        this.emit('updated')
      }
    }
  }

  onError(error) {
    if (error.code) {
      this.emit(error.code, error)
    }
  }

  postProduct = async (productID, body) => {
    const result = await this.#Rest.post(`product/${productID}`, body)

    if (result.ok) {
      this.#Store = result.data
      this.emit('post-product', result)
    } else {
      throw result
    }

    return result
  }

  deleteProduct = async (productID, body) => {
    const result = await this.#Rest.delete(`product/${productID}`, body)

    if (result.ok) {
      this.#Store = result.data
      this.emit('delete-product', result)
    } else {
      throw result
    }

    return result
  }

  on = (event, callback) => {
    this.#Event.on(`${this.#Name}:${event}`, callback)

    return this
  }

  off = (event, callback) => {
    this.#Event.off(`${this.#Name}:${event}`, callback)

    return this
  }

  emit = (event, ...args) => {
    this.#Event.emit(`${this.#Name}:${event}`, ...args)

    return this
  }

  destroy = (completely = true) => {
    this.emit(`${this.#Name}:destroy`, completely)

    this.#Event.destroy()

    return this
  }

  show = (...args) => {
    this.#Message.show(...args)
  }

  get options() {
    return this.#Options
  }

  get rest() {
    return this.#Rest
  }

  get store() {
    return this.#Store
  }

  set store(value) {
    this.#Store = value
  }

  get tables() {
    return this.#Tables
  }
}

export default Core
