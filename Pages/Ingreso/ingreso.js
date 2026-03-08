const loginLink = document.getElementById("login-box-link");
const signupLink = document.getElementById("signup-box-link");

const loginForm = document.querySelector(".email-login");
const signupForm = document.querySelector(".email-signup");

signupForm.style.display = "none";

signupLink.addEventListener("click", function (e) {
  e.preventDefault();

  loginForm.style.display = "none";
  signupForm.style.display = "flex";

  loginLink.classList.remove("active");
  signupLink.classList.add("active");
});

loginLink.addEventListener("click", function (e) {
  e.preventDefault();

  signupForm.style.display = "none";
  loginForm.style.display = "flex";

  signupLink.classList.remove("active");
  loginLink.classList.add("active");
});
