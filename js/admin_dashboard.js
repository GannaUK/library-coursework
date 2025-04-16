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

  // Кнопка "Добавить книгу"
  document
    .getElementById("show-create-book-form")
    .addEventListener("click", function () {
      showCreateBookForm();
    });

  // Кнопки "Редактировать"
  document.querySelectorAll(".edit-book-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
      const bookData = {
        id: btn.dataset.id,
        title: btn.dataset.title,
        author: btn.dataset.author,
        genre: btn.dataset.genre,
        description: btn.dataset.description,
        days: btn.dataset.days,
      };
      showEditBookForm(bookData);
    });
  });

  document.querySelectorAll(".delete-book-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const id = button.dataset.id;
      if (confirm("Delete this book?")) {
        deleteBook(id);
      }
    });
  });

  document
    .getElementById("book-create-form")
    .addEventListener("submit", handleCreateFormSubmit);

  document
    .getElementById("book-edit-form")
    .addEventListener("submit", handleEditFormSubmit);

  document
    .getElementById("book-filter-form")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      fetchAndRenderFilteredBooks(formData);
    });

  document
    .getElementById("reset-filter")
    .addEventListener("click", function () {
      const form = document.getElementById("book-filter-form");
      form.reset();
      const formData = new FormData(form);
      fetchAndRenderFilteredBooks(formData);
    });
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
// Book Management Functions
function showCreateBookForm() {
  const formContainer = document.getElementById("book-form-container");
  const createForm = document.getElementById("create-book-form");
  const editForm = document.getElementById("edit-book-form");

  formContainer.classList.remove("d-none");
  createForm.classList.remove("d-none");
  editForm.classList.add("d-none");

  document.getElementById("book-create-form").reset();
}

function showEditBookForm(bookData) {
  const formContainer = document.getElementById("book-form-container");
  const createForm = document.getElementById("create-book-form");
  const editForm = document.getElementById("edit-book-form");

  formContainer.classList.remove("d-none");
  createForm.classList.add("d-none");
  editForm.classList.remove("d-none");

  document.getElementById("edit-book-id").value = bookData.id;
  document.getElementById("edit-book-title").value = bookData.title;
  document.getElementById("edit-book-author").value = bookData.author;
  document.getElementById("edit-book-genre").value = bookData.genre;
  document.getElementById("edit-book-description").value = bookData.description;
  document.getElementById("edit-book-days").value = bookData.days;
}

// Отправка формы создания книги
function handleCreateFormSubmit(event) {
  event.preventDefault();

  const form = event.target;
  const data = {
    action: "create",
    title: form.title.value,
    author: form.author.value,
    genre: form.genre.value,
    description: form.description.value,
    max_days: parseInt(form.max_days.value, 10) || 14,
  };

  fetch("actions/books.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((res) => res.json())
    .then((response) => {
      if (response.success) {
        form.reset();
        showSuccess("Book added!");
        // refreshBookTable(); // обновим таблицу
        setupBookFilter();
      } else {
        showError(response.message || "Error adding book.");
      }
    })
    .catch(() => showError("Server error."));
}

// Отправка формы редактирования книги
function handleEditFormSubmit(event) {
  event.preventDefault();

  const form = event.target;
  const data = {
    action: "update",
    id: form.id.value,
    title: form.title.value,
    author: form.author.value,
    genre: form.genre.value,
    description: form.description.value,
    max_days: parseInt(form.max_days.value, 10) || 14,
    quantity: parseInt(form.quantity.value),
  };

  fetch("actions/books.php", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((res) => res.json())
    .then((response) => {
      if (response.success) {
        showSuccess("Book updated!");
        // refreshBookTable(); // обновим таблицу
        setupBookFilter();
        //  fetchAndRenderFilteredBooks(formData);

        // Если есть значение quantity — создаём движение книги
        if (!isNaN(data.quantity) && data.quantity !== 0) {
          const movementData = {
            book_id: data.id,
            quantity: data.quantity,
            // При необходимости можно добавить user_id и movement_date
          };

          fetch("actions/book_movements.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(movementData),
          })
            .then((res) => res.json())
            .then((movementResponse) => {
              if (movementResponse.success) {
                showSuccess("Book movement added.");
                // Прячем форму редактирования/создания, если она открыта
                document
                  .getElementById("book-form-container")
                  .classList.add("d-none");

                // Дополнительно сбрасываем формы
                document
                  .getElementById("book-create-form").reset();
                document                  
                  .getElementById("book-edit-form").reset();
                
                attachBookActionHandlers(); //  Подключаем обработчики

              } else {
                showError(
                  movementResponse.message || "Error saving book movement."
                );
              }
            })
            .catch(() => showError("Server error when adding movement."));
        }
      } else {
        showError(response.message || "Error updating book.");
      }
    })
    .catch(() => showError("Server error."));
}

function deleteBook(bookId) {
  fetch("actions/books.php", {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: bookId }),
  })
    .then((res) => res.json())
    .then((response) => {
      if (response.success) {
        setupBookFilter();
        // refreshBookTable();
      } else {
        showError("Failed to delete book.");
      }
    })
    .catch(() => showError("Error deleting book."));
}

function showSuccess(message) {
  alert(message); // можно заменить на toast
}

function showError(message) {
  alert("Error: " + message);
}

function attachBookActionHandlers() {
  document.querySelectorAll(".edit-book-btn").forEach((button) => {
    button.addEventListener("click", () => {
      showEditBookForm({
        id: button.dataset.id,
        title: button.dataset.title,
        author: button.dataset.author,
        genre: button.dataset.genre,
        description: button.dataset.description,
        days: button.dataset.days,
      });
    });
  });

  document.querySelectorAll(".delete-book-btn").forEach((button) => {
    button.addEventListener("click", () => {
      if (confirm("Delete this book?")) {
        deleteBook(button.dataset.id);
      }
    });
  });
}

// Обновление таблицы книг после изменений
function refreshBookTable() {
  fetch("actions/books.php", {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  })
    .then((res) => res.json())
    .then((response) => {
      if (!response.success) {
        showError("Failed to load books.");
        return;
      }

      const books = response.books;
      const tbody = document.querySelector("#books-table tbody");
      tbody.innerHTML = "";

      books.forEach((book) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                    <td>${book.id}</td>
                    <td>${book.title}</td>
                    <td>${book.author}</td>
                    <td>${book.genre}</td>
                    <td>${book.description}</td>
                    <td>${book.max_days}</td>
                    <td>
                    <div class='btn-group' role='group' aria-label='Basic button group'>
                        <button class='btn btn-sm btn-outline-primary me-1 edit-book-btn'
                            data-id='${book.id}'
                            data-title='${book.title}'
                            data-author='${book.author}'
                            data-genre='${book.genre}'
                            data-description='${book.description}'
                            data-days='${book.max_days}'>Edit</button>
                        <button class='btn btn-sm btn-outline-danger delete-book-btn' data-id='${book.id}'>Delete</button>
                        </div>
                    </td>
                `;
        tbody.appendChild(row);
      });

      // Прячем форму редактирования/создания, если она открыта
      document.getElementById("book-form-container").classList.add("d-none");

      // Дополнительно сбрасываем формы
      document.getElementById("book-create-form").reset();
      document.getElementById("book-edit-form").reset();
      attachBookActionHandlers(); //  Подключаем обработчики
    })
    .catch(() => showError("Error loading books."));
}

function fetchAndRenderFilteredBooks(formData) {
  const title = encodeURIComponent(formData.get("title").trim());
  const author = encodeURIComponent(formData.get("author").trim());
  const genre = encodeURIComponent(formData.get("genre"));

  const url = `actions/filter_books.php?title=${title}&author=${author}&genre=${genre}`;

  fetch(url)
    .then((res) => res.json())
    .then((books) => {
      renderBooksTable(books);
      // Прячем форму редактирования/создания, если она открыта
      document.getElementById("book-form-container").classList.add("d-none");

      // Дополнительно сбрасываем формы
      document.getElementById("book-create-form").reset();
      document.getElementById("book-edit-form").reset();
      
    })
    .catch(() => {
      showError("Filter error");
    });
}

function renderBooksTable(books) {
  const tbody = document.querySelector("#books-table tbody");
  tbody.innerHTML = ""; // очищаем старые строки

  if (books.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center">No books found</td></tr>`;
    return;
  }

  books.forEach((book) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
            <td>${book.id}</td>
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td>${book.genre}</td>
            <td>${book.description}</td>
            <td>${book.max_days}</td>
            <td>
            <div class='btn-group' role='group' aria-label='Basic button group'>
                <button class='btn btn-sm btn-outline-primary me-1 edit-book-btn'
                    data-id='${book.id}'
                    data-title='${book.title}'
                    data-author='${book.author}'
                    data-genre='${book.genre}'
                    data-description='${book.description}'
                    data-days='${book.max_days}'
                >Edit</button>
                <button class='btn btn-sm btn-outline-danger delete-book-btn' data-id='${book.id}'>Delete</button>
                </div>
            </td>
        `;
    tbody.appendChild(tr);
  });

  attachBookActionHandlers(); //  Подключаем обработчики
}

function setupBookFilter() {
  const form = document.getElementById("book-filter-form");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    fetchAndRenderFilteredBooks(formData);
  });

  document
    .getElementById("reset-filter")
    .addEventListener("click", function () {
      form.reset();
      fetch("actions/filter_books.php")
        .then((response) => response.json())
        .then((data) => {
          renderBooksTable(data.books || []);
        })
        .catch(() => {
          showError("Error resetting filter");
        });
    });
}
