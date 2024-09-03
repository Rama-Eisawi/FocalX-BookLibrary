<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowRecordFormRequest;
use App\Http\Responses\ApiResponse;
use App\Models\BorrowRecord;
use Illuminate\Http\Request;

class BorrowRecordController extends Controller
{
    /**
     * Display a list of borrowed books for the authenticated user.
     * This includes both returned and unreturned books.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get the currently authenticated user
        $user = auth()->user();

        // Retrieve all borrow records for the authenticated user, including related book information
        $borrowRecords = BorrowRecord::with('book')
            ->where('user_id', $user->id) // Filter records to only those belonging to the authenticated user
            ->get();

        // Return the borrow records with a success message
        return ApiResponse::success($borrowRecords, 'Borrowed books fetched successfully', 200);
    }

    /**
     * Store a new borrow record in the database.
     * This method handles the creation of a new borrow record for a book.
     *
     * @param BorrowRecordFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BorrowRecordFormRequest $request)
    {
        // Get the currently authenticated user
        $user = auth()->user();

        // Create a new borrow record with the provided book_id, and automatically set borrowed_at and due_date
        $borrowRecord = BorrowRecord::create([
            'user_id' => $user->id,
            'book_id' => $request->book_id,
            'borrowed_at' => now(), // Set the current timestamp as the borrowed_at date
            'due_date' => now()->addDays(14), // Set the due date to 14 days from now
        ]);

        // Load the related book information into the borrow record
        $borrowRecord->load('book');

        // Return the newly created borrow record with a success message
        return ApiResponse::success($borrowRecord, 'Book borrowed successfully', 201);
    }

    /**
     * Display a specific borrow record.
     * This method retrieves a specific borrow record by its ID for the authenticated user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Get the currently authenticated user
        $user = auth()->user();

        // Retrieve the specific borrow record for the authenticated user, including related book information
        $borrowRecord = BorrowRecord::with('book')
            ->where('user_id', $user->id) // Ensure the record belongs to the authenticated user
            ->find($id);

        // If the record is not found, return an error response
        if (!$borrowRecord) {
            return ApiResponse::error('Borrow record not found', 404);
        }

        // Return the found borrow record with a success message
        return ApiResponse::success($borrowRecord, 'Borrow record fetched successfully', 200);
    }

    /**
     * Update a borrow record to mark the book as returned.
     * This method sets the returned_at field to the current timestamp for the specified record.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        // Find the borrow record by its ID, or throw a 404 error if not found
        $borrowRecord = BorrowRecord::findOrFail($id);

        // Update the record to set the returned_at date to the current timestamp
        $borrowRecord->update([
            'returned_at' => now(),
        ]);

        // Return the updated borrow record with a success message
        return ApiResponse::success($borrowRecord, 'Book returned successfully', 200);
    }

    /**
     * Remove a borrow record from the database.
     * This method deletes a borrow record if the book has been returned.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Find the borrow record by its ID
        $borrowRecord = BorrowRecord::find($id);

        // Check if the book has been returned (i.e., returned_at is not null)
        if ($borrowRecord->returned_at) {
            // If the book has been returned, delete the borrow record
            $borrowRecord->delete();
            return ApiResponse::success(null, 'Borrow record deleted successfully', 200);
        } else {
            // If the book has not been returned, return an error response
            return ApiResponse::error('Cannot delete a record for an unreturned book', 400);
        }
    }
}
