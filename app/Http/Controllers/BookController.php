<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController
{
    // عرض صفحة الكتب وتمرير البيانات عبر Inertia
    public function index()
    {
        $books = Book::with('category')->latest()->get();

        $categories = Category::select('id', 'name')->get();

        return inertia('Books', [
            'books' => $books,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'caver-image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // التحقق من الصورة
        ]);
        $data = [
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
        ];

        if ($request->hasFile('caver-image')) {
            $file = $request->file('caver-image');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/books');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['caver-image'] = '/uploads/books/'.$filename;
        }

        Book::create($data);

        return redirect()->back()->with('success', 'تم إضافة الكتاب بنجاح');
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'caver-image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
        ];

        if ($request->hasFile('caver-image')) {
            if ($book['caver-image'] && file_exists(public_path($book['caver-image']))) {
                @unlink(public_path($book['caver-image']));
            }

            $file = $request->file('caver-image');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/books');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['caver-image'] = '/uploads/books/'.$filename;
        }

        $book->update($data);

        return redirect()->back()->with('success', 'تم تحديث بيانات الكتاب بنجاح');
    }

    public function destroy(Book $book)
    {
        if ($book['caver-image'] && file_exists(public_path($book['caver-image']))) {
            @unlink(public_path($book['caver-image']));
        }

        $book->delete();

        return redirect()->back()->with('success', 'تم حذف الكتاب بنجاح');
    }
}
