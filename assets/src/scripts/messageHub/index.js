const defaults = {
  classes: {
    message: 'dcaw-message',
    box: 'dcaw-messages-wrapper'
  },
  timeout: 4000,
  max: 5
}

const messageHub = function () {
  let messageBox = document.querySelector(`.${defaults.classes.box}`)
  let data = []

  function findElement(element) {
    return data.find(i => i.element === element)
  }

  function buildElement(str, type = 'success') {
    const element = document.createElement('div')
    element.classList.add(defaults.classes.message, `is-${type}`)
    element.textContent = str

    return element
  }

  function removeElement(element) {
    let message = findElement(element)

    clearTimeout(message.timeout)
    messageBox.removeChild(message.element)
    data = data.filter(i => i !== message)
  }

  function insertElement(element) {
    let { timeout } = findElement(element)

    messageBox.appendChild(element)

    element.addEventListener('click', event => {
      event.stopPropagation()
      clearTimeout(timeout)
      removeElement(element)
    })

    element.addEventListener('mouseover', () => {
      clearTimeout(timeout)
    })

    element.addEventListener('mouseleave', () => {
      timeout = elementTimeout(element)
    })
  }

  function elementTimeout(element) {
    return setTimeout(() => {
      removeElement(element)
    }, defaults.timeout)
  }

  function buildBox() {
    messageBox = document.createElement('div')
    messageBox.classList.add(defaults.classes.box)
    document.body.appendChild(messageBox)
  }

  return {
    show: (str, type) => {
      if (!messageBox) buildBox()

      let element = buildElement(str, type)
      let timeout = elementTimeout(element)

      if (data.length >= defaults.max) {
        removeElement(data[0].element)
      }

      data.push({ element, timeout })
      insertElement(element)
    }
  }
}

export default messageHub
