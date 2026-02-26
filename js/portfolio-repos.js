        async function loadRepos() {
            const endpoint = "https://api.github.com/users/warfdesigns/repos";
            const container = document.getElementById("projects-container");

            try {
                const response = await fetch(endpoint);
                const repos = await response.json();

                if (!Array.isArray(repos)) {
                    container.innerHTML = "<p>Sorry! We can't find the projects! Please try again later. :-)</p>";
                    return;
                }

                container.innerHTML = "";

                repos
                    .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at)) 
                    .forEach(repo => {
                        const card = document.createElement("div");
                        card.className = "repo-card";
                        card.innerHTML = `
                            <h3><a href="${repo.html_url}" target="_blank" rel="noopener noreferrer">${repo.name}</a></h3>
                            <p>${repo.description || "No description provided."}</p>
                            <p class="repo-meta">🍴 ${repo.forks_count}</p>
                        `;
                        container.appendChild(card);
                    });
            } catch (error) {
                console.error("Error loading repositories:", error);
                container.innerHTML = "<p>Error loading projects.</p>";
            }
        }

        loadRepos();