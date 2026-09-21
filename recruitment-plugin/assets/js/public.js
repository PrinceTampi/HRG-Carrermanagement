// public.js - Frontend JavaScript for the Recruitment Plugin public area.
(function recruitmentPublicScripts() {
	if (window.RecruitmentPublicScripts) {
		return;
	}
	window.RecruitmentPublicScripts = true;
	document.documentElement.classList.add('js');
	setupConsentButton();

	document.addEventListener('click', function handleRecruitmentClick(event) {
		const removeButton = event.target.closest('[data-remove-section]');
		if (removeButton) {
			const section = removeButton.closest('[data-repeatable]');
			if (section) {
				section.remove();
				renumberSections(removeButton.dataset.removeSection);
			}
			return;
		}

		const addButton = event.target.closest('[data-add]');
		if (!addButton) {
			return;
		}

		const type = addButton.dataset.add;
		const repeatable = document.querySelector(`[data-repeatable="${type}"]`);
		if (!repeatable) {
			return;
		}

		const clone = repeatable.cloneNode(true);
		const count = document.querySelectorAll(`[data-repeatable="${type}"]`).length + 1;
		const removeControl = document.createElement('button');
		removeControl.type = 'button';
		removeControl.className = 'daw-recruitment__remove-row';
		removeControl.dataset.removeSection = type;
		removeControl.textContent = 'Hapus';
		clone.querySelector('.daw-recruitment__repeatable-title').textContent = `${type === 'education' ? 'PENDIDIKAN' : 'PEKERJAAN'} ${count}`;
		clone.querySelector('.daw-recruitment__repeatable-title').appendChild(removeControl);
		clone.querySelectorAll('input, select, textarea').forEach(function clearField(field) {
			field.value = '';
			if (field.type === 'file' || field.type === 'checkbox' || field.type === 'radio') {
				field.checked = false;
			}
		});
		repeatable.parentNode.insertBefore(clone, addButton);
	});

	function renumberSections(type) {
		document.querySelectorAll(`[data-repeatable="${type}"]`).forEach(function updateSectionTitle(section, index) {
			const title = section.querySelector('.daw-recruitment__repeatable-title');
			const label = type === 'education' ? 'PENDIDIKAN' : 'PEKERJAAN';
			const removeControl = title.querySelector('[data-remove-section]');
			title.textContent = `${label} ${index + 1}`;
			if (removeControl) {
				title.appendChild(removeControl);
			}
		});
	}

	function setupConsentButton() {
		const submitButton = document.querySelector('[data-submit-application]');
		const consentCheckboxes = document.querySelectorAll('[data-pdp-consent]');
		if (!submitButton || !consentCheckboxes.length) {
			return;
		}

		const updateSubmitState = function updateSubmitState() {
			const allConsented = [...consentCheckboxes].every((checkbox) => checkbox.checked);
			submitButton.disabled = !allConsented;
			submitButton.setAttribute('aria-disabled', String(!allConsented));
		};

		consentCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSubmitState));
		updateSubmitState();
	}
}());
