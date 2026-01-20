// accessibility.js - Alto Contraste

document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const contrastToggle = document.getElementById("contrast-toggle");

    const enableContrast = () => {
        body.classList.add("high-contrast");
        contrastToggle?.setAttribute("aria-pressed", "true");
        contrastToggle?.setAttribute(
            "aria-label",
            "Desactivar modo de alto contraste"
        );
        localStorage.setItem("highContrast", "true");
        announce("Modo de alto contraste activado");
    };

    const disableContrast = () => {
        body.classList.remove("high-contrast");
        contrastToggle?.setAttribute("aria-pressed", "false");
        contrastToggle?.setAttribute(
            "aria-label",
            "Activar modo de alto contraste"
        );
        localStorage.setItem("highContrast", "false");
        announce("Modo de alto contraste desactivado");
    };

    // Restaurar preferencia guardada
    const saved = localStorage.getItem("highContrast");
    if (saved === "true") enableContrast();

    // Respetar preferencia del sistema SOLO si el usuario no decidió
    const prefersHighContrast = window.matchMedia("(prefers-contrast: high)");
    if (prefersHighContrast.matches && saved === null) {
        enableContrast();
    }

    // Toggle manual
    contrastToggle?.addEventListener("click", () => {
        body.classList.contains("high-contrast")
            ? disableContrast()
            : enableContrast();
    });

    const skipLink = document.querySelector(".skip-link");
    skipLink?.addEventListener("click", (e) => {
        e.preventDefault();
        const target = document.querySelector(skipLink.getAttribute("href"));
        if (target) {
            target.setAttribute("tabindex", "-1");
            target.focus();
            target.scrollIntoView({ behavior: "smooth" });
        }
    });

    document.querySelectorAll("form").forEach((form) => {
        const requiredInputs = form.querySelectorAll("[required]");

        // Marcar labels requeridos
        requiredInputs.forEach((input) => {
            const label = form.querySelector(`label[for="${input.id}"]`);
            if (label) label.classList.add("required");
        });

        form.addEventListener("submit", (e) => {
            let isValid = true;
            let firstError = null;

            requiredInputs.forEach((input) => {
                if (!input.value.trim()) {
                    input.classList.add("is-invalid");
                    if (!firstError) firstError = input;
                    isValid = false;
                } else {
                    input.classList.remove("is-invalid");
                }
            });

            if (!isValid) {
                e.preventDefault();
                firstError?.focus();
                announce("Hay errores en el formulario");
            }
        });
    });

    function announce(message) {
        const live = document.createElement("div");
        live.className = "sr-only";
        live.setAttribute("aria-live", "polite");
        live.textContent = message;

        document.body.appendChild(live);

        setTimeout(() => live.remove(), 1000);
    }
});