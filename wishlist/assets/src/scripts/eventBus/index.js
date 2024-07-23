const eventBus = function () {
  let data = {}

  return {
    on: (events, callback) => {
      events.split(' ').forEach(event => {
        (data[event] = data[event] || []).push(callback)
      })
    },

    off: (events, callback) => {
      events.split(' ').forEach(event => {
        data[event] = (data[event] || []).filter(i => i !== callback)
      })
    },

    emit: (event, ...args) => {
      (data[event] || []).forEach(callback => callback(...args))
    },

    destroy: () => data = {}
  }
}

export default eventBus
