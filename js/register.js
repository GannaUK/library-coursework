document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  form.addEventListener("submit", function (event) {
    event.preventDefault(); // Предотвращаем стандартную отправку формы

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const email = document.getElementById("email").value;
    const dob = document.getElementById("dob").value;
    const isAdmin = document.getElementById("isAdmin").checked;

    fetch("actions/register.php", {
      method: "POST",
      body: JSON.stringify({
        username: username,
        password: password,
        email: email,
        dob: dob,
        isAdmin: isAdmin,
      }),
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        window.location.href = `login.php?username=${encodeURIComponent(
          username
        )}`;
      })
      .catch((error) => {
        alert("Registration failed: " + error.message);
      });
  });
});
