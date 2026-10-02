// admin.js — JavaScript for the Recruitment Plugin HRD admin dashboard.
// TODO: Tambahkan interaksi admin seperti konfirmasi delete, filter tabel, dll.
document.documentElement.classList.add("js");

(function recruitmentApplicantTabs() {
  const tabContainer = document.querySelector("[data-applicant-tabs]");
  if (!tabContainer) {
    return;
  }

  const tabButtons = Array.from(
    tabContainer.querySelectorAll("[data-applicant-tab]"),
  );
  const panels = Array.from(
    tabContainer.querySelectorAll("[data-applicant-panel]"),
  );

  tabButtons.forEach(function bindTab(button) {
    button.addEventListener("click", function activateTab() {
      const target = button.dataset.applicantTab;
      tabButtons.forEach(function updateButton(item) {
        const isActive = item === button;
        item.classList.toggle("is-active", isActive);
        item.setAttribute("aria-selected", String(isActive));
      });
      panels.forEach(function updatePanel(panel) {
        const shouldShow = panel.dataset.applicantPanel === target;
        panel.classList.toggle("is-active", shouldShow);
        panel.hidden = !shouldShow;
      });
    });
  });
})();

(function recruitmentDashboardNavigation() {
  const dashboard = document.querySelector("[data-recruitment-dashboard]");
  if (!dashboard) {
    return;
  }

  const collapseButton = dashboard.querySelector("[data-sidebar-collapse]");
  const navigationLinks = Array.from(
    dashboard.querySelectorAll(
      ".recruitment-dashboard__navigation a[data-dashboard-stage]",
    ),
  );
  const isMobile = window.matchMedia("(max-width: 782px)").matches;
  dashboard.dataset.sidebarCollapsed = String(isMobile);

  const updateCollapseButton = function updateCollapseButton() {
    const isExpanded = "true" !== dashboard.dataset.sidebarCollapsed;
    collapseButton.setAttribute("aria-expanded", String(isExpanded));
    collapseButton.title = isExpanded ? "Ciutkan navigasi" : "Buka navigasi";
  };

  updateCollapseButton();
  collapseButton.addEventListener("click", function toggleSidebar() {
    dashboard.dataset.sidebarCollapsed = String(
      "true" !== dashboard.dataset.sidebarCollapsed,
    );
    updateCollapseButton();
  });

  navigationLinks.forEach(function bindDashboardNavigation(link) {
    link.addEventListener("click", function filterDashboard(event) {
      event.preventDefault();
      const stage = link.dataset.dashboardStage;
      const stageButton = dashboard.querySelector(
        '[data-stage-filter="' + stage + '"]',
      );
      if (stageButton) {
        stageButton.click();
      }
      dashboard
        .querySelectorAll(".recruitment-dashboard__navigation a")
        .forEach(function updateActiveLink(item) {
          item.classList.toggle("is-active", item === link);
          item.removeAttribute("aria-current");
        });
      link.setAttribute("aria-current", "page");
      dashboard
        .querySelector("#candidate-list-title")
        .scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });
})();

(function recruitmentDashboardFilters() {
  const dashboard = document.querySelector("[data-recruitment-dashboard]");
  if (!dashboard) {
    return;
  }

  const stageButtons = Array.from(
    dashboard.querySelectorAll("[data-stage-filter]"),
  );
  const searchInput = dashboard.querySelector("[data-candidate-search]");
  const dealerSelect = dashboard.querySelector("[data-candidate-dealer]");
  const vacancySelect = dashboard.querySelector("[data-candidate-vacancy]");
  const resetButton = dashboard.querySelector("[data-candidate-reset]");
  const emptyMessage = dashboard.querySelector("[data-candidate-empty]");
  const visibleCount = dashboard.querySelector("[data-candidate-count]");
  let activeStage = "all";

  const applyFilters = function applyFilters() {
    const search = searchInput.value.trim().toLocaleLowerCase();
    const cards = Array.from(
      dashboard.querySelectorAll("[data-candidate-card]"),
    );
    let count = 0;

    cards.forEach(function filterCandidate(card) {
      const matchesStage =
        "all" === activeStage || activeStage === card.dataset.stage;
      const matchesSearch = !search || card.dataset.search.includes(search);
      const matchesDealer =
        !dealerSelect.value || dealerSelect.value === card.dataset.dealer;
      const matchesVacancy =
        !vacancySelect.value || vacancySelect.value === card.dataset.vacancy;
      const isVisible =
        matchesStage && matchesSearch && matchesDealer && matchesVacancy;

      card.hidden = !isVisible;
      if (isVisible) {
        count++;
      }
    });

    visibleCount.textContent = String(count);
    emptyMessage.hidden = count > 0;
  };

  stageButtons.forEach(function bindStageFilter(button) {
    button.addEventListener("click", function selectStage() {
      activeStage = button.dataset.stageFilter;
      stageButtons.forEach(function updateStageButton(item) {
        const isActive = item === button;
        item.classList.toggle("is-active", isActive);
        item.setAttribute("aria-pressed", String(isActive));
      });
      applyFilters();
    });
  });

  const requestedStage = new URLSearchParams(window.location.search).get(
    "stage",
  );
  const requestedStageButton = stageButtons.find(
    function findRequestedStage(button) {
      return button.dataset.stageFilter === requestedStage;
    },
  );
  if (requestedStageButton) {
    requestedStageButton.click();
  }

  searchInput.addEventListener("input", applyFilters);
  dealerSelect.addEventListener("change", applyFilters);
  vacancySelect.addEventListener("change", applyFilters);
  resetButton.addEventListener("click", function resetFilters() {
    searchInput.value = "";
    dealerSelect.value = "";
    vacancySelect.value = "";
    stageButtons[0].click();
  });
})();

(function userInterviewNotes() {
  const screen = document.querySelector(".daw-user-interview--notes");
  if (!screen) {
    return;
  }

  screen
    .querySelectorAll("[data-choice-group]")
    .forEach(function bindChoiceGroup(group) {
      group.querySelectorAll("button").forEach(function bindChoice(button) {
        button.addEventListener("click", function selectChoice() {
          group
            .querySelectorAll("button")
            .forEach(function clearChoice(choice) {
              const selected = choice === button;
              choice.classList.toggle("is-selected", selected);
              choice.setAttribute("aria-pressed", String(selected));
            });
        });
      });
    });

  const form = screen.querySelector("[data-user-interview-form]");
  const saveFeedback = screen.querySelector("[data-interview-feedback]");
  form.addEventListener("submit", function saveInterviewNotes(event) {
    event.preventDefault();
    saveFeedback.textContent = "Catatan diperbarui pada preview ini.";
    saveFeedback.hidden = false;
  });

  const decisionFeedback = screen.querySelector("[data-decision-feedback]");
  screen
    .querySelectorAll("[data-final-decision]")
    .forEach(function bindDecision(button) {
      button.addEventListener("click", function chooseDecision() {
        screen
          .querySelectorAll("[data-final-decision]")
          .forEach(function updateDecision(choice) {
            const selected = choice === button;
            choice.classList.toggle("is-selected", selected);
            choice.setAttribute("aria-pressed", String(selected));
          });
        decisionFeedback.textContent =
          "Keputusan dipilih: " +
          button.textContent.trim().replace(/^[✓×]\s*/, "");
        decisionFeedback.hidden = false;
      });
    });
})();

(function vacancyPreview() {
  const screen = document.querySelector(".daw-vacancies");
  if (!screen) {
    return;
  }

  const dialog = screen.querySelector("[data-vacancy-dialog]");
  const form = screen.querySelector("[data-vacancy-form]");
  const list = screen.querySelector("[data-vacancy-list]");
  const feedback = screen.querySelector("[data-vacancy-feedback]");
  const counts = Object.fromEntries(
    Array.from(screen.querySelectorAll("[data-vacancy-count]")).map(
      function mapCount(element) {
        return [element.dataset.vacancyCount, element];
      },
    ),
  );
  let editingRow = null;
  let nextId = list.querySelectorAll("[data-vacancy-row]").length + 1;

  const updateCount = function updateCount(key, change) {
    if (counts[key]) {
      counts[key].textContent = String(
        Math.max(0, Number(counts[key].textContent) + change),
      );
    }
  };
  const getStatusLabel = function getStatusLabel(status) {
    return { published: "Dipublikasikan", draft: "Draft", closed: "Ditutup" }[
      status
    ];
  };
  const setStatus = function setStatus(row, status) {
    const previousStatus = row.dataset.status;
    if (previousStatus === status) {
      return;
    }
    updateCount(previousStatus, -1);
    updateCount(status, 1);
    row.dataset.status = status;
    const badge = row.querySelector("[data-vacancy-status]");
    badge.className = "daw-vacancies__badge daw-vacancies__badge--" + status;
    badge.textContent = getStatusLabel(status);
    row.querySelector("[data-vacancy-toggle]").textContent =
      "published" === status ? "Tutup" : "Publish";
  };
  const openDialog = function openDialog(row) {
    editingRow = row || null;
    form.reset();
    form.elements.title.value = row
      ? row.querySelector('[data-field="title"]').textContent
      : "";
    form.elements.dealer.value = row
      ? row.querySelector('[data-field="dealer"]').textContent
      : "";
    form.elements.region.value = row
      ? row.querySelector('[data-field="region"]').textContent
      : "Sulawesi Utara";
    form.elements.type.value = row
      ? row.querySelector('[data-field="type"]').textContent
      : "Full Time";
    form.elements.deadline.value = "";
    screen.querySelector("#vacancy-dialog-title").textContent = row
      ? "Edit Lowongan"
      : "Buat Lowongan";
    dialog.showModal();
  };

  screen
    .querySelector("[data-open-vacancy-dialog]")
    .addEventListener("click", function createVacancy() {
      openDialog(null);
    });
  screen
    .querySelectorAll("[data-close-vacancy-dialog]")
    .forEach(function bindClose(button) {
      button.addEventListener("click", function closeDialog() {
        dialog.close();
      });
    });
  form.addEventListener("submit", function saveVacancy(event) {
    event.preventDefault();
    const values = {
      title: form.elements.title.value.trim(),
      dealer: form.elements.dealer.value.trim(),
      region: form.elements.region.value.trim(),
      type: form.elements.type.value,
      deadline: new Intl.DateTimeFormat("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
      }).format(new Date(form.elements.deadline.value + "T00:00:00")),
    };
    if (editingRow) {
      Object.entries(values).forEach(function updateField(entry) {
        const field = editingRow.querySelector(
          '[data-field="' + entry[0] + '"]',
        );
        if (field) {
          field.textContent = entry[1];
        }
      });
      feedback.textContent = "Perubahan diperbarui pada preview ini.";
    } else {
      const row = list.firstElementChild.cloneNode(true);
      row.dataset.vacancyId = String(nextId++);
      row.dataset.status = "draft";
      Object.entries(values).forEach(function setNewField(entry) {
        const field = row.querySelector('[data-field="' + entry[0] + '"]');
        if (field) {
          field.textContent = entry[1];
        }
      });
      row.querySelector("td:first-child small").textContent =
        "Diposting: " +
        new Intl.DateTimeFormat("id-ID", {
          day: "numeric",
          month: "long",
          year: "numeric",
        }).format(new Date());
      const badge = row.querySelector("[data-vacancy-status]");
      badge.className = "daw-vacancies__badge daw-vacancies__badge--draft";
      badge.textContent = "Draft";
      row.querySelector("[data-vacancy-toggle]").textContent = "Publish";
      list.append(row);
      updateCount("total", 1);
      updateCount("draft", 1);
      feedback.textContent =
        "Lowongan ditambahkan sebagai draft pada preview ini.";
    }
    feedback.hidden = false;
    dialog.close();
  });
  list.addEventListener("click", function handleVacancyAction(event) {
    const button = event.target.closest("button");
    const row = button && button.closest("[data-vacancy-row]");
    if (!button || !row) {
      return;
    }
    if (button.hasAttribute("data-vacancy-edit")) {
      openDialog(row);
    } else if (button.hasAttribute("data-vacancy-toggle")) {
      setStatus(
        row,
        "published" === row.dataset.status ? "closed" : "published",
      );
      feedback.textContent = "Status diperbarui pada preview ini.";
      feedback.hidden = false;
    } else if (button.hasAttribute("data-vacancy-archive")) {
      updateCount("total", -1);
      updateCount(row.dataset.status, -1);
      row.remove();
      feedback.textContent = "Lowongan diarsipkan pada preview ini.";
      feedback.hidden = false;
    }
  });
})();

(function recruitmentFormBuilder() {
  const builder = document.querySelector("[data-form-builder]");
  if (!builder) {
    return;
  }

  const sectionList = builder.querySelector("[data-form-sections]");
  const sectionTemplate = builder.parentElement.querySelector(
    "[data-section-template]",
  );
  const questionTemplate = builder.parentElement.querySelector(
    "[data-question-template]",
  );
  const typeLabels = {
    short: "Jawaban Singkat",
    paragraph: "Paragraf / Jawaban Panjang",
    number: "Angka",
    date: "Tanggal",
    email: "Email",
    phone: "Nomor Telepon",
    file: "Upload File",
    dropdown: "Pilihan Dropdown",
    single_choice: "Pilihan Satu Jawaban",
  };

  const refresh = function refreshBuilder() {
    const sections = Array.from(
      sectionList.querySelectorAll("[data-form-section]"),
    );
    let questionTotal = 0;
    sections.forEach(function updateSection(section, sectionIndex) {
      section.querySelector("[data-section-number]").textContent = String(
        sectionIndex + 1,
      ).padStart(2, "0");
      const title = section.querySelector("[data-section-title]");
      const description = section.querySelector("[data-section-description]");
      title.name = `form_sections[${sectionIndex}][title]`;
      description.name = `form_sections[${sectionIndex}][description]`;
      const questions = Array.from(
        section.querySelectorAll("[data-form-question]"),
      );
      questionTotal += questions.length;
      section.querySelector("[data-section-question-count]").textContent =
        String(questions.length);
      questions.forEach(function updateQuestion(question, questionIndex) {
        question.querySelector("[data-question-number]").textContent = String(
          questionIndex + 1,
        );
        const fields = {
          key: question.querySelector("[data-question-key]"),
          title: question.querySelector("[data-question-title]"),
          type: question.querySelector("[data-question-type]"),
          options: question.querySelector("[data-question-options-value]"),
          required: question.querySelector("[data-question-required-input]"),
        };
        Object.keys(fields).forEach(function nameQuestionField(field) {
          if (fields[field]) {
            fields[field].name =
              `form_sections[${sectionIndex}][questions][${questionIndex}][${field}]`;
          }
        });
      });
    });
    builder.querySelector("[data-section-count]").textContent = String(
      sections.length,
    );
    builder.querySelector("[data-question-count]").textContent =
      String(questionTotal);
  };

  const updateQuestion = function updateQuestion(question) {
    const title = question.querySelector("[data-question-title]");
    const type = question.querySelector("[data-question-type]");
    const required = question.querySelector("[data-question-required-input]");
    question.querySelector("[data-question-label]").textContent =
      title.value.trim() || "Pertanyaan baru";
    question.querySelector("[data-question-type-label]").textContent =
      typeLabels[type.value] || type.value;
    question.querySelector("[data-question-required]").textContent =
      required.checked ? "Wajib diisi" : "Opsional";
    question.querySelector("[data-question-options]").hidden = ![
      "dropdown",
      "single_choice",
    ].includes(type.value);
  };

  const addQuestion = function addQuestion(section) {
    const question = questionTemplate.content.firstElementChild.cloneNode(true);
    question.querySelector("[data-question-key]").value =
      `field_${Date.now()}_${Math.random().toString(36).slice(2, 7)}`;
    section.querySelector("[data-section-questions]").append(question);
    refresh();
    updateQuestion(question);
    question.querySelector("[data-question-title]").focus();
  };

  const moveItem = function moveItem(item, direction) {
    const sibling =
      "up" === direction
        ? item.previousElementSibling
        : item.nextElementSibling;
    if (!sibling) {
      return;
    }
    if ("up" === direction) {
      item.parentElement.insertBefore(item, sibling);
    } else {
      item.parentElement.insertBefore(sibling, item);
    }
    refresh();
  };

  builder.addEventListener("input", function updateQuestionOnInput(event) {
    const question = event.target.closest("[data-form-question]");
    if (question && event.target.matches("[data-question-title]")) {
      updateQuestion(question);
    }
  });
  builder.addEventListener("change", function updateQuestionOnChange(event) {
    const question = event.target.closest("[data-form-question]");
    if (
      question &&
      event.target.matches(
        "[data-question-type], [data-question-required-input]",
      )
    ) {
      updateQuestion(question);
    }
  });

  builder.addEventListener("click", function handleBuilderClick(event) {
    const target = event.target.closest("button");
    if (!target) {
      return;
    }
    if (target.matches("[data-add-section]")) {
      const section = sectionTemplate.content.firstElementChild.cloneNode(true);
      sectionList.append(section);
      refresh();
      section.querySelector("[data-section-title]").focus();
    } else if (target.matches("[data-add-question]")) {
      addQuestion(target.closest("[data-form-section]"));
    } else if (target.matches("[data-remove-question]")) {
      target.closest("[data-form-question]").remove();
      refresh();
    } else if (target.matches("[data-remove-section]")) {
      if (
        sectionList.querySelectorAll("[data-form-section]").length > 1 &&
        window.confirm("Hapus section beserta seluruh pertanyaannya?")
      ) {
        target.closest("[data-form-section]").remove();
        refresh();
      }
    } else if (target.matches("[data-move-section]")) {
      moveItem(
        target.closest("[data-form-section]"),
        target.dataset.moveSection,
      );
    } else if (target.matches("[data-move-question]")) {
      moveItem(
        target.closest("[data-form-question]"),
        target.dataset.moveQuestion,
      );
    }
  });

  const preview = builder.parentElement.querySelector("[data-form-preview]");
  const previewContent = preview.querySelector("[data-preview-content]");
  builder.parentElement
    .querySelector("[data-preview-form]")
    .addEventListener("click", function openPreview() {
      previewContent.replaceChildren();
      sectionList
        .querySelectorAll("[data-form-section]")
        .forEach(function renderPreviewSection(section) {
          const previewSection = document.createElement("section");
          const heading = document.createElement("h3");
          heading.textContent = section.querySelector(
            "[data-section-title]",
          ).value;
          previewSection.append(heading);
          const description = section.querySelector(
            "[data-section-description]",
          ).value;
          if (description) {
            const text = document.createElement("p");
            text.textContent = description;
            previewSection.append(text);
          }
          section
            .querySelectorAll("[data-form-question]")
            .forEach(function renderPreviewQuestion(question) {
              const label = document.createElement("label");
              label.className = "recruitment-form-preview__field";
              const title = document.createElement("span");
              title.textContent = question.querySelector(
                "[data-question-title]",
              ).value;
              if (
                question.querySelector("[data-question-required-input]").checked
              ) {
                title.append(document.createTextNode(" *"));
              }
              label.append(title);
              const type = question.querySelector("[data-question-type]").value;
              let input;
              if ("paragraph" === type) {
                input = document.createElement("textarea");
              } else if (["dropdown", "single_choice"].includes(type)) {
                input = document.createElement("select");
                question
                  .querySelector("[data-question-options-value]")
                  .value.split(/\r?\n/)
                  .filter(Boolean)
                  .forEach(function addPreviewOption(optionText) {
                    const option = document.createElement("option");
                    option.textContent = optionText;
                    input.append(option);
                  });
              } else {
                input = document.createElement("input");
                input.type =
                  "file" === type
                    ? "file"
                    : ["short", "number", "date", "email", "phone"].includes(
                          type,
                        )
                      ? { phone: "tel" }[type] || type
                      : "text";
              }
              input.disabled = true;
              label.append(input);
              previewSection.append(label);
            });
          previewContent.append(previewSection);
        });
      preview.showModal();
    });
  preview
    .querySelector("[data-close-preview]")
    .addEventListener("click", function closePreview() {
      preview.close();
    });
  preview.addEventListener("click", function closePreviewOnBackdrop(event) {
    if (event.target === preview) {
      preview.close();
    }
  });

  sectionList.querySelectorAll("[data-form-question]").forEach(updateQuestion);
  refresh();
})();

(function recruitmentSidebarCollapse() {
  const sidebar = document.querySelector("[data-recruitment-sidebar]");
  if (!sidebar) {
    return;
  }

  const collapseButton = sidebar.querySelector("[data-sidebar-collapse]");
  const isMobile = window.matchMedia("(max-width: 782px)").matches;
  document.body.classList.toggle("daw-recruitment-sidebar-collapsed", isMobile);

  const updateButton = function updateButton() {
    const isExpanded = !document.body.classList.contains(
      "daw-recruitment-sidebar-collapsed",
    );
    collapseButton.setAttribute("aria-expanded", String(isExpanded));
    collapseButton.title = isExpanded ? "Ciutkan navigasi" : "Buka navigasi";
  };

  updateButton();
  collapseButton.addEventListener("click", function toggleSidebar() {
    document.body.classList.toggle("daw-recruitment-sidebar-collapsed");
    updateButton();
  });

  sidebar
    .querySelectorAll("[data-nav-group-toggle]")
    .forEach(function bindNavGroup(toggle) {
      const panel = document.getElementById(
        toggle.getAttribute("aria-controls"),
      );
      if (!panel) {
        return;
      }

      toggle.addEventListener("click", function toggleNavGroup() {
        const isExpanded = "true" === toggle.getAttribute("aria-expanded");
        toggle.setAttribute("aria-expanded", String(!isExpanded));
        panel.hidden = isExpanded;
      });
    });
})();

(function psychotestQuestionBank() {
  const bank = document.querySelector("[data-question-bank]");
  if (!bank) {
    return;
  }

  const dialog = bank.querySelector("[data-question-dialog]");
  const form = bank.querySelector("[data-question-form]");
  const prompt = form.querySelector("[data-question-prompt]");
  const choices = form.querySelector("[data-question-choices]");
  const choicesLabel = form.querySelector("[data-question-choices-label]");
  const answerLabel = form.querySelector("[data-question-answer-label]");
  const updateTypeFields = function updateTypeFields() {
    const type = form.querySelector("[data-question-type]").value;
    choicesLabel.hidden = "numeric" === type;
    answerLabel.hidden = !["multiple_choice", "numeric"].includes(type);
  };
  const openDialog = function openDialog(question) {
    form.reset();
    form.querySelector("[data-question-id]").value = question
      ? question.dataset.id
      : "";
    form.querySelector("[data-question-test]").value = question
      ? question.dataset.test
      : "iq";
    form.querySelector("[data-question-type]").value = question
      ? question.dataset.type
      : "multiple_choice";
    form.querySelector("[data-question-status]").value = question
      ? question.dataset.status
      : "active";
    prompt.value = question ? question.dataset.prompt : "";
    choices.value = question ? question.dataset.choices : "";
    form.querySelector("[data-question-answer]").value = question
      ? question.dataset.answer
      : "";
    bank.querySelector("#question-dialog-title").textContent = question
      ? "Edit Soal"
      : "Tambah Soal";
    updateTypeFields();
    dialog.showModal();
    prompt.focus();
  };

  bank
    .querySelector("[data-open-question-dialog]")
    .addEventListener("click", function addQuestion() {
      openDialog(null);
    });
  bank
    .querySelectorAll("[data-edit-question]")
    .forEach(function bindEditButton(button) {
      button.addEventListener("click", function editQuestion() {
        openDialog(button);
      });
    });
  form
    .querySelector("[data-question-type]")
    .addEventListener("change", updateTypeFields);
  dialog
    .querySelectorAll("[data-close-question-dialog]")
    .forEach(function bindClose(button) {
      button.addEventListener("click", function closeDialog() {
        dialog.close();
      });
    });
  dialog.addEventListener("click", function closeOnBackdrop(event) {
    if (event.target === dialog) {
      dialog.close();
    }
  });
  updateTypeFields();
})();

(function userDepartmentInterviewScoring() {
  const form = document.querySelector("[data-interview-rating]");
  if (!form) {
    return;
  }

  const total = form.querySelector("[data-score-total]");
  const scoreInputs = Array.from(
    form.querySelectorAll('input[type="radio"][name^="scores["]'),
  );
  const updateTotal = function updateTotal() {
    const selectedScores = scoreInputs.filter(function isSelected(input) {
      return input.checked;
    });
    const score = selectedScores.reduce(function sumScores(sum, input) {
      return sum + Number(input.value);
    }, 0);
    total.textContent = String(
      Math.round((score / (scoreInputs.length * 5)) * 100),
    );
  };

  form.addEventListener("change", function updateInterviewSelection(event) {
    const input = event.target;
    if (!(input instanceof HTMLInputElement) || "radio" !== input.type) {
      return;
    }
    const group = input.closest("fieldset");
    if (group) {
      group
        .querySelectorAll("label")
        .forEach(function updateSelectedLabel(label) {
          const radio = label.querySelector('input[type="radio"]');
          label.classList.toggle(
            "is-selected",
            Boolean(radio && radio.checked),
          );
        });
    }
    updateTotal();
  });

  updateTotal();
})();
