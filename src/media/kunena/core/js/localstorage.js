document.addEventListener("DOMContentLoaded", function () {
    // Function to clear localStorage based on prefix
    function clearLocalStorageByPrefix(prefix) {
        const keysToRemove = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith(prefix)) {
                keysToRemove.push(key);
            }
        }
        keysToRemove.forEach((key) => {
            localStorage.removeItem(key);
        });
        console.log(
            `Removed ${keysToRemove.length} localStorage items with prefix "${prefix}".`
        );
    }

    // Check for URL parameter on page load
    // [URL]?clear-kunena-localstorage=true
    const urlParams = new URLSearchParams(window.location.search);
    const clearStateParam = urlParams.get("clear-kunena-localstorage");

    if (clearStateParam === "true") {
        clearLocalStorageByPrefix("kunena-");
    }

    // Logic for Collapse elements
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

    // First, select all alert boxes that are dismissible
    const dismissibleAlerts = document.querySelectorAll(".alert-dismissible");

    dismissibleAlerts.forEach((alertElement) => {
        if (!alertElement.id) return; // Skip if no ID is found

        const alertId = alertElement.id;
        const storageKey = `kunena-alert-dismissed-${alertId}`;

        // 1. On page load, check if the alert was previously dismissed
        if (localStorage.getItem(storageKey) === "true") {
            alertElement.classList.add("d-none");
        }

        // 2. Add event listener to the alert to save its state when closed
        alertElement.addEventListener("closed.bs.alert", function () {
            localStorage.setItem(storageKey, "true");
        });
    });
});
