document.addEventListener("DOMContentLoaded", () => {
  const showCreateFormBtn = document.getElementById("show-create-form");
  const createFormContainer = document.getElementById("create-form-container");
  const editFormContainer = document.getElementById("edit-form-container");

  const createForm = createFormContainer.querySelector("form");
  const editForm = editFormContainer.querySelector("form");

  setupCreateForm(
    showCreateFormBtn,
    createForm,
    createFormContainer,
    editFormContainer
  );
  setupEditButtons(editFormContainer, createFormContainer);
  setupEditForm(editForm);
  setupDeleteButtons();
});

function setupCreateForm(button, form, createContainer, editContainer) {
  button.addEventListener("click", () => {
    form.reset();
    editContainer.classList.add("d-none");
    createContainer.classList.remove("d-none");
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.isAdmin = formData.get("edit_is_admin") === "on";

    try {
      const res = await fetch("actions/register.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await res.json();
      handleResponse(res, result, "User created successfully");
    } catch (err) {
      console.error(err);
      alert("Something went wrong!");
    }
  });
}

function setupEditButtons(editContainer, createContainer) {
  document.querySelectorAll(".edit-user").forEach((btn) => {
    btn.addEventListener("click", () => {
      createContainer.classList.add("d-none");
      editContainer.classList.remove("d-none");

      document.getElementById("edit-id").value = btn.dataset.id;
      document.getElementById("edit-username").value = btn.dataset.username;
      document.getElementById("edit-email").value = btn.dataset.email;
      document.getElementById("edit-dob").value = btn.dataset.dob;
      document.getElementById("edit_is_admin").checked =
        btn.dataset.is_admin === "1";
    });
  });
}

function setupEditForm(form) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.isAdmin = formData.get("edit_is_admin") === "on";

    try {
      const res = await fetch("actions/update_profile.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await res.json();
      handleResponse(res, result, "User updated successfully");
    } catch (err) {
      console.error(err);
      alert("Something went wrong!");
    }
  });
}

function setupDeleteButtons() {
  document.querySelectorAll(".btn-delete-user").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      e.preventDefault();
      const userId = btn.dataset.id;
      const confirmed = confirm("Are you sure you want to delete this user?");
      if (!confirmed) return;

      try {
        const res = await fetch("actions/delete_user.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: userId }),
        });

        const result = await res.json();
        handleResponse(res, result, "User deleted successfully");
      } catch (err) {
        console.error(err);
        alert("Something went wrong while deleting the user.");
      }
    });
  });
}

function handleResponse(res, result, successMessage) {
  if (res.ok && result.success !== false) {
    alert(result.message || successMessage);
    location.reload();
  } else {
    alert(result.message || result.error || "Operation failed");
  }
}
