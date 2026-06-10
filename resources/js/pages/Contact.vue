<template>
    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-6 text-right" dir="rtl">
        <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl overflow-hidden grid md:grid-cols-5 animate-fade-in">

            <!-- قسم معلومات الاتصال الجانبي -->
            <div class="md:col-span-2 bg-gradient-to-br from-blue-600 to-indigo-700 p-8 text-white flex flex-col justify-between">
                <div>
                    <h2 class="text-3xl font-bold mb-4">تواصل معنا</h2>
                    <p class="text-blue-100 leading-relaxed mb-8">يسعدنا الإجابة على استفساراتك. يرجى ملء النموذج وسيتم الرد عليك خلال 24 ساعة.</p>

                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <span class="p-3 bg-white/10 rounded-lg">📍</span>
                            <div>
                                <p class="text-xs text-blue-200">الموقع</p>
                                <p class="font-medium">الرياض، المملكة العربية السعودية</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="p-3 bg-white/10 rounded-lg">📞</span>
                            <div>
                                <p class="text-xs text-blue-200">رقم الهاتف</p>
                                <p class="font-medium" dir="ltr">+966 50 000 0000</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="p-3 bg-white/10 rounded-lg">✉️</span>
                            <div>
                                <p class="text-xs text-blue-200">البريد الإلكتروني</p>
                                <p class="font-medium">support@example.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-blue-200 mt-8">جميع الحقوق محفوظة © 2026</div>
            </div>

            <!-- قسم نموذج الاتصال -->
            <div class="md:col-span-3 p-8 md:p-12">
                <h3 class="text-2xl font-bold text-slate-800 mb-6">أرسل رسالة</h3>

                <!-- رسالة النجاح -->
                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 font-medium">
                    {{ $page.props.flash.success }}
                </div>

                <form @submit.prevent="submitForm" class="space-y-5">
                    <!-- حقل الاسم -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">الاسم الكامل</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="جون دو"
                            :class="{'border-red-500 ring-1 ring-red-500': form.errors.name}"
                        />
                        <span v-if="form.errors.name" class="text-xs text-red-500 mt-1 block">{{ form.errors.name }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- حقل البريد الإلكتروني -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">البريد الإلكتروني</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="name@example.com"
                                :class="{'border-red-500 ring-1 ring-red-500': form.errors.email}"
                            />
                            <span v-if="form.errors.email" class="text-xs text-red-500 mt-1 block">{{ form.errors.email }}</span>
                        </div>

                        <!-- حقل الهاتف -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">رقم الهاتف</label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="0500000000"
                                :class="{'border-red-500 ring-1 ring-red-500': form.errors.phone}"
                            />
                            <span v-if="form.errors.phone" class="text-xs text-red-500 mt-1 block">{{ form.errors.phone }}</span>
                        </div>
                    </div>

                    <!-- حقل الرسالة -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">نص الرسالة</label>
                        <textarea
                            v-model="form.text"
                            rows="4"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                            placeholder="اكتب رسالتك هنا..."
                            :class="{'border-red-500 ring-1 ring-red-500': form.errors.text}"
                        ></textarea>
                        <span v-if="form.errors.text" class="text-xs text-red-500 mt-1 block">{{ form.errors.text }}</span>
                    </div>

                    <!-- زر الإرسال -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-200 hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">جاري الإرسال...</span>
                        <span v-else>إرسال الرسالة</span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    text: '',
});

const submitForm = () => {
    form.post('/contact', {
        onSuccess: () => form.reset(),
    });
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
