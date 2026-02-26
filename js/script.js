// This function loads a specific page content into the 'content' element "THIS IS NOT USED YET OR FULLY CONFIGURED"// 
function loadPage(page) {
  const content = document.getElementById('content');
  if (!content) return;

  document.querySelectorAll('.HostingLogin').forEach(HostingLogin => {HostingLogin.style.display = 'none';
  });
  
  switch (page.toLowerCase()) {
    case 'index':
      content.innerHTML = '<h1>We Design</h1>';
      document.getElementById('HostingLogin').style.display = 'block';
      break;
    case 'website-hosting':
      content.innerHTML = '<h1>Website Hosting</h1>';
      document.getElementById('HostingLogin').style.display = 'block';
      break;
  }
}

//This function loads a template from a given URL and inserts it into an element with the specified ID
function loadTemplate(url, elementId, onLoad)  {
    const element = document.getElementById(elementId);
  if (!element) return;
  fetch(url)
    .then(response => {
      if (!response.ok) throw new Error(`Failed to load ${url}`);
      return response.text();
    })
    .then(data => {
            element.innerHTML = data;
      if (elementId === 'nav') {
        setActiveNavLink();
        initHomeNavTheme();
      }
      if (elementId === 'appMenu') {
        initMobileMenu();
        }
      if (typeof onLoad === 'function') {
        onLoad(element);
      }
    })
    .catch(error => console.error('Error loading template:', error));
}

function setActiveNavLink() {
  const nav = document.getElementById('nav');
  if (!nav) return;

  const currentPath = window.location.pathname.replace(/\/+$/, '').toLowerCase() || '/';
  const navLinks = nav.querySelectorAll('a[href]');

  navLinks.forEach((link) => {
    link.classList.remove('active');

    const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '').toLowerCase() || '/';
    const isCurrentPage = linkPath === currentPath;
    const isIndexMatch = linkPath.endsWith('/index.html') && currentPath === linkPath.replace('/index.html', '');

    if (isCurrentPage || isIndexMatch) {
      link.classList.add('active');
    }
  });
}

function initHomeNavTheme() {
  const isHomePage = window.location.pathname === '/' || window.location.pathname.endsWith('/index.html');

  if (!isHomePage || !document.querySelector('.hero-bg')) return;

  const nav = document.getElementById('nav');
  const welcomeHeading = Array.from(document.querySelectorAll('h1')).find((heading) =>
    heading.textContent.trim() === 'Welcome to Warf Designs'
  );

  if (!nav || !welcomeHeading) return;

  const setNavTheme = () => {
    const shouldUseLightNav = welcomeHeading.getBoundingClientRect().top > nav.offsetHeight;
    nav.classList.toggle('home-nav-light', shouldUseLightNav);
  };

  setNavTheme();
  window.addEventListener('scroll', setNavTheme, { passive: true });
  window.addEventListener('resize', setNavTheme);
}

function initBackToTopButton() {
  if (document.getElementById('backToTopButton')) return;

  const button = document.createElement('button');
  button.id = 'backToTopButton';
  button.className = 'back-to-top-button';
  button.type = 'button';
  button.setAttribute('aria-label', 'Back to top');
  button.textContent = '↑ Top';
  document.body.appendChild(button);

  const updateVisibility = () => {
    const isVisible = window.scrollY > 250;
    button.classList.toggle('is-visible', isVisible);
  };

  button.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  updateVisibility();
  window.addEventListener('scroll', updateVisibility, { passive: true });
}

function ensureTemplateMount(elementId, tagName = 'div', insertBeforeId = 'footer') {
  if (document.getElementById(elementId)) return;

  const mount = document.createElement(tagName);
  mount.id = elementId;

  const insertBeforeElement = document.getElementById(insertBeforeId);
  if (insertBeforeElement && insertBeforeElement.parentNode) {
    insertBeforeElement.parentNode.insertBefore(mount, insertBeforeElement);
    return;
  }

  document.body.appendChild(mount);
}

function initReviewSlider(root = document) {
  const slider = root.querySelector('[data-review-slider]') || root.querySelector('.review-slider');
  if (!slider || slider.dataset.sliderInitialized === 'true') return;

  const track = slider.querySelector('[data-review-track]') || slider.querySelector('.review-slider__track');
  const cards = slider.querySelectorAll('[data-review-card], .review-slider__card');
  const prevButton = slider.querySelector('[data-review-prev], .review-slider__prev, #reviewPrev');
  const nextButton = slider.querySelector('[data-review-next], .review-slider__next, #reviewNext');

  if (!track || cards.length === 0 || !prevButton || !nextButton) {
    console.warn('Review slider controls were not found. Verify template markup and cache.');
    return;
  }

  let currentSlide = 0;
  slider.dataset.sliderInitialized = 'true';

  const updateSlide = () => {
    track.style.transform = `translateX(-${currentSlide * 100}%)`;
  };

  prevButton.addEventListener('click', () => {
    currentSlide = (currentSlide - 1 + cards.length) % cards.length;
    updateSlide();
  });

  nextButton.addEventListener('click', () => {
    currentSlide = (currentSlide + 1) % cards.length;
    updateSlide();
  });

  updateSlide();
}


// Load templates when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", () => {
  initBackToTopButton();
  loadTemplate('/templates/menu.html', 'nav');
  loadTemplate('/templates/footer.html', 'footer');
  loadTemplate('/templates/review-slider.html', 'reviewSlider', initReviewSlider);
  window.addEventListener('load', () => initReviewSlider(), { once: true });
  loadTemplate('/templates/app-menu.html', 'appMenu');
  loadTemplate('/templates/error.html', 'error');
  loadTemplate('/templates/product-display.html', 'productDisplay');
  loadTemplate('/templates/appointment-form.html', 'appointmentForm');
  loadTemplate('/templates/contact.html', 'contact');
  loadTemplate('/templates/header.html', 'header');
  loadTemplate('/templates/services.html', 'services');
  loadTemplate('/templates/google-analytics.html', 'analytics');
});

//This function loads more videos when the button is clicked
document.addEventListener("DOMContentLoaded", () => {
  const videos = "Q-8yeKt1ULU,fKqzXpnBDZc,aTNzEcOokZc,D0t36FouM7M,GzP5RCDAgrI,z7X6Gq2ZC1k,A1Ut3-ANOvY,P4ABCX63pFQ,UtZMN91uZqY,AClbAEqzcJw,X4XVB5LJnVY,D0t36FouM7M&t,P2tMpsPRCqs&t,xeAMKHm-5J8,zZK9xNDca84,PCUwOeRHWlk&t ".split(",");
  let loaded = 4; // Already 4 shown in HTML
  const step = 4;
  const container = document.querySelector(".videos");
  const btn = document.getElementById("load-more-btn");

  if (!container || !btn) {
    console.warn("Container or button not found.");
    return;
  }

  function loadVideos() {
    const next = videos.slice(loaded, loaded + step);
    next.forEach(id => {
      const iframe = document.createElement("iframe");
      iframe.src = `https://www.youtube.com/embed/${id}?mute=1&loop=1&playlist=${id}`;
      iframe.width = "100%";
      iframe.height = "100%";
      iframe.loading = "lazy";
      iframe.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share");
      iframe.setAttribute("allowfullscreen", "");
      iframe.setAttribute("frameborder", "0");
      iframe.style.marginBottom = "30px";
      container.appendChild(iframe);
    });

    loaded += step;
    if (loaded >= videos.length) {
      btn.style.display = "none";
    }
  }

  btn.addEventListener("click", loadVideos);
});

//This Loads Github Projects to the portfolio page.//

async function loadRepos() {
  const endpoint = "https://api.github.com/users/warfdesigns/repos";
  const container = document.getElementById("projects-container");
  if (!container) return;
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

if ('requestIdleCallback' in window) {
  requestIdleCallback(() => loadRepos());
} else {
  window.addEventListener('load', loadRepos, { once: true });
}

//This function allows users to submit a domain search request. 
function checkDomain() {
      const domain = document.getElementById("domainSearch").value.trim();
      const resultBox = document.getElementById("result");

      if (!domain) {
        resultBox.textContent = "Please enter a domain name.";
        return;
      }

      fetch(`https://domainr.p.rapidapi.com/v2/status?domain=${domain}`, {
        method: "GET",
        headers: {
          "X-RapidAPI-Key": "6b14e08bc5msh581833082c4bdcbp17bf58jsnc332e1eceb3f",
          "X-RapidAPI-Host": "domainr.p.rapidapi.com"
        }
      })
      .then(res => res.json())
      .then(data => {
        const status = data.status && data.status[0].status;
        if (status.includes("inactive") || status.includes("undelegated")) {
          resultBox.textContent = `😎 ${domain} is available!`;
          resultBox.style.color = "green";
        } else {
          resultBox.textContent = `😭 ${domain} is already taken.`;
          resultBox.style.color = "red";
        }
      })
      .catch(err => {
        console.error(err);
        resultBox.textContent = "Error checking domain. Try again.";
        resultBox.style.color = "orange";
      });
    }

function initMobileMenu() {
  const btn = document.getElementById('mobileMenuBtn');
  const sheet = document.getElementById('mobileMenuSheet');
  const backdrop = document.getElementById('mobileMenuBackdrop');
  const closeBtn = document.getElementById('mobileMenuClose');

  if (!btn || !sheet || !backdrop || !closeBtn) return;
  if (btn.dataset.menuInitialized === 'true') return;

  btn.dataset.menuInitialized = 'true';
  function openMenu() {
    sheet.hidden = false;
    backdrop.hidden = false;

    requestAnimationFrame(() => {
      sheet.classList.add('is-open');
    });

    btn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    sheet.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';

    setTimeout(() => {
      sheet.hidden = true;
      backdrop.hidden = true;
    }, 260);
  }

  btn.addEventListener('click', () => {
    const isOpen = sheet.classList.contains('is-open');
    isOpen ? closeMenu() : openMenu();
  });

  closeBtn.addEventListener('click', closeMenu);
  backdrop.addEventListener('click', closeMenu);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sheet.classList.contains('is-open')) closeMenu();
  });

  sheet.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (a) closeMenu();
  });
}
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("[data-before-after]").forEach((container) => {
    const slider = container.querySelector(".before-after__slider");
    const updateSlider = () => {
        container.style.setProperty("--position", `${slider.value}%`);
        };
          slider.addEventListener("input", updateSlider);
            updateSlider();
          });
});