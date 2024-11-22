<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Blogs</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12 bg-primary text-white p-3">
                <h1>All Blogs</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table table-bordered" id="blogsTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody id="blogsContainer">
                        <!-- Blog data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Blog Modal -->
    <div class="modal fade" id="viewBlogModal" tabindex="-1" aria-labelledby="viewBlogLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewBlogLabel">Blog Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 id="viewBlogTitle"></h5>
                    <p id="viewBlogDescription"></p>
                    <img id="viewBlogImage" src="" alt="Blog Image" class="img-fluid" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fetch and display all blogs
        function loadBlogs() {
            const token = localStorage.getItem('api_token');
            if (!token) {
                alert('No token found. Please log in.');
                return;
            }

            fetch('/api/posts', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(blogs => {
                let tableContent = '';
                blogs.data.forEach(blog => {
                    tableContent += `
                        <tr>
                            <td><img src="/uploads/${blog.image}" alt="Blog Image" width="100"></td>
                            <td>${blog.title}</td>
                            <td>${blog.description.substring(0, 100)}...</td>
                            <td><button class="btn btn-info btn-sm" onclick="viewBlog(${blog.id})">View</button></td>
                        </tr>
                    `;
                });
                document.getElementById('blogsContainer').innerHTML = tableContent;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load blogs.');
            });
        }

        // View a specific blog
        function viewBlog(id) {
            const token = localStorage.getItem('api_token');
            if (!token) {
                alert('No token found. Please log in.');
                return;
            }

            fetch(`/api/posts/${id}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(blogData => {
                if (blogData && blogData.data) {
                    document.getElementById('viewBlogTitle').textContent = blogData.data.title;
                    document.getElementById('viewBlogDescription').textContent = blogData.data.description;
                    document.getElementById('viewBlogImage').src = `/uploads/${blogData.data.image}`;
                    const viewModal = new bootstrap.Modal(document.getElementById('viewBlogModal'));
                    viewModal.show();
                } else {
                    alert('Failed to load blog details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to fetch blog data.');
            });
        }

        // Load blogs when the page is ready
        document.addEventListener('DOMContentLoaded', loadBlogs);
    </script>
</body>

</html>
