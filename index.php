<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Digital Library - Your Personal Book Collection</title>
     <script src="https://cdn.tailwindcss.com"></script>
     <link rel="stylesheet" href="style.css">
     <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.2/axios.min.js"></script>
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
     <script>
     tailwind.config = {
          theme: {
               extend: {
                    colors: {
                         primary: '#2563eb',
                         secondary: '#475569',
                         accent: '#3b82f6'
                    }
               }
          }
     }
     </script>
</head>

<body class="bg-gray-50 min-h-screen">
     <nav class="bg-white shadow-lg">
          <div class="container mx-auto px-4">
               <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                         <i class="fas fa-book-reader text-primary text-2xl"></i>
                         <h1 class="text-2xl font-bold text-gray-800">Digital Library</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                         <button id="addBookBtn"
                              class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center">
                              <i class="fas fa-plus mr-2"></i> Add Book
                         </button>
                    </div>
               </div>
          </div>
     </nav>

     <!-- Main Content -->
     <div class="container mx-auto px-4 py-8">
          <!-- Stats Section -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
               <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                         <div class="p-3 bg-blue-100 rounded-full">
                              <i class="fas fa-book text-primary text-xl"></i>
                         </div>
                         <div class="ml-4">
                              <h3 class="text-gray-500 text-sm">Total Books</h3>
                              <p class="text-2xl font-semibold" id="totalBooks">0</p>
                         </div>
                    </div>
               </div>
               <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                         <div class="p-3 bg-green-100 rounded-full">
                              <i class="fas fa-check-circle text-green-600 text-xl"></i>
                         </div>
                         <div class="ml-4">
                              <h3 class="text-gray-500 text-sm">Read Books</h3>
                              <p class="text-2xl font-semibold" id="readBooks">0</p>
                         </div>
                    </div>
               </div>
               <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                         <div class="p-3 bg-yellow-100 rounded-full">
                              <i class="fas fa-clock text-yellow-600 text-xl"></i>
                         </div>
                         <div class="ml-4">
                              <h3 class="text-gray-500 text-sm">Unread Books</h3>
                              <p class="text-2xl font-semibold" id="unreadBooks">0</p>
                         </div>
                    </div>
               </div>
          </div>

          <!-- Search and Filter Section -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-8">
               <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                         <div class="relative">
                              <input type="text" id="searchInput" placeholder="Search books..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                              <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                         </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                         <select id="formatFilter"
                              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                              <option value="">All Formats</option>
                              <option value="pdf">PDF</option>
                              <option value="ebook">eBook</option>
                              <option value="audiobook">Audiobook</option>
                         </select>
                         <select id="statusFilter"
                              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                              <option value="">All Status</option>
                              <option value="read">Read</option>
                              <option value="unread">Unread</option>
                         </select>
                    </div>
               </div>
          </div>

          <div id="booksList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
               <!-- Books will be dynamically added here -->
          </div>
     </div>

     <!-- Add/Edit Book Modal -->
     <div id="bookModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
          <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
               <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold" id="modalTitle">Add New Book</h2>
                    <button onclick="toggleModal(false)" class="text-gray-500 hover:text-gray-700">
                         <i class="fas fa-times"></i>
                    </button>
               </div>
               <form id="bookForm" class="space-y-4">
                    <input type="hidden" id="bookId">
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                         <input type="text" id="title" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                         <input type="text" id="author" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                         <select id="format" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                              <option value="pdf">PDF</option>
                              <option value="ebook">eBook</option>
                              <option value="audiobook">Audiobook</option>
                         </select>
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                         <select id="status" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                              <option value="unread">Unread</option>
                              <option value="read">Read</option>
                         </select>
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                         <p class="text-sm text-gray-500">Supported formats: PDF, EPUB, MOBI, MP3, MP4, M4A, OGG, WAV
                         </p>
                         <p class="text-sm text-gray-500">Max file size: 500MB</p>
                         <input type="file" id="file" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                         <button type="button" onclick="toggleModal(false)"
                              class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                              Cancel
                         </button>
                         <button type="submit" id="saveBook"
                              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700">
                              Save Book
                         </button>
                    </div>
               </form>
          </div>
     </div>

     <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
          <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 transform transition-all duration-300 scale-0"
               id="modalContent">
               <div class="text-center">
                    <!-- SVG Check Animation -->
                    <div class="success-checkmark">
                         <div class="check-icon">
                              <span class="icon-line line-tip"></span>
                              <span class="icon-line line-long"></span>
                              <div class="icon-circle"></div>
                              <div class="icon-fix"></div>
                         </div>
                    </div>

                    <h2 class="text-2xl font-semibold mt-4 text-gray-800" id="successTitle">Success!</h2>
                    <p class="text-gray-600 mt-2" id="successMessage">Operation completed successfully.</p>

                    <button onclick="closeSuccessModal()"
                         class="mt-6 px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition duration-300">
                         Continue
                    </button>
               </div>
          </div>
     </div>

     <div id="fileViewerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
          <div class="container mx-auto px-4 h-full flex items-center justify-center">
               <div class="bg-white rounded-lg shadow-lg w-full max-w-8xl h-4/5 flex flex-col">
                    <div class="p-4 border-b flex justify-between items-center">
                         <h3 class="text-xl font-semibold" id="viewerTitle">Reading: </h3>
                         <button onclick="closeFileViewer()" class="text-gray-500 hover:text-gray-700">
                              <i class="fas fa-times"></i>
                         </button>
                    </div>
                    <div class="flex-1 p-4 overflow-auto" id="fileViewerContent">
                         <!-- Content of file will be loaded here -->
                    </div>
               </div>
          </div>
     </div>

     <script src="script.js"></script>
</body>

</html>