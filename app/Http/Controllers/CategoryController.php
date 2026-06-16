<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController
{
    public function index()
    {
        $categories = Category::withCount('books')->latest()->get();

        return inertia('Categories', [
            'categories' => $categories,
        ]);
    }

    // إضافة تصنيف جديد
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($data);

        // في Inertia نعود للخلف مباشرة والبيانات تتحدث تلقائياً في الفيو
        return redirect()->back()->with('success', 'تم إضافة التصنيف بنجاح');
    }

    // تعديل تصنيف موجود
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
        ]);

        $category->update($data);

        return redirect()->back()->with('success', 'تم تحديث التصنيف بنجاح');
    }

    // حذف تصنيف
    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف التصنيف لأنه يحتوي على كتب مرتبطة!');
        }

        $category->delete();

        return redirect()->back()->with('success', 'تم حذف التصنيف بنجاح');
    }
}
