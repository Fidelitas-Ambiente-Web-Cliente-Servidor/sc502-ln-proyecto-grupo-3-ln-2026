window.Toast = (() => {
  let queue = [];
  let container;

  const init = () => {
    container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);
  };

  const show = (message, type = "success", duration = 3000) => {
    if (!container) init();

    const toast = document.createElement("div");
    toast.className = `toast-item ${type}`;

    toast.innerHTML = `
      <div class="toast-content">
        <span class="toast-message">${message}</span>
      </div>
    `;

    container.appendChild(toast);

    // entrada (animación)
    requestAnimationFrame(() => {
      toast.classList.add("show");
    });

    // salida
    setTimeout(() => {
      toast.classList.remove("show");

      setTimeout(() => {
        toast.remove();
      }, 300);
    }, duration);
  };

  return { show };
})();
