<!-- resources/js/Pages/ContactMessages.vue -->
<template>
    <div class="p-6 max-w-6xl mx-auto text-right min-h-screen bg-slate-50/50" dir="rtl">

        <!-- الهيدر والإحصائيات -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">إدارة الرسائل الواردة</h1>
                <p class="text-sm text-slate-500 mt-1">استعرض تفاصيل رسائل واستفسارات العملاء بدقة.</p>
            </div>

            <div class="bg-white border border-slate-200 px-4 py-2 rounded-xl shadow-sm flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-sm font-medium text-slate-700">إجمالي الرسائل: {{ messages.length }}</span>
            </div>
        </div>

        <!-- شريط البحث السريع -->
        <div class="mb-6">
            <div class="relative max-w-md">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="البحث باسم المرسل، البريد، أو المحتوى..."
                    class="w-full pr-10 pl-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent transition-all"
                />
                <span class="absolute right-3.5 top-3 text-slate-400 text-sm">🔍</span>
            </div>
        </div>

        <!-- حالة عدم وجود رسائل -->
        <div v-if="filteredMessages.length === 0" class="bg-white border border-dashed border-slate-300 rounded-2xl p-16 text-center">
            <span class="text-4xl block mb-3">📥</span>
            <h3 class="text-lg font-semibold text-slate-800">لا توجد نتائج مطابقة</h3>
            <p class="text-sm text-slate-400 mt-1">يرجى التأكد من كلمة البحث أو انتظار رسائل جديدة.</p>
        </div>

        <!-- جدول / قائمة الرسائل الاحترافية -->
        <div v-else class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm font-semibold">
                        <th class="p-4">المرسل</th>
                        <th class="p-4 hidden md:table-cell">البريد الإلكتروني</th>
                        <th class="p-4 hidden sm:table-cell">التاريخ</th>
                        <th class="p-4 text-center">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    <tr v-for="msg in filteredMessages" :key="msg.id" class="hover:bg-slate-50/80 transition-colors">
                        <!-- معلومات المرسل -->
                        <td class="p-4">
                            <div class="font-semibold text-slate-900">{{ msg.name }}</div>
                            <div class="text-xs text-slate-400 mt-0.5 sm:hidden">{{ msg.email }}</div>
                        </td>
                        <!-- البريد الإلكتروني (شاشات كبيرة) -->
                        <td class="p-4 hidden md:table-cell text-slate-500 font-mono">{{ msg.email }}</td>
                        <!-- التاريخ -->
                        <td class="p-4 hidden sm:table-cell text-slate-500">{{ formatDate(msg.created_at) }}</td>
                        <!-- زر العرض المخصص -->
                        <td class="p-4 text-center">
                            <button
                                @click="openMessageModal(msg)"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 text-xs font-medium rounded-lg transition-all duration-200"
                            >
                                <span>عرض الرسالة</span>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- النافذة المنبثقة الاحترافية لعرض تفاصيل الرسالة (Modal) -->
        <div
            v-if="isModalOpen && selectedMessage"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-fade-in"
            @click.self="closeMessageModal"
        >
            <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all scale-100 animate-slide-up">

                <!-- هيدر المودال -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 text-lg">تفاصيل رسالة العميل</h3>
                    <button @click="closeMessageModal" class="text-slate-400 hover:text-slate-600 text-lg p-1">✕</button>
                </div>

                <!-- محتوى المودال والتصميم المخصص للرسالة -->
                <div class="p-6 space-y-5">
                    <!-- تفاصيل الاتصال السريعة -->
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs">
                        <div>
                            <span class="block text-slate-400 font-medium mb-0.5">اسم المرسل</span>
                            <span class="font-semibold text-slate-800 text-sm">{{ selectedMessage.name }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-medium mb-0.5">تاريخ الإرسال</span>
                            <span class="font-medium text-slate-700">{{ formatDate(selectedMessage.created_at) }}</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <span class="block text-slate-400 font-medium mb-0.5">البريد الإلكتروني</span>
                            <span class="font-mono text-slate-700 text-sm select-all">{{ selectedMessage.email }}</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <span class="block text-slate-400 font-medium mb-0.5">رقم الهاتف</span>
                            <span class="font-mono text-slate-700 text-sm select-all" dir="ltr">{{ selectedMessage.phone }}</span>
                        </div>
                    </div>

                    <!-- الصندوق المخصص لعرض نص الرسالة -->
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 mb-2">محتوى الرسالة:</span>
                        <div class="bg-slate-900 text-slate-100 p-5 rounded-xl text-sm leading-relaxed whitespace-pre-line font-sans max-h-60 overflow-y-auto border border-slate-800 shadow-inner">
                            {{ selectedMessage.message }}
                        </div>
                    </div>
                </div>

                <!-- فوتر المودال (أزرار التفاعل السريع) -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-wrap justify-end gap-2">
                    <a
                        :href="`mailto:${selectedMessage.email}`"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all"
                    >
                        رد عبر البريد
                    </a>
                    <button
                        @click="closeMessageModal"
                        class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-lg transition-all"
                    >
                        إغلاق نافذة العرض
                    </button>
                </div>

            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

interface Message {
    id: number;
    name: string;
    email: string;
    phone: string;
    message: string;
    created_at: string;
}

const props = defineProps<{
    messages: Message[]
}>();

// نظام البحث والتصفية المباشر
const searchQuery = ref('');
const filteredMessages = computed(() => {
    return props.messages.filter(msg => {
        const term = searchQuery.value.toLowerCase();
        return msg.name.toLowerCase().includes(term) ||
            msg.email.toLowerCase().includes(term) ||
            msg.message.toLowerCase().includes(term);
    });
});

// التحكم في حالة النافذة المنبثقة والرسالة المختارة
const isModalOpen = ref(false);
const selectedMessage = ref<Message | null>(null);

const openMessageModal = (message: Message) => {
    selectedMessage.value = message;
    isModalOpen.value = true;
};

const closeMessageModal = () => {
    isModalOpen.value = false;
    selectedMessage.value = null;
};

// دالة تنسيق الوقت والتاريخ
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
}
.animate-slide-up {
    animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(12px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
