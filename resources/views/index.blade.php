<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>


    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-8 bg-primary text-white p-3">
                <h1>All Posts</h1>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-8">
                <a href="/user" class="btn btn-sm btn-primary">Add New</a>
                <button class="btn btn-sm btn-danger" id="logoutBtn">Logout</button>
            </div>
        </div>

        

        <div class="container mt-4">
            <div class="row mb-4">
                <div class="col-8 bg-primary text-white p-3">
                    <h4 id="userName"></h4> 
                </div>
            </div>
        </div> 
        
        <div class="row">
            <div class="col-12">
               
                <table class="table table-bordered" id="postsTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>View</th>
                            <th>Update</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="postsContainer">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="singlePostModal" tabindex="-1" aria-labelledby="singlePostLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="singlePostLabel">Single Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 id="singlePostTitle"></h5>
                    <p id="singlePostDescription"></p>
                    <img id="singlePostImage" src="" alt="Post Image" class="img-fluid" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="updatePostModal" tabindex="-1" aria-labelledby="updatePostLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePostLabel">Update Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="updatePostId">
                    <input type="text" id="updatePostTitle" class="form-control" placeholder="Post Title">
                    <textarea id="updatePostDescription" class="form-control mt-2" placeholder="Post Description"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updatePostBtn">Update</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
                
                const username = localStorage.getItem('username');
                
                if (username) {
                    document.getElementById('userName').textContent = `Welcome, ${username}`;
                } else {
                    alert('Username not found. Please log in.');
                }
            });

        document.querySelector('#logoutBtn').addEventListener('click', function () {
            const token = localStorage.getItem('api_token');
            if (!token) {
                alert('No token found. Please log in.');
                return;
            }
    
            fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert('Logged out successfully');
                localStorage.removeItem('api_token');
                
                window.location.href = '/';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Logout failed. Please try again.');
            });
        });
    
        function deletePost(id) {
            const token = localStorage.getItem('api_token');
            if (!token) {
                alert('No token found. Please log in.');
                return;
            }

            if (confirm('Are you sure you want to delete this post?')) {
                fetch(`/api/posts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Failed to delete the post.');
                        }
                        return response.json();
                    })
                    .then(() => {
                        alert('Post deleted successfully');
                        loadData(); // Reload posts after deletion
                    })
                    .catch((error) => {
                        console.error('Error during DELETE:', error);
                        alert('Failed to delete the post. Please try again.');
                    });
            }
        }

        function viewPost(id) {
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
            .then(postData => {
                if (postData && postData.data) {
                    document.querySelector("#singlePostTitle").textContent = postData.data.title;
                    document.querySelector("#singlePostDescription").textContent = postData.data.description;
                    document.querySelector("#singlePostImage").src = `/uploads/${postData.data.image}`;
                    const viewModal = new bootstrap.Modal(document.getElementById('singlePostModal'));
                    viewModal.show();
                } else {
                    alert('Failed to load post details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to fetch post data.');
            });
        }
    
        function openUpdateModal(id) {
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
            .then(postData => {
                if (postData && postData.data) {
                    document.querySelector('#updatePostId').value = postData.data.id;
                    document.querySelector('#updatePostTitle').value = postData.data.title;
                    document.querySelector('#updatePostDescription').value = postData.data.description;
    
                    const updatePostBtn = document.querySelector('#updatePostBtn');
                    updatePostBtn.addEventListener('click', function() {
                        const postId = document.querySelector('#updatePostId').value;
                        const title = document.querySelector('#updatePostTitle').value;
                        const description = document.querySelector('#updatePostDescription').value;
    
                        fetch(`/api/posts/${postId}`, {
                            method: 'PUT',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ title, description })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                alert('Post updated successfully');
                                loadData(); 
                                const updateModal = new bootstrap.Modal(document.getElementById('updatePostModal'));
                                updateModal.hide();
                            } else {
                                alert('Failed to update post');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating post');
                        });
                    });
                    const updateModal = new bootstrap.Modal(document.getElementById('updatePostModal'));
                    updateModal.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load post for updating.');
            });
        }
    
        function loadData() {
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
            .then(posts => {
                let tableContent = '';
                posts.data.forEach(post => {
                    tableContent += `
                        <tr>
                            <td><img src="/uploads/${post.image}" alt="Post Image" width="100"></td>
                            <td>${post.title}</td>
                            <td>${post.description}</td>
                            <td><button class="btn btn-info btn-sm" onclick="viewPost(${post.id})">View</button></td>
                            <td><button class="btn btn-warning btn-sm" onclick="openUpdateModal(${post.id})">Update</button></td>
                            <td><button class="btn btn-danger btn-sm" onclick="deletePost(${post.id})">Delete</button></td>
                        </tr>
                    `;
                });
                document.getElementById('postsContainer').innerHTML = tableContent;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load posts.');
            });
        }
    
        
        loadData();
    </script>
</body>

</html>
