(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {

		/* Sticky header on scroll */
		var header = document.getElementById('mr-site-header');
		if (header) {
			var onScroll = function () {
				if (window.scrollY > 40) {
					header.classList.add('is-scrolled');
				} else {
					header.classList.remove('is-scrolled');
				}
			};
			window.addEventListener('scroll', onScroll, { passive: true });
			onScroll();
		}

		/* Mobile nav toggle */
		var toggle = document.getElementById('mr-nav-toggle');
		var nav = document.getElementById('mr-nav');
		if (toggle && nav) {
			toggle.addEventListener('click', function () {
				var isOpen = nav.classList.toggle('is-open');
				document.body.classList.toggle('mr-nav-open', isOpen);
				toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			});
			nav.querySelectorAll('a').forEach(function (link) {
				link.addEventListener('click', function () {
					nav.classList.remove('is-open');
					document.body.classList.remove('mr-nav-open');
					toggle.setAttribute('aria-expanded', 'false');
				});
			});
		}

		/* Cookie consent banner */
		var CONSENT_KEY = 'mr_cookie_consent';
		var banner = document.getElementById('mr-cookie-banner');
		var acceptBtn = document.getElementById('mr-cookie-accept');
		var manageLink = document.getElementById('mr-manage-cookies');

		function hasConsent() {
			try {
				return window.localStorage.getItem(CONSENT_KEY) === 'accepted';
			} catch (e) {
				return true; // fail open if storage is unavailable/blocked
			}
		}

		function showBanner() {
			if (banner) {
				banner.classList.add('is-visible');
			}
		}

		function hideBanner() {
			if (banner) {
				banner.classList.remove('is-visible');
			}
		}

		if (banner && !hasConsent()) {
			window.setTimeout(showBanner, 600);
		}

		if (acceptBtn) {
			acceptBtn.addEventListener('click', function () {
				try {
					window.localStorage.setItem(CONSENT_KEY, 'accepted');
				} catch (e) { /* ignore */ }
				hideBanner();
			});
		}

		if (manageLink) {
			manageLink.addEventListener('click', function (e) {
				e.preventDefault();
				showBanner();
			});
		}
	});
})();
