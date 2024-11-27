document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    form.addEventListener("submit", (event) => {
        const email = document.querySelector("input[name='email']").value.trim();
        const password = document.querySelector("input[name='password']").value.trim();

        if (!email || !password) {
            event.preventDefault();
            alert("Please fill in both email and password.");
        }
    });
});
