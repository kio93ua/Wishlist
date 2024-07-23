const restApi = (url, options = {}) => {
  const restUrl = url
  const config = options

  const request = async (method, url, route, options = {}, body = null) => {
    try {
      url = route ? `${url}/${route}` : url
      const init = {
        method: method,
        headers: new Headers({
          'Content-Type': 'application/json',
          'X-WP-Nonce': options.nonce
        }),
        body
      }

      const response = await fetch(url, init)

      return await response.json()
    } catch (e) {
      throw new Error(e)
    }
  }

  return {
    get: async (route = null) => {
      return await request('GET', restUrl, route, config)
    },

    post: async (route = null, body = null) => {
      return await request('POST', restUrl, route, config, body)
    },

    delete: async (route = null, body = null) => {
      return await request('DELETE', restUrl, route, config, body)
    },

    patch: async (route = null, body = null) => {
      return await request('PATCH', restUrl, route, config, body)
    }
  }
}

export default restApi
