<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function getAllBlogs()
    {
        try {

            $blogs = Blog::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Blogs fetched successfully',
                'data' => $blogs
            ], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }


    public function editAndSaveBlogData(Request $request)
    {

        if ($request->id) {

            $checkslug = Blog::where('slug', $request->get('slug'))->where('status', '!=', 'deleted')->where('id', '!=', $request->id)->count();

            if ($checkslug >= 1) {

                return response()->json(['status' => false, 'message' => "Please choose different slug its already in use"]);
            } else {

                $blog = Blog::find($request->id);
                $blog->created_by_id = $request->created_by_id;
                $blog->name = $request->name;
                $blog->slug = $request->slug;
                $blog->author_name = $request->author_name;
                $blog->tag = $request->tag;
                $blog->description = $request->description;
                $blog->meta_tag = $request->meta_tag;
                $blog->meta_desc = $request->meta_desc;


                if ($request->hasFile('image')) {
                    $randomNumber = mt_rand(1000000000, 9999999999);
                    $imagePath = $request->file('image');
                    $imageName = $randomNumber . $imagePath->getClientOriginalName();
                    $imagePath->move(public_path('images/blogs'), $imageName);

                    $blog->image = $imageName;
                }

                if ($request->hasFile('author_image')) {
                    $randomNumber = mt_rand(1000000000, 9999999999);
                    $imagePath = $request->file('author_image');
                    $imageName = $randomNumber . $imagePath->getClientOriginalName();
                    $imagePath->move(public_path('images/blogs/author'), $imageName);

                    $blog->author_image = $imageName;
                }


                $blog->save();


                if ($blog->save()) {

                    return response()->json(['status' => true, 'message' => 'Blog data has been updated successfully']);
                } else {
                    return response()->json(['status' => false, 'message' => 'There has been error for saving the blog data', 'error' => '', 'data' => '']);
                }
            }
        } else {

            $checkslug = Blog::where('slug', $request->get('slug'))->where('status', '!=', 'deleted')->count();

            if ($checkslug >= 1) {

                return response()->json(['status' => false, 'message' => "Please choose different slug its already in use"]);
            } else {
                $blog = new Blog();
                $blog->created_by_id = $request->created_by_id;
                $blog->name = $request->name;
                $blog->slug = $request->slug;
                $blog->author_name = $request->author_name;
                $blog->tag = $request->tag;
                $blog->description = $request->description;
                $blog->meta_tag = $request->meta_tag;
                $blog->meta_desc = $request->meta_desc;

                if ($request->hasFile('image')) {
                    $randomNumber = mt_rand(1000000000, 9999999999);
                    $imagePath = $request->file('image');
                    $imageName = $randomNumber . $imagePath->getClientOriginalName();
                    $imagePath->move(public_path('images/blogs'), $imageName);
                    $blog->image = $imageName;
                }

                if ($request->hasFile('author_image')) {
                    $randomNumber = mt_rand(1000000000, 9999999999);
                    $imagePath = $request->file('author_image');
                    $imageName = $randomNumber . $imagePath->getClientOriginalName();
                    $imagePath->move(public_path('images/blogs/author'), $imageName);
                    $blog->author_image = $imageName;
                }



                $blog->save();


                if ($blog->save()) {

                    return response()->json(['status' => true, 'message' => 'Blog data has been save successfully']);
                } else {
                    return response()->json(['status' => false, 'message' => 'There has been error for saving the blog data', 'error' => '', 'data' => '']);
                }
            }
        }

        try {
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function getDataByTag($tags)
    {
        $tagsArray = explode(',', $tags);
        $query = Blog::where(function ($query) use ($tagsArray) {
            foreach ($tagsArray as $tag) {
                $query->orWhere('tag', 'LIKE', '%' . $tag . '%');
            }
        })->get();
        return $query;
    }

    public function deleteBlog(Request $request)
    {
        try {

            $blog = Blog::find($request->id);
            $blog->status = 'delete';
            $blog->save();
            return response()->json([
                'status' => true,
                'message' => 'Blog has been deleted successfully!',
            ], 200);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function getBlogBySlug($slug)
    {
        try {
            $blog = Blog::where('slug', $slug)->where('status', 'active')->first();

            if ($blog) {
                return response()->json([
                    'status' => true,
                    'message' => 'Single Blog data fetched successfully',
                    'data' => $blog
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No Single Blog data found',
                    'data' => null
                ], 404);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }


}
