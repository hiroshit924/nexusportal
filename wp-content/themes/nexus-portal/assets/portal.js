(() => {
	const splash = document.querySelector('[data-splash]');
	if (splash && !window.matchMedia('(prefers-reduced-motion: reduce)').matches && !window.location.hash) {
		const visitKey = 'nexus-portal-intro-seen';
		let visited = false;

		try {
			visited = sessionStorage.getItem(visitKey) === 'yes';
			if (!visited) sessionStorage.setItem(visitKey, 'yes');
		} catch {
			// Private or embedded browsers may block session storage.
		}

		if (!visited) {
			const pageElements = document.querySelectorAll('.skip-link, .site-header, #main, .site-footer');
			const dismiss = () => {
				if (splash.classList.contains('splash-closing')) return;
				splash.classList.add('splash-closing');
				window.clearTimeout(timer);
				document.removeEventListener('keydown', handleEscape);
				const restoreFocus = splash.contains(document.activeElement);
				window.setTimeout(() => {
					splash.hidden = true;
					document.documentElement.classList.remove('splash-active');
					pageElements.forEach((element) => { element.inert = false; });
					if (restoreFocus) document.querySelector('.site-header .brand-home').focus();
				}, 350);
			};
			const handleEscape = (event) => {
				if (event.key === 'Escape') dismiss();
			};
			splash.hidden = false;
			document.documentElement.classList.add('splash-active');
			pageElements.forEach((element) => { element.inert = true; });
			const timer = window.setTimeout(dismiss, 1850);
			splash.querySelector('[data-splash-skip]').addEventListener('click', dismiss, { once: true });
			document.addEventListener('keydown', handleEscape);
		}
	}

	const toggle = document.querySelector('.menu-toggle');
	const navigation = document.querySelector('.site-nav');
	if (!toggle || !navigation) return;

	toggle.hidden = false;
	document.documentElement.classList.add('has-menu-toggle');

	const setMenu = (open) => {
		toggle.setAttribute('aria-expanded', String(open));
		navigation.classList.toggle('is-open', open);
	};

	toggle.addEventListener('click', () => setMenu(toggle.getAttribute('aria-expanded') !== 'true'));
	navigation.addEventListener('click', (event) => {
		if (event.target.closest('a')) setMenu(false);
	});
	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
			setMenu(false);
			toggle.focus();
		}
	});
})();
