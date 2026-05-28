/*
* File       : common.js
* Author     : B-WORKER
* Dependency : css/b-strap.css
*      
*/

document.addEventListener('DOMContentLoaded', function () {

	function wrapWithSpan(element) {
		if (!element.parentElement || element.parentElement.hasAttribute('data-ani-wrap')) {
			return;
		}
		var wrapper = document.createElement('span');
		wrapper.setAttribute('data-ani-wrap', '');
		element.parentElement.insertBefore(wrapper, element);
		wrapper.appendChild(element);
	}

	function scroll_animation_basic() {
		var motionItems = document.querySelectorAll('[data-ani]');
		if (motionItems.length === 0) return;

		motionItems.forEach(function(item) {
			var dataItem = item.getAttribute('data-ani');
			if (dataItem === 'skew' || dataItem === 'hidden') {
				wrapWithSpan(item);
			}
		});

		var scroll_pos_begin = function() {
			motionItems.forEach(function(item) {
				var width = window.innerWidth;
				var pos_trigger = 0;
				if (width >= 540) {
					pos_trigger = item.offsetHeight / 3;
				}

				var rect = item.getBoundingClientRect();
				var bottom_of_object = window.scrollY + rect.top + pos_trigger;
				var bottom_of_window = window.scrollY + window.innerHeight;

				if (bottom_of_window > bottom_of_object) {
					item.classList.add('is_moved');
				}
			});
		};

		scroll_pos_begin();
		window.addEventListener('scroll', scroll_pos_begin);
	}

	scroll_animation_basic();
});

window.addEventListener('scroll', function() {
	var motionCons = document.querySelectorAll('.motion-con');
	if (motionCons.length === 0) return;

	motionCons.forEach(function(item) {
		var rect = item.getBoundingClientRect();
		var bottom_of_object = window.scrollY + rect.top + item.offsetHeight / 3;
		var bottom_of_window = window.scrollY + window.innerHeight;

		if (bottom_of_window > bottom_of_object) {
			item.classList.add('is_moved');
		} else {
			item.classList.remove('is_moved');
		}
	});
});
