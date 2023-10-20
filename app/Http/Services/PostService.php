<?php

namespace App\Http\Services;

use App\Exceptions\CategoryNotFoundException;
use App\Exceptions\EmployeeNotBelongsToManagerException;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Responses\PermissionDeniedResponse;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LaravelIdea\Helper\App\Models\_IH_Post_QB;

class PostService
{
    public function index(Request $request): JsonResponse
    {
        try {
            $postQuery = $this->buildIndexPostQuery($request->input('category_name'), $request->input('user_id'));
            $posts = $postQuery->paginate(10);
        } catch (EmployeeNotBelongsToManagerException|CategoryNotFoundException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 403);
        }

        return response()->json([
            'success' => true,
            'posts' => PostResource::collection($posts),
        ]);
    }

    /**
     * @throws CategoryNotFoundException|EmployeeNotBelongsToManagerException
     */
    private function buildIndexPostQuery($category_name, $user_id): Builder|_IH_Post_QB
    {
        $postQuery = Post::query();
        if (!empty($category_name)) {
            $postQuery = $this->applyCategoryFilter($postQuery, $category_name);
        }
        $user = auth()->user();
        if ($user->hasRole('employee')) {
            $postQuery->where('user_id', $user->id);
        }
        if ($user->hasRole('manager')) {
            $postQuery = $this->applyManagerFilters($postQuery, $user, $user_id);
        }

        return $postQuery;
    }

    /**
     * @throws CategoryNotFoundException
     */
    private function applyCategoryFilter($postQuery, $category_name)
    {
        return $postQuery->when(!empty($category_name), function ($query) use ($category_name) {
            $category = Category::findByName($category_name);
            return $query->whereHas('category', function ($query) use ($category) {
                $query->where('id', $category->id);
            });
        });
    }

    /**
     * @throws EmployeeNotBelongsToManagerException
     */
    private function applyManagerFilters($postQuery, $user, $user_id)
    {
        if (!empty($user_id)) {
            if ($user->isEmployeeBelongsToManager($user_id)) {
                return $postQuery->where('user_id', $user_id);
            }
            throw new EmployeeNotBelongsToManagerException();
        }

        return $postQuery->whereIn('user_id', $user->employees->pluck('id'));
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $category = Category::findByName($request->category_name);

            $post = Post::create([
                'name' => $request->name,
                'image' => $request->file('image')->store('images', 'public'),
                'category_id' => $category->id,
                'user_id' => auth()->user()->id,
            ]);
        } catch (CategoryNotFoundException $categoryException) {
            return response()->json([
                'success' => false,
                'message' => $categoryException->getMessage(),
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'post' => new PostResource($post),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $user = auth()->user();

        if ($post->user_id != $user->id) {
            return new PermissionDeniedResponse('You do not have permission to update this post');
        }

        $validatedData = $request->validated();

        if (isset($validatedData['category_name'])) {
            try {
                $category = Category::findByName($validatedData['category_name']);
            } catch (CategoryNotFoundException $exception) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], 403);
            }
            $post->category_id = $category->id;
        }

        $post->fill($validatedData)->save();

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            '$post' => new PostResource($post),
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $user = auth()->user();
        if ($user->hasRole('employee')) {
            if ($post->user_id != $user->id) {
                return new PermissionDeniedResponse('You do not have permission to delete this post');
            }
        }

        if ($user->hasRole('manager')) {
            if (!$user->employees->contains('id', $post->user_id)) {
                return new PermissionDeniedResponse('You do not have permission to delete this post');
            }
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }
}
