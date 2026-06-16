<template>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 p-4 md:p-8 font-sans" dir="rtl">
<div class="max-w-7xl mx-auto text-right">

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 bg-white/80 backdrop-blur-md p-6 rounded-2xl border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md">
        <div class="w-full md:w-auto text-right">
            <div class="flex items-center gap-2 justify-start">
                <span class="text-2xl animate-pulse">📚</span>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent">الخزانة الذكية لإدارة الكتب</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1 mr-8">أضف، عدّل، وتصفح قائمة الكتب والمؤلفين بكفاءة عالية</p>
        </div>
        <button @click="openModal(null)" class="w-full md:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 active:scale-95 transition-all duration-200 text-sm">
            <span>➕</span>
            <span>إضافة كتاب جديد</span>
        </button>
    </div>

    <!-- قائمة عرض الكتب (جدول احترافي متجاوب RTL) -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse" direction="rtl">
                <thead>
                <tr class="bg-slate-50/70 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                    <th class="p-4 pr-6 text-right">تفاصيل الكتاب</th>
                    <th class="p-4 text-right">التصنيف الأدبي</th>
                    <th class="p-4 text-right">السعر</th>
                    <th class="p-4 text-center">الإجراءات والتحكم</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                <tr v-for="book in books" :key="book.id" class="hover:bg-slate-50/50 transition-all duration-200 group">
                    <!-- تفاصيل الكتاب (الغلاف، العنوان، المؤلف) -->
                    <td class="p-4 pr-6 text-right">
                        <div class="flex items-center gap-4 justify-start">
                            <div class="w-12 h-16 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200 overflow-hidden shadow-sm group-hover:scale-105 transition-all duration-300">
                                <img v-if="book['caver-image']" :src="book['caver-image']" class="w-full h-full object-cover">
                                <span v-else class="text-lg text-slate-400">📖</span>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-slate-800 text-base group-hover:text-blue-600 transition-colors">{{ book.title }}</div>
                                <div class="text-xs text-slate-400 mt-1 flex items-center gap-1 justify-start">
                                    <span>✍️</span> {{ book.author }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- التصنيف -->
                    <td class="p-4 text-right">
                <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full border border-blue-100/50">
                  📂 {{ book.category?.name || 'غير مصنف' }}
                </span>
                    </td>

                    <!-- السعر -->
                    <td class="p-4 text-right">
                        <span class="font-bold text-slate-700 text-base">${{ book.price }}</span>
                    </td>

                    <!-- الإجراءات -->
                    <td class="p-4 text-center">
                        <div class="flex justify-center items-center gap-2">
                            <button @click="openModal(book)" class="p-2.5 text-blue-600 hover:bg-blue-50 rounded-xl border border-transparent hover:border-blue-100 transition-all duration-200" title="تعديل">
                                ⚙️
                            </button>
                            <button @click="confirmDelete(book.id)" class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl border border-transparent hover:border-red-100 transition-all duration-200" title="حذف">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- حالة عدم وجود بيانات -->
                <tr v-if="books.length === 0">
                    <td colspan="4" class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-2 text-slate-400">
                            <span class="text-4xl">🏜️</span>
                            <p class="text-sm font-medium">مكتبتك فارغة حالياً، ابدأ بإضافة كتابك الأول!</p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- النافذة المنبثقة (Pop-up Modal) للإضافة والتعديل موجهة لليمين -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in" dir="rtl">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 transform scale-100 transition-all duration-300 overflow-hidden relative text-right">

            <!-- هيدر المودال -->
            <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 justify-start">
                    <span>{{ isEditing ? '📝 تعديل بيانات الكتاب' : '✨ إضافة كتاب جديد للمكتبة' }}</span>
                </h3>
                <button @click="closeModal" class="text-slate-400 hover:text-slate-600 p-1 hover:bg-slate-100 rounded-lg transition-colors">✖️</button>
            </div>

            <!-- استمارة إدخال البيانات -->
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- عنوان الكتاب -->
                <div class="text-right">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">عنوان الكتاب *</label>
                    <input v-model="form.title" type="text" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm transition-all duration-200 text-right" placeholder="أدخل اسم الكتاب بدقة..." required>
                    <div v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- اسم المؤلف -->
                    <div class="text-right">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">اسم المؤلف *</label>
                        <input v-model="form.author" type="text" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm transition-all duration-200 text-right" placeholder="اسم الكاتب..." required>
                        <div v-if="form.errors.author" class="text-xs text-red-500 mt-1">{{ form.errors.author }}</div>
                    </div>
                    <!-- السعر -->
                    <div class="text-right">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">السعر ($) *</label>
                        <input v-model="form.price" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm transition-all duration-200 text-right" placeholder="0.00" required>
                        <div v-if="form.errors.price" class="text-xs text-red-500 mt-1">{{ form.errors.price }}</div>
                    </div>
                </div>

                <!-- قائمة الاختيار للتصنيفات الأدبية -->
                <div class="text-right">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">التصنيف الأدبي *</label>
                    <select v-model="form.category_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm cursor-pointer text-right" required>
                        <option value="" disabled>اختر التصنيف من القائمة...</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <div v-if="form.errors.category_id" class="text-xs text-red-500 mt-1">{{ form.errors.category_id }}</div>
                </div>

                <!-- منطقة رفع الملف والمعاينة الفورية للأغلفة -->
                <div class="text-right">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">صورة غلاف الكتاب</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full min-h-[110px] border-2 border-slate-200 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100/50 transition-all duration-200 overflow-hidden p-2">

                            <!-- في حال وجود صورة سيتم عرضها فوراً للمعاينة -->
                            <div v-if="imagePreview" class="w-full flex flex-col items-center justify-center gap-2 animate-fade-in">
                                <img :src="imagePreview" class="h-20 w-14 object-cover rounded-lg shadow-md border border-slate-200">
                                <p class="text-[11px] text-blue-600 font-semibold">تغيير الصورة المحددة</p>
                            </div>

                            <!-- المحتوى التفاعلي في حال عدم وجود صورة مسبقة للمعاينة -->
                            <div v-else class="flex flex-col items-center justify-center pt-2 pb-3">
                                <span class="text-xl mb-1">🖼️</span>
                                <p class="text-xs text-slate-500"><span class="font-semibold text-blue-600">اضغط هنا</span> لرفع صورة الغلاف</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, JPEG (حد أقصى 2MB)</p>
                            </div>

                            <input type="file" class="hidden" @change="handleImageUpload" accept="image/*">
                        </label>
                    </div>
                    <div v-if="form.errors['caver-image']" class="text-xs text-red-500 mt-1">{{ form.errors['caver-image'] }}</div>
                </div>

                <!-- أزرار الإجراءات داخل المودال مصفوفة لليسار لدعم تناسق الـ RTL -->
                <div class="flex gap-2 justify-end pt-4 border-t border-slate-100 mt-6">
                    <button type="button" @click="closeModal" class="px-4 py-2.5 text-slate-500 hover:bg-slate-100 rounded-xl text-sm font-medium transition-colors">إلغاء</button>
                    <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-medium shadow-md shadow-blue-500/10 transition-all duration-200 disabled:opacity-50">
                        {{ form.processing ? 'جاري الحفظ...' : 'حفظ البيانات' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
</template>

    <script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

// 1. استقبال البيانات الممررة من الـ Controller لارايفل
const props = defineProps({
    books: Array,
    categories: Array
});

// 2. متغيرات التحكم في حالة النافذة والـ Modals
const isModalOpen = ref(false);
const isEditing = ref(false);
const currentBookId = ref(null);

// 3. متغير لتوليد رابط المعاينة الفورية للأغلفة قبل الرفع أو جلب الصورة القديمة
const imagePreview = ref(null);

// 4. تهيئة نموذج البيانات الأساسي باستخدام Inertia
const form = useForm({
    title: '',
    author: '',
    price: '',
    category_id: '',
    'caver-image': null,
});

// 5. دالة قراءة وتوليد رابط معالجة ملف الصورة محلياً للمعاينة الحية
const handleImageUpload = (event) => {
    const file = event.target.files[0]; // قراءة أول ملف محدد بدقة
    if (file) {
        form['caver-image'] = file;
        imagePreview.value = URL.createObjectURL(file); // إنشاء رابط وهمي مؤقت للمتصفح لعرض الصورة فوراً
    }
};

// 6. دالة فتح نافذة الإضافة أو التعديل
const openModal = (book = null) => {
    form.clearErrors();
    if (book) {
        isEditing.value = true;
        currentBookId.value = book.id;
        form.title = book.title;
        form.author = book.author;
        form.price = book.price;
        form.category_id = book.category_id;

        // التعديل: عرض الصورة المخزنة حالياً في السيرفر داخل المودال، وترك حقل الرفع فارغاً
        imagePreview.value = book['caver-image'] ? book['caver-image'] : null;
        form['caver-image'] = null;
    } else {
        isEditing.value = false;
        currentBookId.value = null;
        imagePreview.value = null; // تصفير أي معاينة سابقة عند الإضافة الجديدة
        form.reset();
    }
    isModalOpen.value = true;
};

// 7. إغلاق النافذة وتنظيف النموذج
const closeModal = () => {
    isModalOpen.value = false;
    imagePreview.value = null;
    form.reset();
};

// 8. حفظ أو تحديث البيانات عبر المسارات المباشرة لحل مشكلة route
const submitForm = () => {
    if (isEditing.value) {
        // إرسال طلب التعديل (POST ومحاكاة البيانات الإجبارية لرفع الملفات)
        form.post(`/books/${currentBookId.value}`, {
            forceFormData: true,
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'رائع!',
                    text: 'تم تحديث بيانات الكتاب المختار بنجاح.',
                    icon: 'success',
                    confirmButtonText: 'حسناً'
                });
            }
        });
    } else {
        // إرسال طلب إضافة كتاب جديد
        form.post('/books', {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'تم بنجاح!',
                    text: 'تم إضافة الكتاب الجديد إلى مكاتبك الواعدة بنجاح.',
                    icon: 'success',
                    confirmButtonText: 'موافق'
                });
            }
        });
    }
};

// 9. تأكيد عملية الحذف الاحترافية المباشرة بواسطة SweetAlert2
const confirmDelete = (id) => {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "لن تتمكن من استعادة هذا الكتاب وصورته بعد الحذف!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'نعم، احذفه!',
        cancelButtonText: 'إلغاء التراجع'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/books/${id}`, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'تم الحذف!',
                        text: 'تمت إزالة الكتاب من السجلات بنجاح.',
                        icon: 'success',
                        confirmButtonText: 'تم'
                    });
                }
            });
        }
    });
};
</script>
