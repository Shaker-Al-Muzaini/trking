<template>
    <div v-if="isMounted" class="p-6 bg-gray-50 min-h-screen" dir="rtl">

        <!-- رأس الصفحة: العناوين وزر الإضافة -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-xl font-bold text-gray-800">إدارة العقارات</h1>
                <p class="text-xs text-gray-500 mt-1">عرض، إضافة، ومتابعة العقارات المتاحة في النظام</p>
            </div>
            <button @click="openCreateModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition shadow-sm flex items-center gap-2 group">
                <svg class="w-4 h-4 transform group-hover:rotate-90 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>إضافة عقار جديد</span>
            </button>
        </div>

        <!-- قسم عرض العقارات التفاعلي والمقاوم للتمدد -->
        <div class="max-w-[1400px] mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                <div v-for="item in propertiesList" :key="item.id"
                     class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition duration-300 flex flex-col h-full">

                    <div class="relative aspect-[16/10] w-full bg-gray-50 overflow-hidden border-b border-gray-50">
                        <img :src="item.image || 'https://unsplash.com'"
                             alt="Property Image" class="w-full h-full object-cover" />
                        <span class="absolute top-2.5 right-2.5 bg-blue-600/90 backdrop-blur-xs text-white text-[11px] font-bold px-2 py-0.5 rounded-md shadow-sm">
                            ${{ Number(item.price).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                        </span>
                    </div>

                    <div class="p-3.5 flex-grow flex flex-col justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm line-clamp-1 mb-1" :title="item.title">{{ item.title }}</h3>
                            <p class="text-gray-500 text-[11px] line-clamp-2 leading-relaxed">{{ item.des || 'لا يوجد وصف متاح.' }}</p>
                        </div>

                        <div class="pt-2.5 border-t border-gray-50 flex justify-between items-center text-[11px] text-gray-400">
                            <span class="flex items-center gap-1">📍 موقع افتراضي</span>
                            <span @click="openEditModal(item)" class="text-blue-500 hover:text-blue-600 font-medium cursor-pointer transition flex items-center gap-0.5">
                                التفاصيل
                                <svg class="w-3 h-3 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1️⃣ نافذة بوب أب (Modal) لإضافة عقار جديد -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-40 backdrop-blur-sm transition-opacity duration-300">
            <div class="relative w-full max-w-4xl bg-white rounded-xl shadow-2xl border border-gray-100 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                    <h3 class="text-base font-bold text-gray-800">إضافة عقار جديد</h3>
                    <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="submitCreateForm" class="p-6 flex flex-col overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
                        <div class="flex flex-col space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">عنوان العقار</label>
                                <input v-model="createForm.title" type="text" placeholder="مثال: شقة فاخرة للبيع" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm" :class="createForm.errors.title ? 'border-red-500 bg-red-50' : 'border-gray-300'">
                                <span v-if="createForm.errors.title" class="text-xs text-red-500 mt-1 block">{{ createForm.errors.title }}</span>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">السعر ($)</label>
                                <input v-model="createForm.price" type="number" placeholder="0.00" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm" :class="createForm.errors.price ? 'border-red-500 bg-red-50' : 'border-gray-300'">
                                <span v-if="createForm.errors.price" class="text-xs text-red-500 mt-1 block">{{ createForm.errors.price }}</span>
                            </div>
                            <div class="flex-grow flex flex-col">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">وصف العقار</label>
                                <textarea v-model="createForm.des" placeholder="اكتب تفاصيل العقار هنا..." class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm resize-none flex-grow min-h-[120px]" :class="createForm.errors.des ? 'border-red-500 bg-red-50' : 'border-gray-300'"></textarea>
                                <span v-if="createForm.errors.des" class="text-xs text-red-500 mt-1 block">{{ createForm.errors.des }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">صورة العقار</label>
                            <div class="flex items-center justify-center w-full flex-grow min-h-[220px] h-full">
                                <label class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition relative overflow-hidden" :class="createForm.errors.image ? 'border-red-500' : 'border-gray-300'">
                                    <input type="file" @change="handleCreateFileUpload" class="hidden" accept="image/*" />
                                    <div v-if="!createImagePreview" class="flex flex-col items-center justify-center py-6">
                                        <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="text-xs text-gray-500 font-medium">اضغط لرفع صورة أو اسحبها هنا</p>
                                    </div>
                                    <img v-else :src="createImagePreview" class="w-full h-full object-cover rounded-lg absolute inset-0" />
                                </label>
                            </div>
                            <span v-if="createForm.errors.image" class="text-xs text-red-500 mt-1 block">{{ createForm.errors.image }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t border-gray-100">
                        <button type="button" @click="closeCreateModal" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition min-w-[100px]">إلغاء</button>
                        <button type="submit" :disabled="createForm.processing" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition text-sm shadow-sm min-w-[140px]">
                            <span v-if="createForm.processing" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span>{{ createForm.processing ? 'جاري الحفظ...' : 'حفظ العقار' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- 2️⃣ نافذة بوب أب (Modal) لعرض تفاصيل العقار وتعديله -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-40 backdrop-blur-sm transition-opacity duration-300">
            <div class="relative w-full max-w-4xl bg-white rounded-xl shadow-2xl border border-gray-100 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                    <h3 class="text-base font-bold text-gray-800">تفاصيل وتعديل العقار</h3>
                    <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="submitEditForm" class="p-6 flex flex-col overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
                        <div class="flex flex-col space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">عنوان العقار</label>
                                <input v-model="editForm.title" type="text" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">السعر ($)</label>
                                <input v-model="editForm.price" type="number" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm border-gray-300">
                            </div>
                            <div class="flex-grow flex flex-col">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">وصف العقار</label>
                                <textarea v-model="editForm.des" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm resize-none flex-grow min-h-[120px] border-gray-300"></textarea>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">تعديل الصورة الحالية</label>
                            <div class="flex items-center justify-center w-full flex-grow min-h-[220px] h-full">
                                <label class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition relative overflow-hidden border-gray-300">
                                    <input type="file" @change="handleEditFileUpload" class="hidden" accept="image/*" />
                                    <img :src="editImagePreview || 'https://unsplash.com'" class="w-full h-full object-cover rounded-lg absolute inset-0" />
                                    <div class="absolute inset-0 bg-black bg-opacity-25 opacity-0 hover:opacity-100 transition flex items-center justify-center text-white text-xs font-medium">اضغط لتغيير الصورة</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t border-gray-100">
                        <button type="button" @click="closeEditModal" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition min-w-[100px]">إلغاء</button>
                        <button type="submit" :disabled="editForm.processing" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition text-sm shadow-sm min-w-[140px]">
                            <span v-if="editForm.processing" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span>{{ editForm.processing ? 'جاري التعديل...' : 'حفظ التعديلات' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>


<script setup>
import { ref, onMounted, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    properties: Array
});

const isMounted = ref(false);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const propertiesList = ref([]);
const currentPropertyId = ref(null);

onMounted(() => {
    isMounted.value = true;
    propertiesList.value = props.properties || [];
});

// مراقبة الـ props لمزامنة وتحديث شبكة الكروت فوراً عند نجاح العمليات
watch(() => props.properties, (newVal) => {
    propertiesList.value = newVal || [];
}, { deep: true });

// نماذج منفصلة تماماً لكل عملية لمنع تداخل أو تشوه البيانات أثناء التبديل
const createForm = useForm({
    title: '',
    price: '',
    des: '',
    image: null
});

const editForm = useForm({
    title: '',
    price: '',
    des: '',
    image: null,
    _method: 'PUT' // محاكاة طلب PUT لتجاوز مشاكل FormData والملفات في Laravel
});

const createImagePreview = ref(null);
const editImagePreview = ref(null);
// دوال إدارة مودال الإضافة
const openCreateModal = () => {
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    createForm.reset();
    createImagePreview.value = null;
};

// دوال إدارة مودال التفاصيل والتعديل وتعبئة القيم الحالية للعقار المختار
const openEditModal = (property) => {
    currentPropertyId.value = property.id;
    editForm.title = property.title;
    editForm.price = property.price;
    editForm.des = property.des;
    editImagePreview.value = property.image;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.reset();
    editImagePreview.value = null;
    currentPropertyId.value = null;
};

// معالجة قراءة وتحميل ورفع الصور للمودالين
const handleCreateFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        createForm.image = file;
        createImagePreview.value = URL.createObjectURL(file);
    }
};

const handleEditFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        editForm.image = file;
        editImagePreview.value = URL.createObjectURL(file);
    }
};

// إرسال طلب إضافة عقار جديد
const submitCreateForm = () => {
    createForm.processing = true;
    createForm.post('/property', {
        forceFormData: true,
        onSuccess: () => {
            closeCreateModal();
            showToast('success', 'تم حفظ العقار بنجاح!');
        },
        onError: (errors) => {
            createForm.setError(errors);
            createForm.processing = false;
            showErrAlert();
        }
    });
};

// إرسال طلب تحديث العقار الحالي المتوافق مع مسار الـ Update في الـ Controller
// إرسال طلب التعديل بشكل صحيح ومضمون لرفع الصور في لارافل
const submitEditForm = () => {
    editForm.processing = true;

    // تجهيز البيانات يدوياً بداخل FormData لضمان قراءة ملف الصورة الجديد
    const formData = new FormData();
    formData.append('title', editForm.title);
    formData.append('price', editForm.price);
    formData.append('des', editForm.des);
    formData.append('_method', 'PUT'); // السطر السحري الذي يفهمه لارافل مع الـ FormData

    // إذا قام المستخدم باختيار صورة جديدة نقوم بإرفاقها، وإلا نتركها فارغة
    if (editForm.image) {
        formData.append('image', editForm.image);
    }

    // إرسال الطلب عبر دالة router.post الصريحة إلى نفس المسار
    router.post(`/property/${currentPropertyId.value}`, formData, {
        forceFormData: true,
        onSuccess: () => {
            closeEditModal();
            showToast('success', 'تم تحديث بيانات العقار بنجاح!');
        },
        onError: (errors) => {
            editForm.setError(errors);
            editForm.processing = false;
            showErrAlert();
        },
        onFinish: () => {
            editForm.processing = false;
        }
    });
};


// توابع التنبيهات الموحدة للـ SweetAlert2 الاحترافي
const showToast = (icon, title) => {
    Swal.fire({ icon, title, timer: 2500, timerProgressBar: true, showConfirmButton: false, position: 'top-start', toast: true });
};

const showErrAlert = () => {
    Swal.fire({ icon: 'error', title: 'عذراً، حدث خطأ!', text: 'يرجى مراجعة الحقول الحمراء والتحقق من البيانات المكتوبة.', confirmButtonText: 'حسناً', confirmButtonColor: '#3b82f6' });
};
</script>

<style>
.swal2-popup {
    font-family: inherit !important;
}
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
