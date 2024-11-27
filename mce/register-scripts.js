document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    form.addEventListener("submit", (event) => {
        const name = document.querySelector("input[name='name']").value.trim();
        const email = document.querySelector("input[name='email']").value.trim();
        const password = document.querySelector("input[name='password']").value.trim();

        if (!name || !email || !password) {
            event.preventDefault();
            alert("All fields are required. Please fill in the form completely.");
        }
    });
});

