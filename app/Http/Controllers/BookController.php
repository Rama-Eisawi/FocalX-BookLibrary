<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookFormRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookService;
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }
    /**
     * Show all books.
     */
    public function index(Request $request)
    {
        // Get the filter parameters from the request
        $author = $request->query('author');
        $categoryId = $request->query('category');

        // Create a query builder instance for Book
        $booksQuery = Book::query();

        // Apply the filter if the 'author' parameter is provided
        if ($author) {
            $booksQuery->byAuthor($author);
        }
        if ($categoryId) {
            $booksQuery->where('category_id', $categoryId);
        }
        $books = $booksQuery->with(['category', 'ratings'])->availableForBorrowing()->get();
        // Return the list of books with a success message
        return ApiResponse::success($books, 'Books fetched successfully', 200);
    }
    //----------------------------------------------------------------------------------------
    /**
     * Create a new book.
     */
    public function store(BookFormRequest $request)
    {
        $bookRequest = [
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'published_at' => $request->published_at,
        ];
        $newBook = $this->bookService->createBook($bookRequest);
        return ApiResponse::success($newBook, 'Book created successfully', 201);
    }
    //----------------------------------------------------------------------------------------
    /**
     * Show a specific book.
     */
    public function show(Book $book)
    {
        $book->findOrFail($book->id);
        return ApiResponse::success($book, 'Book retrieved successfully');
    }
    //----------------------------------------------------------------------------------------
    /**
     * Update a specific book.
     */
    public function update(BookFormRequest $request, Book $book)
    {
        $updatedBook = $this->bookService->updateBook($book, $request->validated());
        return ApiResponse::success($book, 'Book updated successfully');
    }
    //----------------------------------------------------------------------------------------
    /**
     * Delete a specific book.
     */
    public function destroy(Book $book)
    {
        $this->bookService->deleteBook($book);
        return ApiResponse::success(null, 'Book deleted successfully');
    }
}
