<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatingFormRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    //View all rating
    public function index($bookId)
    {
        $ratings = Rating::with('user')
            ->where('book_id', $bookId)
            ->get();

        return ApiResponse::success($ratings, 'Ratings fetched successfully', 200);
    }

    //Creating a new rating
    public function store(RatingFormRequest $request)
    {
        $user = auth()->user();

        $rating = Rating::create([
            'user_id' => $user->id,
            'book_id' => $request->book_id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return ApiResponse::success($rating, 'Rating created successfully', 201);
    }

    // View a specific rating
    public function show($id)
    {
        $rating = Rating::with('user')->findOrFail($id);
        return ApiResponse::success($rating, 'Rating fetched successfully', 200);
    }

    //Update a specific rating
    public function update(RatingFormRequest $request, $id)
    {
        $rating = Rating::findOrFail($id);

        $rating->update($request->only(['rating', 'review']));

        return ApiResponse::success($rating, 'Rating updated successfully', 200);
    }

    // Delete a specific rating
    public function destroy($id)
    {
        $rating = Rating::findOrFail($id);
        $rating->delete();

        return ApiResponse::success(null, 'Rating deleted successfully', 200);
    }
}
