document.addEventListener("DOMContentLoaded", () => {

    /* ---------- Show / hide password ---------- */
    document.querySelectorAll(".toggle-pw").forEach(btn => {
        btn.addEventListener("click", () => {
            const input = btn.parentElement.querySelector("input");
            const show = input.type === "password";
            input.type = show ? "text" : "password";
            btn.textContent = show ? "🙈" : "👁";
        });
    });

    /* ---------- Registration validation ---------- */
    const form = document.getElementById("register-form");
    if (!form) return;   // login page stops here

    const rules = {
        first_name: v => v.trim() ? "" : "First name is required.",
        last_name:  v => v.trim() ? "" : "Last name is required.",
        email:      v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) ? "" : "Enter a valid email address.",
        phone:      v => /^\+?[0-9]{9,15}$/.test(v.trim()) ? "" : "Use 9-15 digits, e.g. 0712345678.",
        gender:     v => v ? "" : "Please select a gender.",
        age:        v => (/^\d+$/.test(v) && v >= 18 && v <= 100) ? "" : "Age must be between 18 and 100.",
        city:       v => v ? "" : "Please select a city.",
        password:   v => v.length >= 8 ? "" : "Use at least 8 characters.",
        confirm_password: v => (v && v === form.elements.password.value) ? "" : "Passwords do not match."
    };

    const touched = new Set();

    function validate(name) {
        const field = form.elements[name];
        const msg = rules[name](field.value);
        const hint = form.querySelector(`.hint[data-for="${name}"]`);

        hint.textContent = msg || "✓ Looks good";
        hint.className = "hint " + (msg ? "bad" : "ok");

        if (field.classList) {   // radios have no single element to colour
            field.classList.toggle("is-invalid", !!msg);
            field.classList.toggle("is-valid", !msg);
        }
        return !msg;
    }

    Object.keys(rules).forEach(name => {
        const field = form.elements[name];
        const handler = () => {
            touched.add(name);
            validate(name);
            // password changed -> re-check confirmation
            if (name === "password" && touched.has("confirm_password")) validate("confirm_password");
        };
        if (field instanceof RadioNodeList) {
            field.forEach(r => r.addEventListener("change", handler));
        } else {
            field.addEventListener("input", handler);
            field.addEventListener("blur", handler);
            field.addEventListener("change", handler);
        }
    });

    /* ---------- Password strength bar ---------- */
    const pw = form.elements.password;
    const bar = document.querySelector(".strength span");
    const label = document.querySelector(".strength-text");
    const levels = [
        { text: "Too weak",  color: "#e53935", width: "20%" },
        { text: "Weak",      color: "#fb8c00", width: "40%" },
        { text: "Fair",      color: "#fdd835", width: "60%" },
        { text: "Good",      color: "#7cb342", width: "80%" },
        { text: "Strong",    color: "#2e7d32", width: "100%" }
    ];

    pw.addEventListener("input", () => {
        const v = pw.value;
        if (!v) { bar.style.width = "0"; label.textContent = ""; return; }

        let score = 0;
        if (v.length >= 8) score++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
        if (/\d/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        if (v.length < 8) score = Math.min(score, 1);

        const lvl = levels[score];
        bar.style.width = lvl.width;
        bar.style.background = lvl.color;
        label.textContent = "Strength: " + lvl.text;
    });

    /* ---------- Block submit if anything is invalid ---------- */
    form.addEventListener("submit", e => {
        let firstBad = null;
        Object.keys(rules).forEach(name => {
            if (!validate(name) && !firstBad) firstBad = form.elements[name];
        });
        if (firstBad) {
            e.preventDefault();
            (firstBad instanceof RadioNodeList ? firstBad[0] : firstBad).focus();
        }
    });
});