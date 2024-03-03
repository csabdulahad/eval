(() => {

	/**
	 * A helper class which allows to resize a group of divs on drag event!
	 * Use {@link DivResizer.hook()} method to set drag event for divs.
	 * */
	class DivResizer {

		static #obj = {}
		static #dragging = false
		static #callback = []

		static #mouseDiv = null

		/*
		 * Shouldn't be called manually.
		 * DivResizer invokes this on initialization!
		 * */
		static injectDom() {
			if ($('#jst-div-resizer').length) return;

			jst.run(() => {
				/*
				 * Inject overlay for mouse cursor correction
				 * */
				$('body').append(
					`<div 
						id="jst-div-resizer" 
						style="
							position: fixed;
							width: 100%;
							height: 100%;
							display: none;
							z-index: 9999;
							cursor: e-resize;">
					</div>
				`)

				DivResizer.#mouseDiv = $('#jst-div-resizer')
			})
		}

		/**
		 * Listens for mouse movement on the document and performs resizing calculations based on the mouse movement.
		 * Shouldn't be called manually.
		 */
		static mouseMoveOnDoc () {
			$(document).mousemove((e) => {
				// if (!DivResizer.#dragging || DivResizer.#obj.name !== config.name) return
				if (!DivResizer.#dragging) return

				/*
				 * When we have signed [meaning negative diff], it means the owner div who has the
				 * bar in it needs more space, thus we are using ABS to add it to the owner div
				 * and subtracting this amount from the other div!
				 * */
				let diff = e.clientX - DivResizer.#obj.x
				let signed = diff < 0
				diff = Math.abs(diff)

				let otherW, ownerW
				if (signed) {
					otherW = DivResizer.pxToPercent(DivResizer.#obj.yourW - diff)
					ownerW = DivResizer.pxToPercent(DivResizer.#obj.myW + diff)
				} else {
					otherW = DivResizer.pxToPercent(DivResizer.#obj.yourW + diff)
					ownerW = DivResizer.pxToPercent(DivResizer.#obj.myW - diff)
				}

				/*
				 * Check this resize will not shrink too much!
				 * */
				if (otherW <= DivResizer.#obj.otherMinSize || ownerW <= DivResizer.#obj.ownerMinSize) {
					log('no resize')
					return
				}

				/*
				 * THERE IS HUGE DIFFERENCE BETWEEN .css('width', X') and .width(X)!
				 * */
				// $(owner).css('width', `${ownerW}%`)
				// $(other).css('width', `${otherW}%`)
				DivResizer.#obj.owner.width(`${ownerW}%`)
				DivResizer.#obj.other.width(`${otherW}%`)
			})
		}

		/**
		 * Handles the mouse up event on the document.
		 * Shouldn't be called manually.
		 */
		static mouseUpOnDoc () {
			$(document).mouseup(() => {
				DivResizer.#dragging = false

				/*
				 * Restore mouse & select properties
				 * */
				$('body').css('user-select', 'unset')
				$(DivResizer.#mouseDiv).hide()

				/*
				 * Remove drag style css class from the bar
				 * */
				if (DivResizer.#obj.style)
					DivResizer.#obj.bar.removeClass(DivResizer.#obj.style)

				/*
				 * Notify callbacks
				 * */
				DivResizer.#callback.forEach((i) => {
					i(
						DivResizer.#obj.name,
						DivResizer.#obj.owner,
						DivResizer.#obj.other
					)
				})
			})
		}

		/**
		 * Callback when the drag is done between two divs
		 *
		 * @param {function ({name:string, owner:jQuery, other:jQuery})} callback receives the callback
		 * when user has done dragging divs
		 * */
		static listenToDragDone(callback) {
			DivResizer.#callback.push(callback)
		}

		/**
		 * Two divs can be resized using this method when user drags the bar between two divs. An initial
		 * width for the divs are required to make it work for responsive scenario. Also, body tag should
		 * not have any margin/padding [not tested with padding], as it causes some calculation off!
		 *
		 * @param {object} config
		 * @param {string} config.name Unique name is required to distinguish the drag event between drag bars
		 * @param {any | jQuery} config.bar The jquery fetched DOM for the drag bar
		 * @param {any | jQuery} config.owner The jquery fetched DOM for the owner div
		 * @param {any | jQuery} config.other The jquery fetched DOM for the other div
		 * @param {number=} [config.ownerMinSize=100] The min size for the owner div
		 * @param {number=} [config.otherMinSize=100] The min size for the other div
		 * @param {string=} config.style A CSS class to be applied when user dragging the bar
		 * */
		static hook(config) {
			config.bar.mousedown((e) => {

				DivResizer.#obj.name = config.name
				DivResizer.#obj.bar = config.bar
				DivResizer.#obj.owner = config.owner
				DivResizer.#obj.other = config.other
				DivResizer.#obj.ownerMinSize = DivResizer.pxToPercent(config.ownerMinSize ?? 100)
				DivResizer.#obj.otherMinSize = DivResizer.pxToPercent(config.otherMinSize ?? 100)
				DivResizer.#obj.x = e.clientX
				DivResizer.#obj.myW = config.owner.outerWidth()
				DivResizer.#obj.yourW = config.other.outerWidth()
				DivResizer.#obj.style = config.style

				DivResizer.#dragging = true

				/*
				 * Update mouse cursor stying to avoid having glitch in dragging for mouse cursor
				 * */
				$('body').css('user-select', 'none')
				$(DivResizer.#mouseDiv).show()

				/*
				 * Apply drag style css class to the bar
				 * */
				if (config.style) config.bar.addClass(config.style)
			})

		}

		/**
		 * Converts pixel unit to percentage for the inner height & width of the window
		 *
		 * @param {number} px The pixel value
		 * @param {'x'|'y'} axis The axis needed to calculate the percentage against either width or height
		 * of the window
		 * @return {number} Percentage value for specified pixel
		 */
		static pxToPercent(px, axis = 'x') {
			let full = axis === 'x' ? window.innerWidth : window.innerHeight
			let uni = full * .01
			return px / uni
		}


		/**
		 * Converts percentage value to pixels based on the specified axis.
		 *
		 * @param {number} percent - The percentage value to convert.
		 * @param {string} [axis='x'] - The axis to base the conversion on. Default value is 'x'.
		 * @return {number} The converted value in pixels.
		 */
		static percentToPx(percent, axis = 'x') {
			let full = axis === 'x' ? window.innerWidth : window.innerHeight
			let uni = full * .01
			return percent * uni;
		}

	}


	DivResizer.injectDom()
	DivResizer.mouseMoveOnDoc()
	DivResizer.mouseUpOnDoc()

	window.DivResizer = DivResizer

})()