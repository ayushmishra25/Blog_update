<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Retrieve all posts
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->userType === 'Superadmin') {
            $posts = Post::all(); 
        } elseif ($user->userType === 'Admin') {
            $posts = Post::with('user')->where('user_id', $user->id)->get(); 
        } else {
            $posts = Post::with('user')->where('user_id', $user->id)->get(); 
        }

        return response()->json([
            'status' => true,
            'message' => 'Posts retrieved successfully',
            'data' => $posts
        ], 200);
    }

    // Store a new post
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
        }

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Post created successfully',
            'data' => $post,
            'user_id' => $user->id,
        ], 201);
    }

    

    // Show a single post by ID
    public function show($id)
    {
        $post = Post::with('user')->find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Post retrieved successfully',
            'data' => $post,
        ], 200);
    }

    // Update an existing post
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ], 404);
        }

        // Check if the logged-in user is allowed to edit the post
        $user = Auth::user();
        if ($user->userType !== 'Superadmin' && $post->user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action',
            ], 403);
        }

        $post->title = $request->title;
        $post->description = $request->description;

        if ($request->hasFile('image')) {
            $imagePath = public_path('uploads/' . $post->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
            $post->image = $imageName;
        }

        $post->save();

        return response()->json([
            'status' => true,
            'message' => 'Post updated successfully',
            'data' => $post,
        ], 200);
    }

    // Delete a post by ID
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
    
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized access'], 401);
        }
    
        $user = Auth::user();
    
        // Allow Superadmin to delete any post
        if ($user->userType === 'Superadmin') {
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully']);
        }
    
        // Allow Admin to delete only their own posts
        if ($user->userType === 'Admin' && $user->id === $post->user_id) {
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully']);
        }
    
        // If user is neither Superadmin nor the owner of the post
        return response()->json(['message' => 'You do not have permission to delete this post'], 403);
    }
    
}


