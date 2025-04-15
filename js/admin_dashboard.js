document.addEventListener("DOMContentLoaded", () => {
  const editButtons = document.querySelectorAll(".edit-user");
  const idField = document.getElementById("edit-id");
  const usernameField = document.getElementById("edit-username");
  const emailField = document.getElementById("edit-email");
  const dobField = document.getElementById("edit-dob");
  const isAdminCheckbox = document.getElementById("edit_is_admin");

  editButtons.forEach((button) => {
    button.addEventListener("click", () => {
      idField.value = button.dataset.id;
      usernameField.value = button.dataset.username;
      emailField.value = button.dataset.email;
      dobField.value = button.dataset.dob;
      isAdminCheckbox.checked = button.dataset.is_admin === "1";
    });
  });
});
