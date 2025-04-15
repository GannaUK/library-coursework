// Save the active tab on click
document.querySelectorAll('button[data-bs-toggle="tab"]').forEach((button) => {
  button.addEventListener("shown.bs.tab", function (event) {
    localStorage.setItem(
      "activeTab",
      event.target.getAttribute("data-bs-target")
    );
  });
});

// Activate the saved tab on page load
document.addEventListener("DOMContentLoaded", function () {
  const activeTab = localStorage.getItem("activeTab");
  if (activeTab) {
    const tabTrigger = document.querySelector(
      `button[data-bs-target="${activeTab}"]`
    );
    if (tabTrigger) {
      new bootstrap.Tab(tabTrigger).show();
    }
  }
});
