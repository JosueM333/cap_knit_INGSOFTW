// accessibility.js – Accesibilidad WCAG 2.2 AA

document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const html = document.documentElement;

    // --- 1. Widget de accesibilidad (Stickman) ---
    const accessBtn = document.getElementById("access-btn");
    const accessMenu = document.getElementById("access-menu");

    if (accessBtn && accessMenu) {
        const toggleMenu = (show) => {
            accessMenu.classList.toggle("show", show);
            accessMenu.hidden = !show;
            accessBtn.setAttribute("aria-expanded", show);
        };

        accessBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleMenu(accessBtn.getAttribute("aria-expanded") !== "true");
        });

        document.addEventListener("click", (e) => {
            if (accessMenu.classList.contains("show") &&
                !accessMenu.contains(e.target) &&
                e.target !== accessBtn) {
                toggleMenu(false);
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && accessMenu.classList.contains("show")) {
                toggleMenu(false);
                accessBtn.focus();
            }
        });
    }

    // --- 2. Alto contraste ---
    const contrastToggles = document.querySelectorAll("#contrast-toggle-widget");

    const updateContrastUI = (isActive) => {
        contrastToggles.forEach(btn => {
            btn.setAttribute("aria-pressed", isActive);
            const span = btn.querySelector("span");
            if (span) span.textContent = isActive ? "Desactivar contraste" : "Alto contraste";
            
            btn.classList.toggle("active", isActive);
            btn.classList.toggle("bg-warning", isActive);
            btn.classList.toggle("text-dark", isActive);
        });
    };

    const enableContrast = () => {
        body.classList.add("high-contrast");
        localStorage.setItem("highContrast", "true");
        updateContrastUI(true);
        announce("Modo de alto contraste activado");
    };

    const disableContrast = () => {
        body.classList.remove("high-contrast");
        localStorage.setItem("highContrast", "false");
        updateContrastUI(false);
        announce("Modo de alto contraste desactivado");
    };

    const savedContrast = localStorage.getItem("highContrast");
    if (savedContrast === "true") enableContrast();

    contrastToggles.forEach(btn => {
        btn.addEventListener("click", () => {
            body.classList.contains("high-contrast") ? disableContrast() : enableContrast();
        });
    });

    // --- 3. Subrayar Enlaces ---
    const linksToggle = document.getElementById("links-toggle");

    const toggleLinks = () => {
        body.classList.toggle("show-underlines");
        const isActive = body.classList.contains("show-underlines");
        localStorage.setItem("showUnderlines", isActive);
        
        if(linksToggle) {
             linksToggle.setAttribute("aria-pressed", isActive);
             linksToggle.classList.toggle("active", isActive);
             linksToggle.querySelector("span").textContent = isActive ? "Quitar subrayado" : "Subrayar Enlaces";
        }
        announce(isActive ? "Subrayado de enlaces activado" : "Subrayado de enlaces desactivado");
    };

    if (localStorage.getItem("showUnderlines") === "true") toggleLinks();
    linksToggle?.addEventListener("click", toggleLinks);

    // --- 4. Tamaño de fuente ---
    const btnDecrease = document.getElementById("btn-decrease-font");
    const btnReset = document.getElementById("btn-reset-font");
    const btnIncrease = document.getElementById("btn-increase-font");

    const fontClasses = ["font-sm", "", "font-lg", "font-xl"];
    let currentFontIndex = 1;

    const updateFont = (index) => {
        html.classList.remove("font-sm", "font-lg", "font-xl");
        if (fontClasses[index]) html.classList.add(fontClasses[index]);
        localStorage.setItem("fontSizeIndex", index);

        let msg = "Tamaño de letra normal";
        if (index === 0) msg = "Tamaño de letra disminuido";
        if (index > 1) msg = "Tamaño de letra aumentado";
        announce(msg);
    };

    const savedFont = localStorage.getItem("fontSizeIndex");
    if (savedFont !== null) {
        currentFontIndex = parseInt(savedFont);
        updateFont(currentFontIndex);
    }

    btnDecrease?.addEventListener("click", () => {
        if (currentFontIndex > 0) updateFont(--currentFontIndex);
    });
    btnIncrease?.addEventListener("click", () => {
        if (currentFontIndex < fontClasses.length - 1) updateFont(++currentFontIndex);
    });
    btnReset?.addEventListener("click", () => {
        currentFontIndex = 1;
        updateFont(currentFontIndex);
    });

    // --- 5. Skip link y Navegación ---
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

    // --- 6. Formularios accesibles ---
    document.querySelectorAll("form").forEach((form) => {
        const requiredInputs = form.querySelectorAll("[required]");
        requiredInputs.forEach((input) => {
            const label = form.querySelector(`label[for="${input.id}"]`);
            if (label) label.classList.add("required");
        });

        form.addEventListener("submit", (e) => {
            let firstError = null;
            let isValid = true;
            requiredInputs.forEach((input) => {
                if (!input.value.trim()) {
                    input.classList.add("is-invalid");
                    firstError ??= input;
                    isValid = false;
                } else {
                    input.classList.remove("is-invalid");
                }
            });
            if (!isValid) {
                e.preventDefault();
                firstError?.focus();
                announce("Hay errores en el formulario.");
            }
        });
    });

    // Función auxiliar
    function announce(message) {
        const live = document.createElement("div");
        live.className = "visually-hidden";
        live.setAttribute("aria-live", "polite");
        live.textContent = message;
        document.body.appendChild(live);
        setTimeout(() => live.remove(), 1000);
    }
});