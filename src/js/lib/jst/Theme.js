(() => {

	class Theme {
		#listeners

		constructor() {
			this.#listeners = [];

			let config = {
				attributes: true,
				attributeFilter: ['data-bs-theme'] // specify the attribute you are interested in
			};

			let callback = (mutationsList, observer) =>{

				let value = this.isDark() ? 'dark' : 'light'
				localStorage.setItem('theme', value)

				for(let mutation of mutationsList) {
					if (mutation.type === 'attributes') {
						this.#listeners.forEach((listener) => {
							listener(this.isLight())
						})
					}
				}
			}

			// Create an observer instance linked to the callback function
			let observer = new MutationObserver(callback)

			$(document).ready(() => {
				// Start observing the target node for configured mutations
				observer.observe($('body')[0], config)
			})
		}

		/**
		 * @param callback {function(string:theme)}
		 * */
		listenChange(callback) {
			this.#listeners.push(callback)
		}

		isDark() {
			return document.body.getAttribute('data-bs-theme') === 'dark'
		}

		isLight() {
			return !this.isDark()
		}

		toggle(theme = null) {
			this.#setTransitionEffect()

			if (theme === null) {
				theme = this.isDark() ? 'light' : 'dark'
			}

			document.body.setAttribute('data-bs-theme', theme)
			localStorage.setItem('theme', theme)

			jst.runLater(2, this.#removeTransitionEffect)

			$('#theme-btn i').toggleClass('bi-moon-stars-fill', 'bi-brightness-high-fill')
			$('#theme-btn span').text(theme === 'light' ? 'Dark' : 'Light')
		}

		eChartThemeName() {
			return this.isDark() ? 'dark_custom' : 'light_custom'
		}

		load() {
			let theme = localStorage.getItem('theme') ?? 'dark'

			if (theme === 'dark') return

			this.toggle(theme)
		}

		#setTransitionEffect() {
			let style = document.createElement('style')
			style.id = 'dynamicTransition'
			style.innerHTML = `* { transition: background 600ms !important; }`
			document.head.appendChild(style)
		}

		#removeTransitionEffect() {
			let styleElement = document.querySelector('#dynamicTransition')
			if (styleElement) {
				styleElement.parentNode.removeChild(styleElement)
			}
		}

	}

	window.Theme = new Theme()

})()

