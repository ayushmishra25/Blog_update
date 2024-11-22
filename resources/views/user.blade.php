<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Create Post</h2>
        <form id="addform">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>

            <a href="/allposts" class="btn btn-secondary">Back</a>
        </form>
    </div>

    <!-- Optional: Bootstrap JS for additional features -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector("#addform").onsubmit = async (e) => {
            e.preventDefault();

            // Retrieve the token from localStorage
            const token = localStorage.getItem('api_token');
            if (!token) {
                alert('No authorization token found. Please log in again.');
                return;
            }

            const title = document.querySelector("#title").value;
            const description = document.querySelector("#description").value;
            const image = document.querySelector("#image").files[0];

            let formData = new FormData();
            formData.append('title', title);
            formData.append('description', description);
            formData.append('image', image);

            try {
                // Making the POST request
                let response = await fetch('/api/posts', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                // Check if the response is successful
                if (!response.ok) {
                    throw new Error('Failed to create post');
                }

                // Parse the response
                let data = await response.json();
                console.log('Response data:', data);

                // Check for successful creation
                if (data.status === true) {
                    window.location.assign("/index");
                } else {
                    alert(data.message || 'Failed to create post');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while creating the post.');
            }
        };
    </script>
</body>
</html>
