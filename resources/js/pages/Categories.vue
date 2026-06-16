<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50/30 p-4 md:p-8 font-sans" dir="rtl">
        <div class="max-w-7xl mx-auto">

            <!-- الهيدر وبطاقة التحكم -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 bg-white/80 backdrop-blur-md p-6 rounded-2xl border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl animate-pulse">📂</span>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent">إدارة أقسام وتصنيفات المكتبة</h1>
                    </div>
                    <p class="text-sm text-slate-500 mt-1 mr-8">نظم وأضف الأقسام الأدبية والعلمية لتوزيع كتبك عليها بسلاسة</p>
                </div>
                <button @click="openModal(null)" class="w-full md:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 active:scale-95 transition-all duration-200 text-sm">
                    <span>➕</span>
                    <span>إضافة تصنيف جديد</span>
                </button>
            </div>

            <!-- شبكة عرض التصنيفات (Grid Layout) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="category in categories" :key="category.id" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">

                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold text-lg group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                                📚
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base group-hover:text-emerald-600 transition-colors">{{ category.name }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">تاريخ الإضافة: {{ new Date(category.created_at).toLocaleDateString('ar-EG') }}</p>
                            </div>
                        </div>
                        <span class="bg-slate-100 text-slate-600 text-xs px-2.5 py-1 rounded-full font-bold">
              {{ category.books_count }} كتاب
            </span>
                    </div>

                    <!-- أزرار التحكم بالتصنيف -->
                    <div class="flex justify-end gap-2 border-t border-slate-50 pt-3 mt-2">
                        <button @click="openModal(category)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 text-sm" title="تعديل">
                            ⚙️ تعديل
                        </button>
                        <button @click="confirmDelete(category)" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-all duration-200 text-sm" title="حذف">
                            🗑️ حذف
                        </button>
                    </div>

                </div>

                <!-- حالة عدم وجود تصنيفات مضافة -->
                <div v-if="categories.length === 0" class="col-span-full bg-white rounded-2xl p-12 text-center border border-slate-100">
                    <div class="flex flex-col items-center justify-center gap-2 text-slate-400">
                        <span class="text-4xl">🏜️</span>
                        <p class="text-sm font-medium">لا توجد أي تصنيفات مضافة حالياً. ابدأ بإضافة أول قسم لمكتبتك!</p>
                    </div>
                </div>
            </div>

            <!-- النافذة المنبثقة (Pop-up Modal) للإضافة والتعديل -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform scale-100 transition-all duration-300 overflow-hidden relative">

                    <!-- هيدر المودال -->
                    <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800">
                            {{ isEditing ? '📝 تعديل اسم التصنيف' : '✨ إضافة تصنيف أدبي جديد' }}
                        </h3>
                        <button @click="closeModal" class="text-slate-400 hover:text-slate-600 p-1 hover:bg-slate-100 rounded-lg transition-colors">✖️</button>
                    </div>

                    <!-- استمارة الإدخال -->
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">اسم القسم أو التصنيف *</label>
                            <input v-model="form.name" type="text" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none text-sm transition-all duration-200" placeholder="مثال: روايات، تاريخ، علوم حاسوب..." required>
                            <div v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</div>
                        </div>

                        <!-- أزرار التحكم -->
                        <div class="flex gap-2 justify-end pt-4 border-t border-slate-100 mt-6">
                            <button type="button" @click="closeModal" class="px-4 py-2.5 text-slate-500 hover:bg-slate-100 rounded-xl text-sm font-medium transition-colors">إلغاء</button>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl text-sm font-medium transition-all duration-200">
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

// استقبال التصنيفات الممررة من الـ CategoryController
const props = defineProps({
    categories: Array
});

// متغيرات التحكم بالنوافذ المنبثقة
const isModalOpen = ref(false);
const isEditing = ref(false);
const currentCategoryId = ref(null);

// مصفوفة البيانات مع Inertia form لتمرير العمليات للخلفية
const form = useForm({
    name: ''
});

// فتح نافذة الإضافة أو التعديل
const openModal = (category = null) => {
    form.clearErrors();
    if (category) {
        isEditing.value = true;
        currentCategoryId.value = category.id;
        form.name = category.name;
    } else {
        isEditing.value = false;
        currentCategoryId.value = null;
        form.reset();
    }
    isModalOpen.value = true;
};

// إغلاق النافذة وتنظيف النموذج
const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

// معالجة وحفظ البيانات عبر طلبات الـ Inertia
const submitForm = () => {
    if (isEditing.value) {
        // استبدال دالة route بالمسار النصي المباشر للتعديل
        form.put(`/categories/${currentCategoryId.value}`, {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'تم التحديث!',
                    text: 'تم تعديل اسم التصنيف بنجاح واحترافية.',
                    icon: 'success',
                    confirmButtonText: 'حسناً'
                });
            }
        });
    } else {
        // استبدال دالة route بالمسار النصي المباشر للإضافة
        form.post('/categories', {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'عمل رائع!',
                    text: 'تم إنشاء التصنيف الجديد وإدراجه بنجاح.',
                    icon: 'success',
                    confirmButtonText: 'موافق'
                });
            }
        });
    }
};

// عملية الحذف الذكية مع التحذير المسبق باستخدام SweetAlert2
const confirmDelete = (category) => {
    // التحقق قبل إرسال طلب الحذف إذا كان القسم يحتوي على كتب (لمنع مشاكل الـ Database)
    if (category.books_count > 0) {
        Swal.fire({
            title: 'عذراً، لا يمكن الحذف!',
            text: `هذا التصنيف يحتوي على (${category.books_count}) من الكتب المرتبطة به حالياً. قم بنقل الكتب أو حذفها أولاً.`,
            icon: 'error',
            confirmButtonText: 'فهمت ذلك',
            confirmButtonColor: '#059669'
        });
        return;
    }

    Swal.fire({
        title: 'هل أنت متأكد من الحذف؟',
        text: "سيتم إزالة هذا التصنيف نهائياً من سجلات النظام.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'نعم، قم بالحذف',
        cancelButtonText: 'تراجع'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('categories.destroy', category.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'تم الحذف!',
                        text: 'تمت إزالة القسم بنجاح تام.',
                        icon: 'success',
                        confirmButtonText: 'موافق'
                    });
                }
            });
        }
    });
};
</script>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
}
</style>
