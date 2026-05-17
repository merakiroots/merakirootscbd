(function () {
	var verifiedKey = 'meraki_age_verified';
	var gate = document.querySelector('[data-meraki-age-gate]');
	var acceptButton = gate ? gate.querySelector('[data-meraki-age-accept]') : null;
	var lastFocused = null;

	function storageGet(key) {
		try {
			return window.localStorage.getItem(key);
		} catch (error) {
			return null;
		}
	}

	function storageSet(key, value) {
		try {
			window.localStorage.setItem(key, value);
		} catch (error) {
			return;
		}
	}

	function hasVerifiedCookie() {
		return document.cookie.split('; ').indexOf('meraki_age_verified=1') !== -1;
	}

	function setVerifiedCookie() {
		document.cookie = 'meraki_age_verified=1; max-age=2592000; path=/; SameSite=Lax';
	}

	function showAgeGate() {
		if (!gate || !acceptButton) {
			return;
		}

		lastFocused = document.activeElement;
		gate.hidden = false;
		document.documentElement.classList.add('meraki-age-gate-active');
		acceptButton.focus({ preventScroll: true });
	}

	function hideAgeGate() {
		if (!gate) {
			return;
		}

		gate.hidden = true;
		document.documentElement.classList.remove('meraki-age-gate-active');

		if (lastFocused && typeof lastFocused.focus === 'function') {
			lastFocused.focus({ preventScroll: true });
		}
	}

	function trapAgeGateFocus(event) {
		if (!gate || gate.hidden || event.key !== 'Tab') {
			return;
		}

		var focusable = gate.querySelectorAll('a[href], button:not([disabled])');
		if (!focusable.length) {
			return;
		}

		var first = focusable[0];
		var last = focusable[focusable.length - 1];

		if (event.shiftKey && document.activeElement === first) {
			event.preventDefault();
			last.focus();
		} else if (!event.shiftKey && document.activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	}

	if (gate && acceptButton && storageGet(verifiedKey) !== '1' && !hasVerifiedCookie()) {
		showAgeGate();
	}

	if (acceptButton) {
		acceptButton.addEventListener('click', function () {
			storageSet(verifiedKey, '1');
			setVerifiedCookie();
			hideAgeGate();
		});
	}

	document.addEventListener('keydown', trapAgeGateFocus);

	document.querySelectorAll('.meraki-mobile-nav').forEach(function (nav) {
		nav.addEventListener('click', function (event) {
			if (event.target && event.target.matches('.meraki-mobile-nav__panel a')) {
				nav.removeAttribute('open');
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				nav.removeAttribute('open');
			}
		});

		document.addEventListener('click', function (event) {
			if (!nav.contains(event.target)) {
				nav.removeAttribute('open');
			}
		});
	});
}());
