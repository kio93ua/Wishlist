const config = {
  active: 'is-active',
  target: '.js-dcaw-dropdown'
}

const dropdown = function () {
  let isClose = true
  let toggle
  let parent

  const closeHandler = e => {
    if (!isClose && !parent.contains(e.target)) {
      parent.classList.remove(config.active)
      isClose = true
      document.removeEventListener('click', closeHandler, true)
    }
  }

  const clickHandler = e => {
    toggle = e.target.closest(config.target)

    if (toggle) {
      e.preventDefault()
      e.stopPropagation()

      parent = toggle.parentNode

      if (parent.classList.contains(config.active)) {
        parent.classList.remove(config.active)
        isClose = true
      } else {
        parent.classList.add(config.active)
        isClose = false
        document.addEventListener('click', closeHandler, true)
      }
    }
  }

  return {
    mount: () => {
      document.addEventListener('click', clickHandler)
    }
  }
}()

export default dropdown
