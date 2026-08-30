/*!
 * INSG GABON — app.js
 * Script global : navbar, animations, compteurs, filtres, formulaires, portails.
 * Vanilla JS — aucune dépendance hors Bootstrap 5 (bundle JS pour les composants natifs).
 */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    initNavbarScroll();
    initActiveNavigation();
    initMobileNavClose();
    initRevealOnScroll();
    initCounters();
    initBackToTop();
    initFormationFilter();
    initNewsFilter();
    initContactForm();
    initPreRegistrationForm();
    initMasterApplicationForm();
    initFileUploadLabel();
    initPortalSidebar();
    initGenericFormValidation();
    initBackofficeDownloads();
    initBackofficeSearch();
    initBackofficeFilters();
    initBackofficeQuickActions();
    initChatbot();
  });

  function initActiveNavigation() {
    document.querySelectorAll(".navbar-insg .nav-link.active:not(.dropdown-toggle), .navbar-insg .dropdown-item.active").forEach(function (link) {
      link.setAttribute("aria-current", "page");
    });
  }

  /* 1. Navbar : fond plein au scroll ---------------------------------- */
  function initNavbarScroll() {
    var navbar = document.querySelector(".navbar-insg");
    if (!navbar) return;

    function toggleScrolled() {
      if (window.scrollY > 40) {
        navbar.classList.add("is-scrolled");
      } else if (!navbar.classList.contains("navbar-solid")) {
        navbar.classList.remove("is-scrolled");
      }
    }
    toggleScrolled();
    window.addEventListener("scroll", toggleScrolled, { passive: true });
  }

  /* 2. Ferme le menu mobile après un clic sur un lien ------------------ */
  function initMobileNavClose() {
    var collapseEl = document.getElementById("mainNavbar");
    if (!collapseEl || typeof bootstrap === "undefined") return;
    var links = collapseEl.querySelectorAll(".nav-link, .btn-portal");
    links.forEach(function (link) {
      link.addEventListener("click", function () {
        if (window.innerWidth < 992) {
          var instance = bootstrap.Collapse.getOrCreateInstance(collapseEl);
          instance.hide();
        }
      });
    });
  }

  /* 3. Animation "reveal" au défilement (IntersectionObserver) -------- */
  function initRevealOnScroll() {
    var items = document.querySelectorAll(".reveal");
    if (!items.length) return;

    if (!("IntersectionObserver" in window)) {
      items.forEach(function (el) { el.classList.add("is-visible"); });
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    items.forEach(function (el) { observer.observe(el); });
  }

  /* 4. Compteurs animés (chiffres clés) -------------------------------- */
  function initCounters() {
    var counters = document.querySelectorAll("[data-counter]");
    if (!counters.length) return;

    function animateCounter(el) {
      var target = parseInt(el.getAttribute("data-counter"), 10) || 0;
      var duration = 1500;
      var start = null;

      function step(timestamp) {
        if (!start) start = timestamp;
        var progress = Math.min((timestamp - start) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
        el.textContent = Math.floor(eased * target).toLocaleString("fr-FR");
        if (progress < 1) {
          requestAnimationFrame(step);
        } else {
          el.textContent = target.toLocaleString("fr-FR");
        }
      }
      requestAnimationFrame(step);
    }

    if (!("IntersectionObserver" in window)) {
      counters.forEach(animateCounter);
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.5 }
    );
    counters.forEach(function (el) { observer.observe(el); });
  }

  /* 5. Bouton "retour en haut" ------------------------------------------ */
  function initBackToTop() {
    var btn = document.querySelector(".back-to-top");
    if (!btn) return;
    window.addEventListener(
      "scroll",
      function () {
        btn.classList.toggle("is-visible", window.scrollY > 500);
      },
      { passive: true }
    );
    btn.addEventListener("click", function () {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  /* 6. Filtre des formations (page formations.html) --------------------- */
  function initFormationFilter() {
    var filterBar = document.querySelector("[data-filter-bar]");
    if (!filterBar) return;
    var buttons = filterBar.querySelectorAll(".filter-btn");
    var cards = document.querySelectorAll("[data-filter-item]");

    buttons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        buttons.forEach(function (b) { b.classList.remove("active"); });
        btn.classList.add("active");
        var category = btn.getAttribute("data-filter");

        cards.forEach(function (card) {
          var match = category === "all" || card.getAttribute("data-filter-item") === category;
          card.style.display = match ? "" : "none";
        });
      });
    });
  }

  /* 7. Recherche + filtre catégories (page actualites.html) ------------- */
  function initNewsFilter() {
    var searchInput = document.querySelector("[data-news-search]");
    var catButtons = document.querySelectorAll("[data-news-cat]");
    var items = document.querySelectorAll("[data-news-item]");
    if (!items.length) return;

    var activeCategory = "all";

    function applyFilters() {
      var query = (searchInput ? searchInput.value : "").trim().toLowerCase();
      var visibleCount = 0;

      items.forEach(function (item) {
        var title = (item.getAttribute("data-title") || "").toLowerCase();
        var category = item.getAttribute("data-news-item");
        var matchesCategory = activeCategory === "all" || category === activeCategory;
        var matchesQuery = query === "" || title.indexOf(query) !== -1;
        var visible = matchesCategory && matchesQuery;
        item.style.display = visible ? "" : "none";
        if (visible) visibleCount++;
      });

      var emptyState = document.querySelector("[data-news-empty]");
      if (emptyState) emptyState.classList.toggle("d-none", visibleCount !== 0);
    }

    if (searchInput) searchInput.addEventListener("input", applyFilters);
    catButtons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        catButtons.forEach(function (b) { b.classList.remove("active"); });
        btn.classList.add("active");
        activeCategory = btn.getAttribute("data-news-cat");
        applyFilters();
      });
    });
  }

  /* 8. Validation formulaire de contact ---------------------------------- */
  function initContactForm() {
    var form = document.getElementById("contactForm");
    if (!form) return;
    form.addEventListener("submit", function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        form.classList.add("was-validated");
        return;
      }
      // Le formulaire valide est envoyé au contrôleur Laravel.
    });
  }

  /* 9. Validation formulaire de pré-inscription --------------------------- */
  function initPreRegistrationForm() {
    var form = document.getElementById("preRegistrationForm");
    if (!form) return;
    form.addEventListener("submit", function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        form.classList.add("was-validated");
        var firstInvalid = form.querySelector(":invalid");
        if (firstInvalid) firstInvalid.focus();
        return;
      }
      // Le formulaire valide est envoyé au contrôleur Laravel.
    });
  }

  function showFormSuccess(form, successElId) {
    var successEl = document.getElementById(successElId);
    form.reset();
    form.classList.remove("was-validated");
    if (successEl) {
      successEl.classList.remove("d-none");
      successEl.scrollIntoView({ behavior: "smooth", block: "center" });
      setTimeout(function () { successEl.classList.add("d-none"); }, 6000);
    }
  }

  /* 10. Validation du dossier de candidature en Master ------------------ */
  function initMasterApplicationForm() {
    var form = document.getElementById("masterApplicationForm");
    if (!form) return;
    form.addEventListener("submit", function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        form.classList.add("was-validated");
        var firstInvalid = form.querySelector(":invalid");
        if (firstInvalid) firstInvalid.focus();
        return;
      }
      // Le formulaire valide est envoyé au contrôleur Laravel.
    });
  }

  /* 11. Affiche le nom du fichier sélectionné (input file stylisé) -------- */
  function initFileUploadLabel() {
    document.querySelectorAll("[data-file-input]").forEach(function (input) {
      var labelTarget = document.querySelector('[data-file-label-for="' + input.id + '"]');
      if (!labelTarget) return;
      var defaultText = labelTarget.textContent;
      input.addEventListener("change", function () {
        if (input.files && input.files.length > 0) {
          var names = Array.prototype.map.call(input.files, function (f) { return f.name; }).join(", ");
          labelTarget.textContent = names;
        } else {
          labelTarget.textContent = defaultText;
        }
      });
    });
  }

  /* 12. Sidebar portails (ouverture/fermeture mobile) ---------------------- */
  function initPortalSidebar() {
    var toggleBtn = document.querySelector("[data-sidebar-toggle]");
    var sidebar = document.querySelector(".portal-sidebar");
    var overlay = document.querySelector("[data-sidebar-overlay]");
    if (!toggleBtn || !sidebar) return;

    function closeSidebar() {
      sidebar.classList.remove("is-open");
      if (overlay) overlay.classList.add("d-none");
    }
    function openSidebar() {
      sidebar.classList.add("is-open");
      if (overlay) overlay.classList.remove("d-none");
    }

    toggleBtn.addEventListener("click", function () {
      sidebar.classList.contains("is-open") ? closeSidebar() : openSidebar();
    });
    if (overlay) overlay.addEventListener("click", closeSidebar);
  }

  /* 13. Validation générique Bootstrap (autres formulaires du site) -------- */
  function initGenericFormValidation() {
    var forms = document.querySelectorAll(".needs-validation:not(#contactForm):not(#preRegistrationForm):not(#masterApplicationForm)");
    forms.forEach(function (form) {
      form.addEventListener("submit", function (e) {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add("was-validated");
      });
    });
  }

  function initBackofficeDownloads() {
    document.querySelectorAll('[aria-label="Télécharger"]').forEach(function (button) {
      button.addEventListener("click", function () {
        window.location.href = window.location.pathname.indexOf("admin-dashboard") !== -1
          ? "/backoffice/admin/exports/paiements"
          : "/backoffice/documents/releve-notes";
      });
    });
  }

  function initBackofficeSearch() {
    document.querySelectorAll('.portal-topbar input[type="search"], .portal-content input[type="search"]').forEach(function (input) {
      input.addEventListener("input", function () {
        var query = input.value.trim().toLowerCase();
        var scope = input.closest(".tab-pane") || document.querySelector(".tab-pane.active") || document;
        scope.querySelectorAll("tbody tr, .card-insg").forEach(function (item) {
          item.style.display = !query || item.textContent.toLowerCase().indexOf(query) !== -1 ? "" : "none";
        });
      });
    });
  }

  function initBackofficeQuickActions() {
    function showNotice(message) {
      var notice = document.createElement("div");
      notice.className = "alert alert-primary position-fixed shadow";
      notice.style.cssText = "right:1.25rem;top:5rem;z-index:2000;max-width:320px";
      notice.setAttribute("role", "status");
      notice.textContent = message;
      document.body.appendChild(notice);
      setTimeout(function () { notice.remove(); }, 3000);
    }

    function openTabOrNotify(source, selector, message) {
      if (source && source.dataset.portalUrl) {
        window.location.href = source.dataset.portalUrl;
        return;
      }
      var trigger = document.querySelector(selector);
      if (trigger && typeof bootstrap !== "undefined") {
        bootstrap.Tab.getOrCreateInstance(trigger).show();
      } else {
        showNotice(message);
      }
    }

    document.querySelectorAll('.portal-topbar button[aria-label="Notifications"]').forEach(function (button) {
      button.addEventListener("click", function () {
        openTabOrNotify(button, '[href="#tab-notifications"]', "Aucune nouvelle notification.");
      });
    });
    document.querySelectorAll('.portal-topbar button[aria-label="Messages"]').forEach(function (button) {
      button.addEventListener("click", function () {
        openTabOrNotify(button, '[href="#tab-messages"]', "Aucun nouveau message.");
      });
    });
  }

  function initBackofficeFilters() {
    document.querySelectorAll("[data-program-filter]").forEach(function (select) {
      select.addEventListener("change", function () {
        var panel = select.closest(".panel-insg");
        if (!panel) return;
        panel.querySelectorAll("tbody tr[data-program-id]").forEach(function (row) {
          row.style.display = !select.value || row.dataset.programId === select.value ? "" : "none";
        });
      });
    });
  }

  function initChatbot() {
    if (document.body.classList.contains("portal-body") || window.location.pathname.indexOf("/connexion") === 0) return;

    var root = document.createElement("section");
    root.className = "insg-chatbot";
    root.setAttribute("aria-label", "Assistant virtuel INSG");
    root.innerHTML = [
      '<div class="insg-chatbot-invite"><button type="button" data-chat-invite-close aria-label="Masquer">×</button><strong>Bonjour 👋</strong><span>Une question sur l’INSG ?</span></div>',
      '<button class="insg-chatbot-toggle" type="button" aria-expanded="false" aria-label="Ouvrir l\'assistant"><span class="chatbot-toggle-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11a8 8 0 0 1-8 8H6l-4 3V11a8 8 0 1 1 18 0Z"/><path d="M8 11h.01M12 11h.01M16 11h.01"/></svg><span class="chatbot-online-dot"></span></span><span>Besoin d’aide ?</span></button>',
      '<div class="insg-chatbot-panel" hidden>',
      '  <header><div class="chatbot-identity"><span class="chatbot-avatar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 9 9-5 9 5-9 5-9-5Z"/><path d="M7 12v4c3 2 7 2 10 0v-4M21 9v6"/></svg></span><div><strong>Assistant INSG</strong><small><span></span> Disponible maintenant</small></div></div><button type="button" data-chat-close aria-label="Fermer">×</button></header>',
      '  <div class="insg-chatbot-messages" aria-live="polite"><div class="chatbot-day-label">Aujourd’hui</div><div class="chat-message bot">Bonjour ! Je suis l’assistant virtuel de l’INSG. Je peux vous guider sur les formations, les admissions, les concours et nos services.</div></div>',
      '  <div class="insg-chatbot-suggestions"><button type="button">Quelles formations proposez-vous ?</button><button type="button">Comment s’inscrire ?</button></div>',
      '  <form class="insg-chatbot-form"><label class="visually-hidden" for="insg-chatbot-input">Votre question</label><input id="insg-chatbot-input" maxlength="500" autocomplete="off" placeholder="Écrivez votre question…" required><button type="submit" aria-label="Envoyer"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 9-18 9 3-9-3-9Z"/><path d="M6 12h15"/></svg></button></form>',
      '</div>'
    ].join("");
    document.body.appendChild(root);

    var toggle = root.querySelector(".insg-chatbot-toggle");
    var panel = root.querySelector(".insg-chatbot-panel");
    var close = root.querySelector("[data-chat-close]");
    var form = root.querySelector(".insg-chatbot-form");
    var input = root.querySelector("input");
    var messages = root.querySelector(".insg-chatbot-messages");
    var suggestions = root.querySelector(".insg-chatbot-suggestions");
    var invite = root.querySelector(".insg-chatbot-invite");

    function setOpen(open) {
      panel.hidden = !open;
      toggle.setAttribute("aria-expanded", String(open));
      root.classList.toggle("is-open", open);
      if (open) {
        invite.hidden = true;
        input.focus();
      }
    }

    function addMessage(text, type, links) {
      var message = document.createElement("div");
      message.className = "chat-message " + type;
      message.textContent = text;
      if (links && links.length) {
        var linkBox = document.createElement("div");
        linkBox.className = "chat-links";
        links.forEach(function (item) {
          var link = document.createElement("a");
          link.href = item.url;
          link.textContent = item.label;
          linkBox.appendChild(link);
        });
        message.appendChild(linkBox);
      }
      messages.appendChild(message);
      messages.scrollTop = messages.scrollHeight;
      return message;
    }

    async function ask(question) {
      addMessage(question, "user");
      input.value = "";
      var pending = addMessage("Je recherche l’information…", "bot loading");
      try {
        var response = await fetch("/assistant/poser-une-question?message=" + encodeURIComponent(question), { headers: { Accept: "application/json" } });
        if (!response.ok) throw new Error("chatbot");
        var data = await response.json();
        pending.remove();
        addMessage(data.answer, "bot", data.links);
      } catch (error) {
        pending.textContent = "Je ne peux pas répondre pour le moment. Vous pouvez utiliser la page Contact pour joindre l’INSG.";
        pending.classList.remove("loading");
      }
    }

    toggle.addEventListener("click", function () { setOpen(panel.hidden); });
    close.addEventListener("click", function () { setOpen(false); });
    invite.addEventListener("click", function (event) {
      if (event.target.closest("[data-chat-invite-close]")) invite.hidden = true;
      else setOpen(true);
    });
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      var question = input.value.trim();
      if (question) ask(question);
    });
    suggestions.addEventListener("click", function (event) {
      if (event.target.tagName === "BUTTON") ask(event.target.textContent);
    });
    if (window.location.hash === "#assistant") setOpen(true);
  }
})();
