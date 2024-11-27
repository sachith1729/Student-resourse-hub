async function loadResources(department) {
    const resourceContainer = document.getElementById("resourceContainer");
    resourceContainer.innerHTML = `<p>Loading resources for ${department}...</p>`;

    try {
        const response = await fetch(`index.php?department=${department}`);
        const data = await response.json();

        resourceContainer.innerHTML = "";
        Object.entries(data).forEach(([semester, topics]) => {
            const semesterBlock = document.createElement("div");
            semesterBlock.classList.add("semester-block");
            semesterBlock.innerHTML = `
                <h4>${semester}</h4>
                <ul>${topics.map((topic) => `<li>${topic}</li>`).join("")}</ul>
            `;
            resourceContainer.appendChild(semesterBlock);
        });
    } catch (error) {
        resourceContainer.innerHTML = `<p>Error loading resources: ${error.message}</p>`;
    }
}
