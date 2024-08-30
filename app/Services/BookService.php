<?php

namespace App\Services;

use App\Models\Book;

class BookService
{
    /**
     *
     * @param $data an array containing the data required to create a new book.
     * @return Book: The function returns an instance of the newly created Book model containing the saved book’s details.
     */
    public function createBook($data)
    {
        return Book::create($data);
    }
    //----------------------------------------------------------------------------------------
    /**
     * @param Book $book: The instance of the Book model that is being updated.
     * @param array $data: An associative array containing the book data to be updated. Possible keys include:
     */
    public function updateBook(Book $book, $data)
    {
        $book->update($data);
        return $book;
    }
    //----------------------------------------------------------------------------------------
    /**
     * @param Book $book: The instance of the Book model that is being updated.
     */
    public function deleteBook(Book $book)
    {
        $book->delete();
    }
}
