
// Sidebar toggle
const sidebar = document.getElementById("sidebar");
const menuBtn = document.getElementById("menuToggle");

menuBtn.addEventListener("click", () => {
  sidebar.classList.toggle("active");
});

// Mostrar usuario activo
const usuarioActivo = JSON.parse(localStorage.getItem("usuarioActivo"));
if (usuarioActivo) {
  document.getElementById("usuarioActivo").textContent = "👤 " + usuarioActivo.usuario;
} else {
  window.location.href = "#"; // Si no hay sesión, redirigir al login
}

// Logout
document.getElementById("logoutBtn").addEventListener("click", () => {
  localStorage.removeItem("usuarioActivo");
  window.location.href = "#";
});

// Demo de estadísticas solo en dashboard
if (document.getElementById("ventasDia")) {
  document.getElementById("ventasDia").textContent = "$" + (Math.floor(Math.random() * 500000)).toLocaleString();
  document.getElementById("ventasSemana").textContent = "$" + (Math.floor(Math.random() * 2000000)).toLocaleString();
  document.getElementById("ventasMes").textContent = "$" + (Math.floor(Math.random() * 8000000)).toLocaleString();
}
