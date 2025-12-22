
// Sidebar toggle + overlay
const sidebar = document.getElementById("sidebar");
const menuBtn = document.getElementById("menuToggle");
const overlay = document.createElement("div");
overlay.classList.add("overlay");
document.body.appendChild(overlay);

if (menuBtn) {
  menuBtn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
  });
}

overlay.addEventListener("click", () => {
  sidebar.classList.remove("active");
  overlay.classList.remove("active");
});

// Mostrar usuario activo
const usuarioActivo = JSON.parse(localStorage.getItem("usuarioActivo"));
if (usuarioActivo && document.getElementById("usuarioActivo")) {
  document.getElementById("usuarioActivo").textContent = "👤 " + usuarioActivo.usuario;
} else if (!usuarioActivo) {
  // Redirige al login si no hay sesión
  window.location.href = "#";
}

// Logout
const logoutBtn = document.getElementById("logoutBtn");
if (logoutBtn) {
  logoutBtn.addEventListener("click", () => {
    localStorage.removeItem("usuarioActivo");
    window.location.href = "#";
  });
}
