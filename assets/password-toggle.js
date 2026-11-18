document.addEventListener("DOMContentLoaded", function () {
  document
    .querySelectorAll('input[type="password"]')
    .forEach(function (input, index) {
      if (input.dataset.visibilityReady === "1") return;
      input.dataset.visibilityReady = "1";
      const wrapper = document.createElement("div");
      wrapper.className = "password-visibility-wrap";
      input.parentNode.insertBefore(wrapper, input);
      wrapper.appendChild(input);
      const toggle = document.createElement("button");
      toggle.type = "button";
      toggle.className = "password-visibility-toggle";
      toggle.textContent = "Show";
      toggle.setAttribute("aria-label", "Show password");
      toggle.addEventListener("click", function () {
        const show = input.type === "password";
        input.type = show ? "text" : "password";
        toggle.textContent = show ? "Hide" : "Show";
        toggle.setAttribute(
          "aria-label",
          show ? "Hide password" : "Show password",
        );
        input.focus();
      });
      wrapper.appendChild(toggle);
    });
});
