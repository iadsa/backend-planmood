<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostJurnal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class JurnalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $user = Auth::user();
        $posts = PostJurnal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_dibuat' => 'required|date',
            'input_mood' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
        ]);

        $postJurnal = new PostJurnal([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_dibuat' => $request->tanggal_dibuat,
            'input_mood' => $request->input_mood,
            'image' => $request->image,
        ]);

        $postJurnal->save();

        return response()->json([
            'message' => 'Jurnal berhasil dibuat!',
            'data' => $postJurnal
        ], 201);
    }

    public function show($id)
    {
        $post = PostJurnal::where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = PostJurnal::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_dibuat' => 'required|date',
            'input_mood' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $post->image;
        if ($request->hasFile('image')) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $file = $request->file('image');
            $path = $file->store('images', 'public');
        }

        $post->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_dibuat' => $request->tanggal_dibuat,
            'input_mood' => $request->input_mood,
            'image' => $path,
        ]);

        $post->load('user');

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }

    public function destroy($id)
    {
        $post = PostJurnal::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}
