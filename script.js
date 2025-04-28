// ================= BARRE DE PROGRESSION DU SCROLL =================
window.addEventListener('scroll', () => {
    const scrollProgress = document.getElementById('scroll-progress');
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = (scrollTop / docHeight) * 100;
    scrollProgress.style.width = scrollPercent + "%";
});

// ================= PAGINATION DES ARTICLES =================
const articlesPerPage = 4;
const articleContainer = document.getElementById("articleContainer");
const pagination = document.getElementById("pagination");
const articles = Array.from(articleContainer.children);
let currentArticlePage = 1;
const totalArticlePages = Math.ceil(articles.length / articlesPerPage);

function showArticlePage(page) {
    currentArticlePage = page;
    const start = (page - 1) * articlesPerPage;
    const end = start + articlesPerPage;

    articles.forEach((article, index) => {
        article.style.display = (index >= start && index < end) ? "block" : "none";
    });

    updateArticlePagination();
}

function updateArticlePagination() {
    pagination.innerHTML = "";

    // Bouton Précédent
    if (currentArticlePage > 1) {
        const prevBtn = document.createElement("button");
        prevBtn.innerHTML = "&laquo;";
        prevBtn.addEventListener("click", () => showArticlePage(currentArticlePage - 1));
        prevBtn.className = "pagination-btn";
        pagination.appendChild(prevBtn);
    }

    // Boutons numérotés
    for (let i = 1; i <= totalArticlePages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = "pagination-btn";
        if (i === currentArticlePage) btn.classList.add("active");
        btn.addEventListener("click", () => showArticlePage(i));
        pagination.appendChild(btn);
    }

    // Bouton Suivant
    if (currentArticlePage < totalArticlePages) {
        const nextBtn = document.createElement("button");
        nextBtn.innerHTML = "&raquo;";
        nextBtn.addEventListener("click", () => showArticlePage(currentArticlePage + 1));
        nextBtn.className = "pagination-btn";
        pagination.appendChild(nextBtn);
    }
}

// ================= PAGINATION DES PROJETS =================
const projetsPerPage = 2;
const projetContainer = document.getElementById("projetContainer");
const paginationProjets = document.getElementById("paginationProjets");
const projets = Array.from(projetContainer.children);
let currentProjetPage = 1;
const totalProjetPages = Math.ceil(projets.length / projetsPerPage);

function showProjetPage(page) {
    currentProjetPage = page;
    const start = (page - 1) * projetsPerPage;
    const end = start + projetsPerPage;

    projets.forEach((projet, index) => {
        projet.style.display = (index >= start && index < end) ? "block" : "none";
    });

    updateProjetPagination();
}

function updateProjetPagination() {
    paginationProjets.innerHTML = "";

    // Bouton Précédent
    if (currentProjetPage > 1) {
        const prevBtn = document.createElement("button");
        prevBtn.innerHTML = "&laquo;";
        prevBtn.addEventListener("click", () => showProjetPage(currentProjetPage - 1));
        prevBtn.className = "pagination-btn";
        paginationProjets.appendChild(prevBtn);
    }

    // Boutons numérotés
    for (let i = 1; i <= totalProjetPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = "pagination-btn";
        if (i === currentProjetPage) btn.classList.add("active");
        btn.addEventListener("click", () => showProjetPage(i));
        paginationProjets.appendChild(btn);
    }

    // Bouton Suivant
    if (currentProjetPage < totalProjetPages) {
        const nextBtn = document.createElement("button");
        nextBtn.innerHTML = "&raquo;";
        nextBtn.addEventListener("click", () => showProjetPage(currentProjetPage + 1));
        nextBtn.className = "pagination-btn";
        paginationProjets.appendChild(nextBtn);
    }
}

// ================= INITIALISATION =================
document.addEventListener("DOMContentLoaded", function() {
    showArticlePage(1);
    showProjetPage(1);
});

// Supprimer ceci (ce n'est pas utile ici) :
// showPage(1); 
