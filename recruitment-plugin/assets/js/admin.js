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
