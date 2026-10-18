<template>
    <div class="space-y-2.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-1.5 border-b border-gray-200">
            <label class="flex items-center space-x-2 cursor-pointer select-none">
                <input
                    type="checkbox"
                    :checked="isEnabled"
                    @change="toggleEnabled"
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                />
                <span class="font-bold text-xs text-gray-800">
                    Include Bank Account Details on Invoice <span class="text-[11px] text-gray-400 font-normal">(Optional)</span>
                </span>
            </label>

            <div class="flex items-center space-x-2 self-end sm:self-auto">
                <template v-if="isEnabled && bankAccounts.length > 0">
                    <button
                        type="button"
                        @click="selectAll"
                        class="text-[10px] text-gray-500 hover:text-primary font-medium"
                    >
                        Select All
                    </button>
                    <span class="text-gray-300 text-[10px]">|</span>
                    <button
                        type="button"
                        @click="clearAll"
                        class="text-[10px] text-gray-500 hover:text-rose-600 font-medium"
                    >
                        Omit All
                    </button>
                    <span class="text-gray-300 text-[10px]">|</span>
                </template>
                <button
                    type="button"
                    @click="openAddModal"
                    class="text-[11px] font-bold text-primary hover:text-primary/80 flex items-center transition-colors"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    + Add Bank
                </button>
            </div>
        </div>

        <!-- Bank Details Omitted / Turned Off Notice -->
        <div 
            v-if="!isEnabled" 
            class="p-3 bg-amber-50/70 border border-amber-200 rounded-lg text-xs text-amber-900 flex items-center justify-between"
        >
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><strong>No bank accounts on invoice:</strong> Payment bank details are excluded from this invoice (ideal for cash, POS, or zero-transfer transactions).</span>
            </div>
            <button
                type="button"
                @click="toggleEnabled"
                class="text-xs font-bold text-amber-900 underline hover:text-amber-700 ml-3 shrink-0"
            >
                Enable
            </button>
        </div>

        <div v-else-if="bankAccounts.length === 0" class="p-3 bg-gray-50 border border-dashed border-gray-300 rounded-lg text-center text-xs text-gray-500">
            No bank accounts configured yet.
            <button type="button" @click="openAddModal" class="text-primary font-bold ml-1 hover:underline">
                Add one now
            </button>
        </div>

        <div v-else class="space-y-1.5">
            <p class="text-[11px] text-gray-500">
                Choose which bank account(s) to display on this invoice:
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div
                v-for="bank in bankAccounts"
                :key="bank.id"
                @click="toggleSelection(bank.id)"
                class="flex items-start justify-between p-2.5 border rounded-lg cursor-pointer transition-all bg-white relative group"
                :class="isSelected(bank.id) ? 'border-primary ring-1 ring-primary/20 bg-primary/5' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50/50'"
            >
                <div class="flex items-start space-x-2.5 min-w-0 pr-2">
                    <input
                        type="checkbox"
                        :checked="isSelected(bank.id)"
                        @click.stop="toggleSelection(bank.id)"
                        class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary h-3.5 w-3.5"
                    />
                    <div class="min-w-0">
                        <div class="flex items-center space-x-1.5 flex-wrap">
                            <span class="font-bold text-xs text-gray-900 truncate">{{ bank.bank_name }}</span>
                            <span v-if="bank.is_default" class="px-1.5 py-0.2 text-[9px] font-bold bg-emerald-100 text-emerald-800 rounded">
                                Default
                            </span>
                        </div>
                        <div class="text-[11px] font-mono font-bold text-gray-800 tracking-wide mt-0.5">
                            {{ bank.account_number }}
                        </div>
                        <div class="text-[10px] text-gray-600 truncate mt-0.5 flex items-center">
                            <span class="text-gray-400 mr-1">Name:</span>
                            <strong class="text-gray-700 font-semibold truncate">{{ bank.account_name }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Edit Button -->
                <button
                    type="button"
                    @click.stop="openEditModal(bank)"
                    title="Edit Bank Account Details"
                    class="p-1 text-gray-400 hover:text-primary hover:bg-white rounded transition-colors flex-shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
            </div>
            </div>
        </div>

        <!-- Bank Account Modal (Add / Edit) -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-5 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center">
                        <span class="mr-1.5">💳</span>
                        {{ editingBankId ? 'Edit Bank Account' : 'Add Bank Account' }}
                    </h3>
                    <button type="button" @click="closeModal" class="text-gray-400 hover:text-gray-600 text-sm">✕</button>
                </div>

                <div v-if="errorMessage" class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-md">
                    {{ errorMessage }}
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Bank Name *</label>
                        <input
                            type="text"
                            list="nigerian-banks-list"
                            v-model="modalForm.bank_name"
                            class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:ring-primary focus:border-primary text-xs"
                            placeholder="Zenith Bank, GTBank, Moniepoint..."
                        />
                        <datalist id="nigerian-banks-list">
                            <option value="Zenith Bank" />
                            <option value="Guaranty Trust Bank (GTBank)" />
                            <option value="Moniepoint Microfinance Bank" />
                            <option value="OPay Digital Services" />
                            <option value="Access Bank" />
                            <option value="First Bank of Nigeria" />
                            <option value="United Bank for Africa (UBA)" />
                            <option value="Kuda Bank" />
                            <option value="Stanbic IBTC Bank" />
                            <option value="Fidelity Bank" />
                            <option value="Sterling Bank" />
                            <option value="First City Monument Bank (FCMB)" />
                            <option value="Wema Bank" />
                        </datalist>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Account Name *</label>
                        <input
                            type="text"
                            v-model="modalForm.account_name"
                            class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:ring-primary focus:border-primary text-xs"
                            placeholder="e.g. ZAYIN GLOBAL LTD"
                        />
                        <p class="text-[10px] text-gray-400 mt-0.5">The exact registered beneficiary name on the account.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Account Number (NUBAN) *</label>
                        <input
                            type="text"
                            v-model="modalForm.account_number"
                            maxlength="20"
                            class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:ring-primary focus:border-primary font-mono font-bold text-xs"
                            placeholder="0123456789"
                        />
                    </div>

                    <div>
                        <label class="flex items-center space-x-2 cursor-pointer mt-1">
                            <input
                                type="checkbox"
                                v-model="modalForm.is_default"
                                class="rounded border-gray-300 text-primary focus:ring-primary h-3.5 w-3.5"
                            />
                            <span class="text-xs font-semibold text-gray-700">Set as default account</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div>
                        <button
                            v-if="editingBankId"
                            type="button"
                            @click="deleteBank"
                            :disabled="isSaving"
                            class="text-xs text-rose-600 hover:text-rose-800 hover:underline font-medium"
                        >
                            Delete
                        </button>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-md"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            @click="saveBank"
                            :disabled="isSaving"
                            class="px-3.5 py-1.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-md shadow-sm disabled:opacity-50"
                        >
                            {{ isSaving ? 'Saving...' : 'Save Account' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

const props = defineProps<{
    modelValue: number[];
    bankAccounts: Array<{
        id: number;
        bank_name: string;
        account_name: string;
        account_number: string;
        is_default?: boolean;
    }>;
    defaultAccountName?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: number[]): void;
    (e: 'update:bankAccounts', value: any[]): void;
    (e: 'change'): void;
}>();

const showModal = ref(false);
const editingBankId = ref<number | null>(null);
const isSaving = ref(false);
const errorMessage = ref('');

const modalForm = ref({
    bank_name: '',
    account_name: '',
    account_number: '',
    is_default: false,
});

const isEnabled = computed(() => {
    return (props.modelValue || []).length > 0;
});

function toggleEnabled() {
    if (isEnabled.value) {
        emit('update:modelValue', []);
    } else {
        const defaultBank = props.bankAccounts.find((b) => b.is_default);
        if (defaultBank) {
            emit('update:modelValue', [defaultBank.id]);
        } else if (props.bankAccounts.length > 0) {
            emit('update:modelValue', [props.bankAccounts[0].id]);
        } else {
            emit('update:modelValue', []);
        }
    }
    emit('change');
}

function selectAll() {
    emit('update:modelValue', props.bankAccounts.map((b) => b.id));
    emit('change');
}

function clearAll() {
    emit('update:modelValue', []);
    emit('change');
}

function isSelected(id: number): boolean {
    return (props.modelValue || []).includes(id);
}

function toggleSelection(id: number) {
    const current = [...(props.modelValue || [])];
    const index = current.indexOf(id);
    if (index > -1) {
        current.splice(index, 1);
    } else {
        current.push(id);
    }
    emit('update:modelValue', current);
    emit('change');
}

function openAddModal() {
    editingBankId.value = null;
    errorMessage.value = '';
    modalForm.value = {
        bank_name: '',
        account_name: props.defaultAccountName || 'ZAYIN GLOBAL LTD',
        account_number: '',
        is_default: props.bankAccounts.length === 0,
    };
    showModal.value = true;
}

function openEditModal(bank: any) {
    editingBankId.value = bank.id;
    errorMessage.value = '';
    modalForm.value = {
        bank_name: bank.bank_name,
        account_name: bank.account_name,
        account_number: bank.account_number,
        is_default: Boolean(bank.is_default),
    };
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingBankId.value = null;
    errorMessage.value = '';
}

async function saveBank() {
    if (!modalForm.value.bank_name.trim() || !modalForm.value.account_number.trim() || !modalForm.value.account_name.trim()) {
        errorMessage.value = 'Please provide bank name, account name, and account number.';
        return;
    }

    isSaving.value = true;
    errorMessage.value = '';

    try {
        const axios = (window as any).axios;
        let response;
        if (editingBankId.value) {
            response = await axios.put(
                `/invoices-settings/bank-accounts/${editingBankId.value}`,
                modalForm.value,
                { headers: { Accept: 'application/json' } }
            );
        } else {
            response = await axios.post(
                '/invoices-settings/bank-accounts',
                modalForm.value,
                { headers: { Accept: 'application/json' } }
            );
        }

        if (response.data && response.data.accounts) {
            emit('update:bankAccounts', response.data.accounts);

            // If it was a new account, auto-select it
            if (!editingBankId.value && response.data.account) {
                const updatedSelection = Array.from(new Set([...(props.modelValue || []), response.data.account.id]));
                emit('update:modelValue', updatedSelection);
            }
        }

        closeModal();
        emit('change');
    } catch (err: any) {
        if (err.response?.data?.message) {
            errorMessage.value = err.response.data.message;
        } else {
            errorMessage.value = 'Failed to save bank account. Please check inputs.';
        }
    } finally {
        isSaving.value = false;
    }
}

async function deleteBank() {
    if (!editingBankId.value) return;
    if (!confirm('Are you sure you want to remove this bank account?')) return;

    isSaving.value = true;
    errorMessage.value = '';

    try {
        const axios = (window as any).axios;
        const response = await axios.delete(
            `/invoices-settings/bank-accounts/${editingBankId.value}`,
            { headers: { Accept: 'application/json' } }
        );

        if (response.data && response.data.accounts) {
            emit('update:bankAccounts', response.data.accounts);
        } else {
            const remaining = props.bankAccounts.filter(b => b.id !== editingBankId.value);
            emit('update:bankAccounts', remaining);
        }

        // Remove from selection
        const updatedSelection = (props.modelValue || []).filter(id => id !== editingBankId.value);
        emit('update:modelValue', updatedSelection);

        closeModal();
        emit('change');
    } catch (err: any) {
        errorMessage.value = 'Failed to delete bank account.';
    } finally {
        isSaving.value = false;
    }
}
</script>
