document.addEventListener("DOMContentLoaded", function () {
  // Fermeture automatique des alertes après 4 secondes
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = "opacity 0.5s";
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 500);
    }, 4000);
  });

  // Toggle du menu hamburger
  const burgerButton = document.querySelector(".burger_menu_button");
  const burgerIcon = document.querySelector(".burger_menu_button i");
  const burgerMenu = document.querySelector(".burger_menu");

  if (burgerButton && burgerMenu) {
    burgerButton.setAttribute("aria-expanded", "false");
    burgerMenu.style.maxHeight = "0px";

    burgerButton.addEventListener("click", function () {
      const isOpen = burgerMenu.classList.toggle("open");

      // Change l'icône
      burgerIcon.classList.remove("fa-bars", "fa-xmark");
      burgerIcon.classList.add(isOpen ? "fa-xmark" : "fa-bars");

      burgerButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
      burgerMenu.style.maxHeight = isOpen
        ? `${burgerMenu.scrollHeight}px`
        : "0px";
    });

    // Ferme le menu après clic sur un lien
    burgerMenu.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", function () {
        burgerMenu.classList.remove("open");
        burgerIcon.classList.remove("fa-xmark");
        burgerIcon.classList.add("fa-bars");
        burgerButton.setAttribute("aria-expanded", "false");
        burgerMenu.style.maxHeight = "0px";
      });
    });

    // Ferme le menu en passant en desktop
    window.addEventListener("resize", function () {
      if (window.innerWidth > 990) {
        burgerMenu.classList.remove("open");
        burgerIcon.classList.remove("fa-xmark");
        burgerIcon.classList.add("fa-bars");
        burgerButton.setAttribute("aria-expanded", "false");
        burgerMenu.style.maxHeight = "0px";
      }
    });
  }
});
