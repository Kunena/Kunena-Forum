document.addEventListener("DOMContentLoaded", function () {
    // --- Logic for Collapse elements ---
    const collapseToggles = document.querySelectorAll(
        '[data-bs-toggle="collapse"]'
    );
    const ignoredCollapseIds = new Set(["search", "searchoptions"]);

    collapseToggles.forEach((toggle) => {
        const targetId = toggle.getAttribute("data-bs-target");
        if (!targetId) return;

        const collapseElement = document.querySelector(targetId);
        if (!collapseElement || ignoredCollapseIds.has(collapseElement.id)) {
            return;
        }

        const storageKey = `kunena-collapse-state-${targetId}`;

        const storedState = localStorage.getItem(storageKey);

        if (storedState === "hide") {
            collapseElement.classList.remove("show");
            toggle.setAttribute("aria-expanded", "false");
        } else if (storedState === "show") {
            collapseElement.classList.add("show");
            toggle.setAttribute("aria-expanded", "true");
        }

        collapseElement.addEventListener("shown.bs.collapse", function () {
            localStorage.setItem(storageKey, "show");
        });

        collapseElement.addEventListener("hidden.bs.collapse", function () {
            localStorage.setItem(storageKey, "hide");
        });
    });

    // --- Corrected Logic for Dismissible Alerts ---
    // First, select all alert boxes that are dismissible
    const dismissibleAlerts = document.querySelectorAll(".alert-dismissible");

    dismissibleAlerts.forEach((alertElement) => {
        // For each alert, find the closest parent div with an ID starting with 'announcement'
        const alertContainer = alertElement.closest('div[id^="announcement"]');

        if (!alertContainer) return; // Skip if no matching parent is found

        const alertId = alertContainer.id;
        const storageKey = `kunena-alert-dismissed-${alertId}`;

        // 1. On page load, check if the alert was previously dismissed
        if (localStorage.getItem(storageKey) === "true") {
            alertContainer.classList.add("d-none");
        }

        // 2. Add event listener to the alert to save its state when closed
        alertElement.addEventListener("closed.bs.alert", function () {
            localStorage.setItem(storageKey, "true");
        });
    });
});
