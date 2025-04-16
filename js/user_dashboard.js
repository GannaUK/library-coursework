document.addEventListener("DOMContentLoaded", () => {
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

  document
    .querySelectorAll(".order-book-btn")
    .forEach((button) => {
    button.addEventListener("click", () => {
      const id = button.dataset.id;
      if (confirm("Order this book?")) {
        orderBook(id);
      }
    });
  });
});

function orderBook(id) {
  const movementData = {
    book_id: id,
    quantity: -1,
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

        triggerBookFilterSubmit();

        attachBookActionHandlers();
      } else {
        showError(movementResponse.message || "Error saving book movement.");
      }
    })
    .catch(() => showError("Server error when adding movement."));
}

function showSuccess(message) {
  alert(message);
}

function showError(message) {
  alert("Error: " + message);
}

function attachBookActionHandlers() {
  
  document.querySelectorAll(".order-book-btn").forEach((button) => {
    button.addEventListener("click", () => {
      if (confirm("Order this book?")) {
        orderBook(button.dataset.id);
      }
    });
  });
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
    })
    .catch(() => {
      showError("Filter error");
    });
}

function renderBooksTable(books) {
  const tbody = document.querySelector("#books-table tbody");
  // Clear old rows
  tbody.innerHTML = "";

  if (books.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center">No books found</td></tr>`;
    return;
  }

  books.forEach((book) => {
    const tr = document.createElement("tr");

    let actionCellContent = "";
    if (book.available > 0) {
      actionCellContent = `
            <div class='btn-group' role='group' aria-label='Basic button group'>
                <button class='btn btn-sm btn-primary order-book-btn' data-id='${book.id}'>Order</button>
            </div>
        `;
    } else {
      actionCellContent = `<span class='text-muted'>Not available</span>`;
    }

    tr.innerHTML = `
        <td>${book.id}</td>
        <td>${book.title}</td>
        <td>${book.author}</td>
        <td>${book.genre}</td>
        <td>${book.description}</td>
        <td>${book.max_days}</td>
        <td>${book.available}</td>
        <td>${actionCellContent}</td>
    `;
    tbody.appendChild(tr);
  });

  attachBookActionHandlers(); // Reattach event handlers to the buttons
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

function triggerBookFilterSubmit() {
  const form = document.getElementById("book-filter-form");
  const event = new Event("submit", { bubbles: true, cancelable: true });
  form.dispatchEvent(event);
}
