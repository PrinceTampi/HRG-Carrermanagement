// public.js - Frontend JavaScript for the Recruitment Plugin public area.
(function recruitmentPublicScripts() {
  if (window.RecruitmentPublicScripts) {
    return;
  }
  window.RecruitmentPublicScripts = true;
  document.documentElement.classList.add("js");
  setupConsentButton();
  setupTokenCopy();
  setupScreenExplorer();
  setupPsychologicalTestProcessing();

  document.addEventListener("click", function handleRecruitmentClick(event) {
    const removeButton = event.target.closest("[data-remove-section]");
    if (removeButton) {
      const section = removeButton.closest("[data-repeatable]");
      if (section) {
        section.remove();
        renumberSections(removeButton.dataset.removeSection);
      }
      return;
    }

    const addButton = event.target.closest("[data-add]");
    if (!addButton) {
      return;
    }

    const type = addButton.dataset.add;
    const repeatable = document.querySelector(`[data-repeatable="${type}"]`);
    if (!repeatable) {
      return;
    }

    const clone = repeatable.cloneNode(true);
    const count =
      document.querySelectorAll(`[data-repeatable="${type}"]`).length + 1;
    const removeControl = document.createElement("button");
    removeControl.type = "button";
    removeControl.className = "daw-recruitment__remove-row";
    removeControl.dataset.removeSection = type;
    removeControl.textContent = "Hapus";
    clone.querySelector(".daw-recruitment__repeatable-title").textContent =
      `${type === "education" ? "PENDIDIKAN" : "PEKERJAAN"} ${count}`;
    clone
      .querySelector(".daw-recruitment__repeatable-title")
      .appendChild(removeControl);
    clone
      .querySelectorAll("input, select, textarea")
      .forEach(function clearField(field) {
        field.value = "";
        if (
          field.type === "file" ||
          field.type === "checkbox" ||
          field.type === "radio"
        ) {
          field.checked = false;
        }
      });
    repeatable.parentNode.insertBefore(clone, addButton);
  });

  function renumberSections(type) {
    document
      .querySelectorAll(`[data-repeatable="${type}"]`)
      .forEach(function updateSectionTitle(section, index) {
        const title = section.querySelector(
          ".daw-recruitment__repeatable-title",
        );
        const label = type === "education" ? "PENDIDIKAN" : "PEKERJAAN";
        const removeControl = title.querySelector("[data-remove-section]");
        title.textContent = `${label} ${index + 1}`;
        if (removeControl) {
          title.appendChild(removeControl);
        }
      });
  }

  function setupConsentButton() {
    const submitButton = document.querySelector("[data-submit-application]");
    const applicationForm = submitButton ? submitButton.closest("form") : null;
    const consentCheckboxes = document.querySelectorAll("[data-pdp-consent]");
    if (!submitButton || !applicationForm || !consentCheckboxes.length) {
      return;
    }

    const updateSubmitState = function updateSubmitState() {
      const allConsented = [...consentCheckboxes].every(
        (checkbox) => checkbox.checked,
      );
      submitButton.disabled = !allConsented;
      submitButton.setAttribute("aria-disabled", String(!allConsented));
    };

    consentCheckboxes.forEach((checkbox) =>
      checkbox.addEventListener("change", updateSubmitState),
    );
    applicationForm.addEventListener("submit", function lockSubmitButton() {
      if (submitButton.disabled) {
        return;
      }
      submitButton.disabled = true;
      submitButton.textContent = "Submitting...";
    });
    updateSubmitState();
  }

  function setupTokenCopy() {
    const copyButton = document.querySelector("[data-copy-token]");
    const tokenElement = document.querySelector("[data-application-token]");
    const feedback = document.querySelector(".daw-recruitment__copy-feedback");
    if (!copyButton || !tokenElement) {
      return;
    }

    copyButton.addEventListener("click", function copyApplicationToken() {
      const token = tokenElement.textContent.trim();
      const showSuccess = function showCopySuccess() {
        copyButton.classList.add("is-copied");
        copyButton.setAttribute("aria-label", "Kode lamaran berhasil disalin");
        if (feedback) {
          feedback.textContent = "Tersalin";
        }
      };

      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(token).then(showSuccess);
        return;
      }

      const selection = window.getSelection();
      const range = document.createRange();
      range.selectNodeContents(tokenElement);
      selection.removeAllRanges();
      selection.addRange(range);
      document.execCommand("copy");
      selection.removeAllRanges();
      showSuccess();
    });
  }

  function setupScreenExplorer() {
    const explorer = document.querySelector("[data-screen-explorer]");
    if (!explorer) {
      return;
    }

    const toggle = explorer.querySelector("[data-screen-explorer-toggle]");
    const close = explorer.querySelector("[data-screen-explorer-close]");
    const panel = explorer.querySelector(".daw-screen-explorer__panel");
    const setOpen = function setOpen(isOpen) {
      panel.hidden = !isOpen;
      toggle.setAttribute("aria-expanded", String(isOpen));
      explorer.classList.toggle("is-open", isOpen);
    };

    toggle.addEventListener("click", function toggleExplorer() {
      setOpen(panel.hidden);
    });
    close.addEventListener("click", function closeExplorer() {
      setOpen(false);
    });
    document.addEventListener(
      "keydown",
      function closeExplorerWithEscape(event) {
        if ("Escape" === event.key) {
          setOpen(false);
        }
      },
    );

    const groupButtons = explorer.querySelectorAll(
      "[data-screen-explorer-group-toggle]",
    );
    groupButtons.forEach(function groupButton(groupButtonElement) {
      const parentGroup = groupButtonElement.closest(
        ".daw-screen-explorer__group",
      );
      const body = parentGroup
        ? parentGroup.querySelector(".daw-screen-explorer__group-body")
        : null;
      if (!parentGroup || !body) {
        return;
      }
      groupButtonElement.addEventListener("click", function toggleGroup() {
        const isOpen = parentGroup.classList.contains("is-open");
        parentGroup.classList.toggle("is-open", !isOpen);
        parentGroup.classList.toggle("is-collapsed", isOpen);
        groupButtonElement.setAttribute("aria-expanded", String(!isOpen));
        body.hidden = isOpen;
      });
    });
  }

  function setupPsychologicalTestProcessing() {
    const processingCard = document.querySelector("[data-psych-processing]");
    if (!processingCard) {
      return;
    }

    const nextUrl = processingCard.dataset.nextUrl;
    const progressBar = processingCard.querySelector(
      "[data-psych-progress-bar]",
    );
    const progressValue = processingCard.querySelector(
      "[data-psych-progress-value]",
    );
    const progressTrack = processingCard.querySelector('[role="progressbar"]');
    if (!nextUrl || !progressBar || !progressValue || !progressTrack) {
      return;
    }

    let progress = 0;
    const updateProgress = function updateProgress() {
      progress = Math.min(progress + 1, 100);
      progressBar.style.width = `${progress}%`;
      progressValue.textContent = `${progress}%`;
      progressTrack.setAttribute("aria-valuenow", String(progress));

      if (progress < 100) {
        window.setTimeout(updateProgress, 35);
        return;
      }

      window.setTimeout(function advancePsychologicalTest() {
        window.location.href = nextUrl;
      }, 500);
    };

    updateProgress();
  }
})();
