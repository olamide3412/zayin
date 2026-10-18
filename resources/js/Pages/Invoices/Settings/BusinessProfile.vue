<template>
    <Head title="Invoice Business Profile & Accounts - Zayin Global Limited" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">Business Profile & Bank Accounts</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Configure company branding, RC/TIN tax identifiers, and payment accounts.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('invoices.settings.templates')"
                        class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm"
                    >
                        Templates Configuration →
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Company Details Form (7 cols) -->
                <div class="lg:col-span-7 bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                        Company Details & Tax Identifiers
                    </h3>

                    <form @submit.prevent="updateProfile" class="space-y-4 text-xs">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Company Legal Name *</label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Tagline</label>
                                <input
                                    type="text"
                                    v-model="form.tagline"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">CAC Registration (RC Number)</label>
                                <input
                                    type="text"
                                    v-model="form.rc_number"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Tax Identification Number (TIN)</label>
                                <input
                                    type="text"
                                    v-model="form.tin"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Primary Phone</label>
                                <input
                                    type="text"
                                    v-model="form.phone"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Alternate Phone</label>
                                <input
                                    type="text"
                                    v-model="form.phone_alt"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Billing Email</label>
                                <input
                                    type="email"
                                    v-model="form.email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Website URL</label>
                                <input
                                    type="text"
                                    v-model="form.website"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Address Line 1</label>
                            <input
                                type="text"
                                v-model="form.address_line1"
                                placeholder="Street address or plot number"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            />
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Address Line 2 (Optional)</label>
                            <input
                                type="text"
                                v-model="form.address_line2"
                                placeholder="Suite, unit, building, or floor"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">City</label>
                                <input
                                    type="text"
                                    v-model="form.city"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">State</label>
                                <input
                                    type="text"
                                    v-model="form.state"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                        </div>

                        <!-- VAT Defaults -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-gray-100">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Default VAT Rate (%)</label>
                                <input
                                    type="number"
                                    step="0.1"
                                    :value="form.vat_rate_bps / 100"
                                    @input="form.vat_rate_bps = Math.round(Number(($event.target as HTMLInputElement).value) * 100)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Default VAT Mode</label>
                                <select
                                    v-model="form.default_vat_mode"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                >
                                    <option value="exclusive">Exclusive (Added on top)</option>
                                    <option value="inclusive">Inclusive (Embedded in rate)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Default Notes</label>
                            <textarea
                                rows="2"
                                v-model="form.default_notes"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Default Terms & Conditions</label>
                            <textarea
                                rows="3"
                                v-model="form.default_terms"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">A5 Bottom Notice / Footer Text</label>
                            <input
                                type="text"
                                v-model="form.footer_text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            />
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-primary text-white font-bold rounded-lg hover:bg-primary/90 shadow-sm"
                            >
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Bank Accounts Management (5 cols) -->
                <div class="lg:col-span-5 bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-900">
                            Bank Accounts ({{ bankAccounts.length }})
                        </h3>
                        <button
                            type="button"
                            @click="openBankModal"
                            class="px-3 py-1.5 text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-md"
                        >
                            + Add Account
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="bank in bankAccounts"
                            :key="bank.id"
                            class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs space-y-1 relative"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900">{{ bank.bank_name }}</span>
                                <span v-if="bank.is_default" class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Default
                                </span>
                            </div>
                            <div class="font-mono text-sm font-black text-gray-800">{{ bank.account_number }}</div>
                            <div class="text-gray-500">{{ bank.account_name }}</div>

                            <div class="pt-2 flex justify-end space-x-2">
                                <button
                                    type="button"
                                    @click="editBank(bank)"
                                    class="text-blue-600 hover:underline"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    @click="deleteBank(bank.id)"
                                    class="text-rose-600 hover:underline"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add / Edit Bank Account Modal -->
        <div v-if="showBankModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">
                        {{ editingBankId ? 'Edit Bank Account' : 'Add Bank Account' }}
                    </h3>
                    <button type="button" @click="showBankModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Bank Name *</label>
                        <input
                            type="text"
                            v-model="bankForm.bank_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="Zenith Bank, GTBank, Moniepoint..."
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Account Name *</label>
                        <input
                            type="text"
                            v-model="bankForm.account_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="ZAYIN GLOBAL LTD"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Account Number (NUBAN) *</label>
                        <input
                            type="text"
                            v-model="bankForm.account_number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary font-mono font-bold"
                            placeholder="0123456789"
                        />
                    </div>
                    <div>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="bankForm.is_default"
                                class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                            />
                            <span class="font-semibold text-gray-700">Set as primary default account</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showBankModal = false"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="saveBank"
                        class="px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg shadow-sm"
                    >
                        Save Account
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps<{
    business: any;
    bankAccounts: any[];
}>();

const form = ref({
    name: props.business.name || '',
    tagline: props.business.tagline || '',
    rc_number: props.business.rc_number || '',
    tin: props.business.tin || '',
    phone: props.business.phone || '',
    phone_alt: props.business.phone_alt || '',
    email: props.business.email || '',
    website: props.business.website || '',
    address_line1: props.business.address_line1 || '',
    address_line2: props.business.address_line2 || '',
    city: props.business.city || '',
    state: props.business.state || '',
    vat_rate_bps: props.business.vat_rate_bps || 750,
    default_vat_mode: props.business.default_vat_mode || 'exclusive',
    default_notes: props.business.default_notes || '',
    default_terms: props.business.default_terms || '',
    footer_text: props.business.footer_text || '',
});

function updateProfile() {
    router.put(route('invoices.settings.business.update'), form.value);
}

const showBankModal = ref(false);
const editingBankId = ref<number | null>(null);
const bankForm = ref({
    bank_name: '',
    account_name: '',
    account_number: '',
    is_default: false,
});

function openBankModal() {
    editingBankId.value = null;
    bankForm.value = {
        bank_name: '',
        account_name: form.value.name,
        account_number: '',
        is_default: props.bankAccounts.length === 0,
    };
    showBankModal.value = true;
}

function editBank(bank: any) {
    editingBankId.value = bank.id;
    bankForm.value = {
        bank_name: bank.bank_name,
        account_name: bank.account_name,
        account_number: bank.account_number,
        is_default: bank.is_default,
    };
    showBankModal.value = true;
}

function saveBank() {
    if (!bankForm.value.bank_name || !bankForm.value.account_number) {
        alert('Please fill bank name and account number.');
        return;
    }

    if (editingBankId.value) {
        router.put(route('invoices.settings.bank-accounts.update', editingBankId.value), bankForm.value, {
            onSuccess: () => {
                showBankModal.value = false;
            },
        });
    } else {
        router.post(route('invoices.settings.bank-accounts.store'), bankForm.value, {
            onSuccess: () => {
                showBankModal.value = false;
            },
        });
    }
}

function deleteBank(id: number) {
    if (confirm('Are you sure you want to remove this bank account?')) {
        router.delete(route('invoices.settings.bank-accounts.destroy', id));
    }
}
</script>
