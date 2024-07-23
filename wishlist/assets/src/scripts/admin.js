/* global jQuery, wp */

const sections = document.querySelectorAll('.MSettingSection')

if (sections.length) {
  let tabs = document.querySelectorAll('.nav-tab')

  /* Active tab */
  let activeTab
  activeTab = localStorage.getItem('activeTab')

  if (window.location.hash) {
    activeTab = window.location.hash
    localStorage.setItem('activeTab', activeTab)
  }

  /* Initialize active section */
  if (activeTab) {
    tabs.forEach(tab => {
      tab.getAttribute('href') === activeTab
      && (tab.classList.add('nav-tab-active'))
    })

    sections.forEach(section => {
      `#${section.id}` === activeTab && (section.style.display = 'block')
    })
  } else {
    sections[0] && (sections[0].style.display = 'block')
    tabs[0] && (tabs[0].classList.add('nav-tab-active'))
  }

  /* Change active tab and section */
  tabs.forEach(tab => {
    tab.addEventListener('click', evt => {
      evt.preventDefault()

      localStorage.setItem('activeTab', tab.getAttribute('href'))

      tabs.forEach(tab => tab.classList.remove('nav-tab-active'))
      tab.classList.add('nav-tab-active')

      sections.forEach(section => {
        section.style.display = 'none'

        if (`#${section.id}` === tab.getAttribute('href')) {
          section.style.display = 'block'
        }
      })
    })
  })
}

jQuery(document).ready(function ($) {

  /***** Colour picker *****/
  $('.MColorPickerInput').wpColorPicker()

  /* File input */
  $('.MFileInputBrowse').on('click', function (event) {
    event.preventDefault()

    let self = $(this)
    let attachment

    // Create the media frame.
    // eslint-disable-next-line camelcase
    let file_frame = wp.media.frames.file_frame = wp.media({
      title: self.data('uploader_title'),
      button: {
        text: self.data('uploader_button_text')
      },
      multiple: false
    })

    // eslint-disable-next-line camelcase
    file_frame.on('select', function () {
      // eslint-disable-next-line camelcase
      attachment = file_frame.state().get('selection').first().toJSON()
      self.prev('.MFileInput').val(attachment.url).change()
    })

    // Finally, open the modal
    // eslint-disable-next-line camelcase
    file_frame.open()
  })

})
