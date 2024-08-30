<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookFormRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Services\BookService;
use GuzzleHttp\Promise\Create;
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
    public function index()
    {
        $books = Book::all();
        return ApiResponse::success($books, 'Books retrieved successfully');
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
