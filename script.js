const API_URL = "http://localhost/digital-library/api.php";
let books = [];

function toggleModal(show, bookData = null) {
  const modal = document.getElementById("bookModal");
  modal.style.display = show ? "flex" : "none";

  if (show) {
    if (bookData) {
      document.getElementById("modalTitle").textContent = "Edit Book";
      document.getElementById("bookId").value = bookData.id;
      document.getElementById("title").value = bookData.title;
      document.getElementById("author").value = bookData.author;
      document.getElementById("format").value = bookData.format;
      document.getElementById("status").value = bookData.status;
    } else {
      document.getElementById("modalTitle").textContent = "Add New Book";
      document.getElementById("bookForm").reset();
      document.getElementById("bookId").value = "";
    }
  }
}

// Display the book stats and update the stats when a book is added, edited or deleted
// The function is also filter the read status of the books
function updateStats() {
  document.getElementById("totalBooks").textContent = books.length;
  document.getElementById("readBooks").textContent = books.filter(
    (b) => b.status === "read"
  ).length;
  document.getElementById("unreadBooks").textContent = books.filter(
    (b) => b.status === "unread"
  ).length;
}

function getFormatIcon(format) {
  switch (format) {
    case "pdf":
      return "fa-file-pdf";
    case "ebook":
      return "fa-book";
    case "audiobook":
      return "fa-headphones";
    default:
      return "fa-file";
  }
}

function renderBooks(booksToRender = books) {
  const booksList = document.getElementById("booksList");
  booksList.innerHTML = "";

  booksList.className =
    "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4";

  booksToRender.forEach((book) => {
    const formatIcon = getFormatIcon(book.format);
    const statusColor =
      book.status === "read" ? "text-green-600" : "text-yellow-600";
    const statusIcon = book.status === "read" ? "fa-check-circle" : "fa-clock";

    const bookCard = `
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 max-w-sm">
      <div class="p-4">
          <div class="flex items-center justify-between mb-3">
              <div class="flex items-center">
                  <i class="fas ${formatIcon} text-primary text-xl"></i>
                  <h3 class="ml-2 font-semibold text-lg truncate">${
                    book.title
                  }</h3>
              </div>
              <div class="flex items-center">
                  <i class="fas ${statusIcon} ${statusColor}"></i>
              </div>
          </div>
          <p class="text-gray-600 mb-3 truncate">by ${book.author}</p>
          <div class="flex items-center justify-between">
              <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                  ${book.format.toUpperCase()}
              </span>
              <div class="flex space-x-1">
                  <button onclick="openFileViewer('${encodeURIComponent(
                    JSON.stringify(book)
                  )}')" 
                          class="p-2 text-green-600 hover:bg-green-50 rounded-full"
                          title="Read/View">
                      <i class="fas fa-book-open"></i>
                  </button>
                  <button onclick="editBook('${encodeURIComponent(
                    JSON.stringify(book)
                  )}')" 
                          class="p-2 text-blue-600 hover:bg-blue-50 rounded-full">
                      <i class="fas fa-edit"></i>
                  </button>
                  <button onclick="deleteBook(${book.id})" 
                          class="p-2 text-red-600 hover:bg-red-50 rounded-full">
                      <i class="fas fa-trash"></i>
                  </button>
              </div>
          </div>
      </div>
    </div>
    `;
    booksList.innerHTML += bookCard;
  });

  updateStats();
}

// function to open the file viewer
function openFileViewer(encodedBookData) {
  try {
    const book = JSON.parse(decodeURIComponent(encodedBookData));
    const viewerModal = document.getElementById("fileViewerModal");
    const viewerTitle = document.getElementById("viewerTitle");
    const viewerContent = document.getElementById("fileViewerContent");

    if (!book.file_path) {
      throw new Error("File path is missing");
    }

    viewerTitle.textContent = `Reading: ${book.title || "Untitled"}`;
    viewerContent.innerHTML = "";

    switch (book.format) {
      case "pdf":
        viewerContent.innerHTML = `
          <embed src="${book.file_path}" type="application/pdf" 
                 style="width: 100%; height: 100%;">
        `;
        break;

      case "ebook":
        viewerContent.innerHTML = `
          <div class="text-center p-4">
            <a href="${book.file_path}" download class="bg-primary text-white px-4 py-2 rounded">
              Download eBook
            </a>
          </div>
        `;
        break;

      case "audiobook":
        viewerContent.innerHTML = `
          <audio controls class="w-full">
            <source src="${book.file_path}" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        `;
        break;
    }

    viewerModal.style.display = "block";
  } catch (error) {
    console.error("Error opening file viewer:", error);
  }
}

function closeFileViewer() {
  document.getElementById("fileViewerModal").style.display = "none";
}

async function fetchBooks() {
  try {
    const response = await axios.get(`${API_URL}?action=read`);
    books = response.data;
    renderBooks();
  } catch (error) {
    console.error("Error fetching books:", error);
  }
}

document
  .getElementById("addBookBtn")
  .addEventListener("click", () => toggleModal(true));

function showSuccessModal(operation) {
  const modal = document.getElementById("successModal");
  const modalContent = document.getElementById("modalContent");
  const title = document.getElementById("successTitle");
  const message = document.getElementById("successMessage");

  // Set appropriate messages based on operation
  switch (operation) {
    case "add":
      title.textContent = "Book Added Successfully!";
      message.textContent = "Your new book has been added to the library.";
      break;
    case "edit":
      title.textContent = "Book Updated Successfully!";
      message.textContent = "Your book details have been updated.";
      break;
    case "delete":
      title.textContent = "Book Deleted Successfully!";
      message.textContent = "The book has been removed from your library.";
      break;
  }

  // Show modal with animation
  modal.style.display = "flex";
  setTimeout(() => {
    modalContent.classList.add("show");
  }, 10);
}

function closeSuccessModal() {
  const modal = document.getElementById("successModal");
  const modalContent = document.getElementById("modalContent");

  modalContent.classList.remove("show");
  setTimeout(() => {
    modal.style.display = "none";
  }, 300);
}

// Edit book
function editBook(encodedBookData) {
  try {
    const book = JSON.parse(decodeURIComponent(encodedBookData));

    document.getElementById("modalTitle").textContent = "Edit Book";

    document.getElementById("bookId").value = book.id;
    document.getElementById("title").value = book.title;
    document.getElementById("author").value = book.author;
    document.getElementById("format").value = book.format;
    document.getElementById("status").value = book.status;
    document.getElementById("file").removeAttribute("required");

    document.getElementById("saveBook").textContent = "Update Book";

    toggleModal(true);
  } catch (error) {
    console.error("Error preparing edit form:", error);
  }
}

function toggleModal(show) {
  const modal = document.getElementById("bookModal");
  const form = document.getElementById("bookForm");
  const fileInput = document.getElementById("file");

  if (show) {
    modal.style.display = "flex";

    if (!document.getElementById("bookId").value) {
      form.reset();
      document.getElementById("modalTitle").textContent = "Add New Book";
      fileInput.setAttribute("required", "required");
    }
  } else {
    modal.style.display = "none";
    form.reset();
    document.getElementById("bookId").value = "";
    document.getElementById("modalTitle").textContent = "Add New Book";
    document.getElementById("saveBook").textContent = "Save Book";
    fileInput.setAttribute("required", "required");
  }
}

// Save book
document.getElementById("bookForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const bookId = document.getElementById("bookId").value;
  const fileInput = document.getElementById("file");

  try {
    let formData = new FormData();

    // Handle file upload first if there's a new file uploaded
    let filePath = "";
    if (fileInput.files.length > 0) {
      formData.append("file", fileInput.files[0]);
      const uploadResponse = await axios.post("upload_handler.php", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      filePath = uploadResponse.data.file_path;
    }

    // Prepare book data
    const bookData = {
      title: document.getElementById("title").value,
      author: document.getElementById("author").value,
      format: document.getElementById("format").value,
      status: document.getElementById("status").value,
      file_path: filePath || (bookId ? undefined : ""), // Keep existing path if editing
    };

    if (bookId) {
      // Edit operation
      await axios.put(`${API_URL}?action=update`, {
        ...bookData,
        id: parseInt(bookId),
      });
      showSuccessModal("edit");
    } else {
      // Add operation
      await axios.post(`${API_URL}?action=create`, bookData);
      showSuccessModal("add");
    }

    toggleModal(false);
    await fetchBooks();
  } catch (error) {
    console.error("Error saving book:", error);
  }
});

async function deleteBookFile(filePath) {
  try {
    const response = await fetch("upload_handler.php", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        file_path: filePath,
      }),
    });
    const result = await response.json();
    return result;
  } catch (error) {
    console.error("Error:", error);
  }
}

async function deleteBook(id) {
  try {
    const book = books.find((b) => b.id === id);
    if (book && book.file_path) {
      await deleteBookFile(book.file_path);
    }
    await axios.delete(`${API_URL}?action=delete`, {
      data: {
        id,
      },
    });
    showSuccessModal("delete");
    await fetchBooks();
  } catch (error) {
    console.error("Error deleting book:", error);
  }
}

// Close modal with Escape key
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeSuccessModal();
  }
});

// Close modal when clicking outside
document.getElementById("successModal").addEventListener("click", (e) => {
  if (e.target.id === "successModal") {
    closeSuccessModal();
  }
});

document.getElementById("searchInput").addEventListener("input", (e) => {
  const searchTerm = e.target.value.toLowerCase();
  const formatFilter = document.getElementById("formatFilter").value;
  const statusFilter = document.getElementById("statusFilter").value;

  const filteredBooks = books.filter((book) => {
    const matchesSearch =
      book.title.toLowerCase().includes(searchTerm) ||
      book.author.toLowerCase().includes(searchTerm);
    const matchesFormat = !formatFilter || book.format === formatFilter;
    const matchesStatus = !statusFilter || book.status === statusFilter;
    return matchesSearch && matchesFormat && matchesStatus;
  });

  renderBooks(filteredBooks);
});

["formatFilter", "statusFilter"].forEach((filterId) => {
  document.getElementById(filterId).addEventListener("change", () => {
    document.getElementById("searchInput").dispatchEvent(new Event("input"));
  });
});

// Loading States when fecthing data
function setLoadingState(loading) {
  const booksList = document.getElementById("booksList");
  if (loading) {
    booksList.innerHTML = `
                    <div class="col-span-full flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                    </div>
                `;
  }
}

// handle file type validation
document.getElementById("file").addEventListener("change", function (e) {
  const file = e.target.files[0];
  const allowedTypes = {
    pdf: ["application/pdf"],
    ebook: ["application/epub+zip", "application/x-mobipocket-ebook"],
    audiobook: ["audio/mpeg", "audio/mp4", "audio/x-m4a"],
  };

  const format = document.getElementById("format").value;
  if (!allowedTypes[format].includes(file.type)) {
    showToast(`Invalid file type for ${format} format`, "error");
    this.value = "";
  }
});

// Keyboard Shortcuts
document.addEventListener("keydown", (e) => {
  if (e.ctrlKey && e.key === "a") {
    // Ctrl+A to open add book modal
    e.preventDefault();
    toggleModal(true);
  }
  if (e.key === "Escape") {
    // Escape to close modal
    toggleModal(false);
  }
});

// Drag and Drop File Upload
const fileInput = document.getElementById("file");
["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
  fileInput.addEventListener(eventName, (e) => {
    e.preventDefault();
    e.stopPropagation();
  });
});

fileInput.addEventListener("drop", (e) => {
  const file = e.dataTransfer.files[0];
  if (file) {
    const dT = new DataTransfer();
    dT.items.add(file);
    fileInput.files = dT.files;
    fileInput.dispatchEvent(new Event("change"));
  }
});

// Add hover effects for dropzone
fileInput.addEventListener("dragenter", () => {
  fileInput.classList.add("border-primary", "bg-blue-50");
});

fileInput.addEventListener("dragleave", () => {
  fileInput.classList.remove("border-primary", "bg-blue-50");
});

// Read Progress Tracking
async function updateReadStatus(bookId, status) {
  try {
    await axios.put(`${API_URL}?action=update`, {
      id: bookId,
      status: status,
    });
    fetchBooks();
  } catch (error) {
    console.error("Error updating read status:", error);
  }
}

// Initialize tooltips
function initTooltips() {
  const tooltips = document.querySelectorAll("[data-tooltip]");
  tooltips.forEach((element) => {
    element.addEventListener("mouseenter", (e) => {
      const tooltip = document.createElement("div");
      tooltip.className =
        "absolute bg-gray-800 text-white px-2 py-1 rounded text-sm -mt-8 -ml-1 z-50";
      tooltip.textContent = element.dataset.tooltip;
      element.appendChild(tooltip);
    });

    element.addEventListener("mouseleave", () => {
      const tooltip = element.querySelector("div");
      if (tooltip) tooltip.remove();
    });
  });
}

// Enhanced error handling
window.addEventListener("unhandledrejection", (event) => {
  console.error("Unhandled promise rejection:", event.reason);
});

async function initialize() {
  setLoadingState(true);
  await fetchBooks();
  setLoadingState(false);
  initTooltips();
}

initialize();
