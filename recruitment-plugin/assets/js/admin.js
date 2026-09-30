// admin.js — JavaScript for the Recruitment Plugin HRD admin dashboard.
// TODO: Tambahkan interaksi admin seperti konfirmasi delete, filter tabel, dll.
document.documentElement.classList.add('js');

(function recruitmentDashboardFilters() {
	const dashboard = document.querySelector('[data-recruitment-dashboard]');
	if (!dashboard) {
		return;
	}

	const stageButtons = Array.from(dashboard.querySelectorAll('[data-stage-filter]'));
	const searchInput = dashboard.querySelector('[data-candidate-search]');
	const dealerSelect = dashboard.querySelector('[data-candidate-dealer]');
	const vacancySelect = dashboard.querySelector('[data-candidate-vacancy]');
	const resetButton = dashboard.querySelector('[data-candidate-reset]');
	const emptyMessage = dashboard.querySelector('[data-candidate-empty]');
	const visibleCount = dashboard.querySelector('[data-candidate-count]');
	let activeStage = 'all';

	const applyFilters = function applyFilters() {
		const search = searchInput.value.trim().toLocaleLowerCase();
		const cards = Array.from(dashboard.querySelectorAll('[data-candidate-card]'));
		let count = 0;

		cards.forEach(function filterCandidate(card) {
			const matchesStage = 'all' === activeStage || activeStage === card.dataset.stage;
			const matchesSearch = !search || card.dataset.search.includes(search);
			const matchesDealer = !dealerSelect.value || dealerSelect.value === card.dataset.dealer;
			const matchesVacancy = !vacancySelect.value || vacancySelect.value === card.dataset.vacancy;
			const isVisible = matchesStage && matchesSearch && matchesDealer && matchesVacancy;

			card.hidden = !isVisible;
			if (isVisible) {
				count++;
			}
		});

		visibleCount.textContent = String(count);
		emptyMessage.hidden = count > 0;
	};

	stageButtons.forEach(function bindStageFilter(button) {
		button.addEventListener('click', function selectStage() {
			activeStage = button.dataset.stageFilter;
			stageButtons.forEach(function updateStageButton(item) {
				const isActive = item === button;
				item.classList.toggle('is-active', isActive);
				item.setAttribute('aria-pressed', String(isActive));
			});
			applyFilters();
		});
	});

	searchInput.addEventListener('input', applyFilters);
	dealerSelect.addEventListener('change', applyFilters);
	vacancySelect.addEventListener('change', applyFilters);
	resetButton.addEventListener('click', function resetFilters() {
		searchInput.value = '';
		dealerSelect.value = '';
		vacancySelect.value = '';
		stageButtons[0].click();
	});
})();

(function userInterviewNotes() {
	const screen = document.querySelector('.daw-user-interview--notes');
	if (!screen) {
		return;
	}

	screen.querySelectorAll('[data-choice-group]').forEach(function bindChoiceGroup(group) {
		group.querySelectorAll('button').forEach(function bindChoice(button) {
			button.addEventListener('click', function selectChoice() {
				group.querySelectorAll('button').forEach(function clearChoice(choice) {
					const selected = choice === button;
					choice.classList.toggle('is-selected', selected);
					choice.setAttribute('aria-pressed', String(selected));
				});
			});
		});
	});

	const form = screen.querySelector('[data-user-interview-form]');
	const saveFeedback = screen.querySelector('[data-interview-feedback]');
	form.addEventListener('submit', function saveInterviewNotes(event) {
		event.preventDefault();
		saveFeedback.textContent = 'Catatan diperbarui pada preview ini.';
		saveFeedback.hidden = false;
	});

	const decisionFeedback = screen.querySelector('[data-decision-feedback]');
	screen.querySelectorAll('[data-final-decision]').forEach(function bindDecision(button) {
		button.addEventListener('click', function chooseDecision() {
			screen.querySelectorAll('[data-final-decision]').forEach(function updateDecision(choice) {
				const selected = choice === button;
				choice.classList.toggle('is-selected', selected);
				choice.setAttribute('aria-pressed', String(selected));
			});
			decisionFeedback.textContent = 'Keputusan dipilih: ' + button.textContent.trim().replace(/^[✓×]\s*/, '');
			decisionFeedback.hidden = false;
		});
	});
})();

(function vacancyPreview() {
	const screen = document.querySelector('.daw-vacancies');
	if (!screen) {
		return;
	}

	const dialog = screen.querySelector('[data-vacancy-dialog]');
	const form = screen.querySelector('[data-vacancy-form]');
	const list = screen.querySelector('[data-vacancy-list]');
	const feedback = screen.querySelector('[data-vacancy-feedback]');
	const counts = Object.fromEntries(Array.from(screen.querySelectorAll('[data-vacancy-count]')).map(function mapCount(element) {
		return [element.dataset.vacancyCount, element];
	}));
	let editingRow = null;
	let nextId = list.querySelectorAll('[data-vacancy-row]').length + 1;

	const updateCount = function updateCount(key, change) {
		if (counts[key]) {
			counts[key].textContent = String(Math.max(0, Number(counts[key].textContent) + change));
		}
	};
	const getStatusLabel = function getStatusLabel(status) {
		return { published: 'Dipublikasikan', draft: 'Draft', closed: 'Ditutup' }[status];
	};
	const setStatus = function setStatus(row, status) {
		const previousStatus = row.dataset.status;
		if (previousStatus === status) {
			return;
		}
		updateCount(previousStatus, -1);
		updateCount(status, 1);
		row.dataset.status = status;
		const badge = row.querySelector('[data-vacancy-status]');
		badge.className = 'daw-vacancies__badge daw-vacancies__badge--' + status;
		badge.textContent = getStatusLabel(status);
		row.querySelector('[data-vacancy-toggle]').textContent = 'published' === status ? 'Tutup' : 'Publish';
	};
	const openDialog = function openDialog(row) {
		editingRow = row || null;
		form.reset();
		form.elements.title.value = row ? row.querySelector('[data-field="title"]').textContent : '';
		form.elements.dealer.value = row ? row.querySelector('[data-field="dealer"]').textContent : '';
		form.elements.region.value = row ? row.querySelector('[data-field="region"]').textContent : 'Sulawesi Utara';
		form.elements.type.value = row ? row.querySelector('[data-field="type"]').textContent : 'Full Time';
		form.elements.deadline.value = '';
		screen.querySelector('#vacancy-dialog-title').textContent = row ? 'Edit Lowongan' : 'Buat Lowongan';
		dialog.showModal();
	};

	screen.querySelector('[data-open-vacancy-dialog]').addEventListener('click', function createVacancy() {
		openDialog(null);
	});
	screen.querySelectorAll('[data-close-vacancy-dialog]').forEach(function bindClose(button) {
		button.addEventListener('click', function closeDialog() {
			dialog.close();
		});
	});
	form.addEventListener('submit', function saveVacancy(event) {
		event.preventDefault();
		const values = {
			title: form.elements.title.value.trim(),
			dealer: form.elements.dealer.value.trim(),
			region: form.elements.region.value.trim(),
			type: form.elements.type.value,
			deadline: new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(form.elements.deadline.value + 'T00:00:00')),
		};
		if (editingRow) {
			Object.entries(values).forEach(function updateField(entry) {
				const field = editingRow.querySelector('[data-field="' + entry[0] + '"]');
				if (field) {
					field.textContent = entry[1];
				}
			});
			feedback.textContent = 'Perubahan diperbarui pada preview ini.';
		} else {
			const row = list.firstElementChild.cloneNode(true);
			row.dataset.vacancyId = String(nextId++);
			row.dataset.status = 'draft';
			Object.entries(values).forEach(function setNewField(entry) {
				const field = row.querySelector('[data-field="' + entry[0] + '"]');
				if (field) {
					field.textContent = entry[1];
				}
			});
			row.querySelector('td:first-child small').textContent = 'Diposting: ' + new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date());
			const badge = row.querySelector('[data-vacancy-status]');
			badge.className = 'daw-vacancies__badge daw-vacancies__badge--draft';
			badge.textContent = 'Draft';
			row.querySelector('[data-vacancy-toggle]').textContent = 'Publish';
			list.append(row);
			updateCount('total', 1);
			updateCount('draft', 1);
			feedback.textContent = 'Lowongan ditambahkan sebagai draft pada preview ini.';
		}
		feedback.hidden = false;
		dialog.close();
	});
	list.addEventListener('click', function handleVacancyAction(event) {
		const button = event.target.closest('button');
		const row = button && button.closest('[data-vacancy-row]');
		if (!button || !row) {
			return;
		}
		if (button.hasAttribute('data-vacancy-edit')) {
			openDialog(row);
		} else if (button.hasAttribute('data-vacancy-toggle')) {
			setStatus(row, 'published' === row.dataset.status ? 'closed' : 'published');
			feedback.textContent = 'Status diperbarui pada preview ini.';
			feedback.hidden = false;
		} else if (button.hasAttribute('data-vacancy-archive')) {
			updateCount('total', -1);
			updateCount(row.dataset.status, -1);
			row.remove();
			feedback.textContent = 'Lowongan diarsipkan pada preview ini.';
			feedback.hidden = false;
		}
	});
})();
