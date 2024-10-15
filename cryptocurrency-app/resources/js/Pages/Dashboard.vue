<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios'
import VueNumberFormat from 'vue-number-format'
import { ref, onMounted, computed } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import AddEditWalletModal from '@/Components/AddEditWalletModal.vue'
import { router } from '@inertiajs/vue3';

let showModal = ref(false);
let selectedWallet = ref('');
let modalMode = ref('');
let refreshInterval = ref('');
let search = ref('');
let column = ref('name');
let reversed = ref(false);
var props = defineProps({
    wallets: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    autoRefresh();
})
function openModal(mode) {
    modalMode.value = mode;

    document.body.classList.add("modal-open");
    showModal.value = true;
};
function openEditModal(wallet) {
    modalMode.value = 'edit';
    selectedWallet.value = wallet;
    document.body.classList.add("modal-open");
    showModal.value = true;

};
function openDeleteModal(wallet) {
    modalMode.value = 'delete';
    selectedWallet.value = wallet;
    document.body.classList.add("modal-open");
    showModal.value = true;

};

function closeModal() {
    showModal.value = false;
    document.body.classList.remove("modal-open");
};
async function saveCurrency(selectedcurrency, price, amount, total) {
    //console.log(price);

    if (modalMode.value == 'add') {
        try {
            await axios.post('save', {
                selectedcurrency: selectedcurrency,
                user: props.user,
                price: price,
                amount: amount,
                total: total
            }

            );
            closeModal();
            refreshData();
            toast.success("Currency saved successfully !", {
                autoClose: 3000,
                position: toast.POSITION.BOTTOM_RIGHT,
            });

        } catch (error) {
            toast.error("Error saving  currency !", {
                autoClose: 3000,
                position: toast.POSITION.BOTTOM_RIGHT,
            });

        }
    } else {
        try {
            await axios.post('update', {
                selectedcurrency: selectedcurrency,
                user: props.user,
                price: price,
                amount: amount,
                total: total
            }

            );
            closeModal();
            refreshData();
            toast.success("Currency saved successfully !", {
                autoClose: 3000,
                position: toast.POSITION.BOTTOM_RIGHT,
            });

        } catch (error) {
            toast.error("Error saving  currency !", {
                autoClose: 3000,
                position: toast.POSITION.BOTTOM_RIGHT,
            });

        }
    }

};
async function deleteCurrency() {
    try {
        await axios.post('delete', {
            selectedcurrency: selectedWallet.value,
            user: props.user,
        }

        );
        closeModal();
        toast.success("Currency deleted successfully !", {
            autoClose: 3000,
            position: toast.POSITION.BOTTOM_RIGHT,
        });
        refreshData();

    } catch (error) {
        toast.error("Error while deleting  currency !", {
            autoClose: 3000,
            position: toast.POSITION.BOTTOM_RIGHT,
        });

    }
};
async function refreshData() {
    //console.log('refresh');
    router.visit('/dashboard', {
        only: ['wallets', 'user'],
        preserveState: true,
    });
};

function autoRefresh() {
    refreshInterval.value = setInterval(() => {
        refreshData();
    }, 600000); // refresh after 10 minutes
};

const filteredWallets = computed(() => {
    let coins = props.wallets;

    coins = coins.filter((coin) => coin.name.toUpperCase().includes(search.value.toUpperCase()));

    if (column.value === 'name') {
        coins.sort((a, b) => a.name.localeCompare(b.name));
    } else {
        coins.sort((a, b) => a[column.value] - b[column.value]);
    }

    if (reversed.value) {
        coins.reverse();
    }

    return coins;
});

function sortTable(selectedColumn) {
    column.value = selectedColumn;
    reversed.value = !reversed.value;
}

//axios.defaults.headers['X-API-KEY'] = 'CG-aG72w9qQQKDVojYUQNeFsw1o';

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>


            <button class="bg-orange-500 hover:bg-orange-700 text-black font-bold py-2 px-4 rounded-full"
                @click="openModal('add')">
                <font-awesome-icon icon="credit-card" />
                Buy
            </button>
            <h2 class="text-xl font-semibold leading-tight text-white-800 dark:text-white pt-3">
                ${{ props.user.balance }}
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden  shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 w-full  overflow-y-auto" v-if="wallets.length > 0">
                        <input v-model="search" type="text" placeholder="Search" class="mt-1 p-2 block dark:bg-gray-800  border border-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        <table class=" table-auto  w-full">
                            <thead class="bg-gray-800 text-white-500">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                        <a @click="sortTable('name')" class="hover:underline hover:cursor-pointer">Name</a></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                        <a @click="sortTable('amount')" class="hover:underline hover:cursor-pointer">Amount</a></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        <a @click="sortTable('price')" class="hover:underline hover:cursor-pointer">Price</a></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                        <a @click="sortTable('total')" class="hover:underline hover:cursor-pointer">Total</a></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800">
                                <tr v-for="wallet in filteredWallets" :key="wallet.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-white">
                                        <div class="flex flex-row items-center">

                                            <img :src="wallet.image" alt="logo" class="h-6 w-6 mr-2" />
                                            {{ wallet.name }}
                                        </div>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-white">{{ wallet.amount }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-white">${{ wallet.price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-white"><strong>${{ wallet.total
                                            }}</strong></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-white">
                                        <span v-if="wallet.price_change_24h < 0" class="text-red-700 pr-3">${{
                                            wallet.price_change_24h
                                        }}</span>
                                        <span v-if="wallet.price_change_24h > 0" class="text-green-700 pr-3">${{
                                            wallet.price_change_24h }}</span>
                                        <span v-if="wallet.price_change_percentage_24h < 0" class="text-red-700">{{
                                            wallet.price_change_percentage_24h }}%</span>
                                        <span v-if="wallet.price_change_percentage_24h > 0" class="text-green-700">{{
                                            wallet.price_change_percentage_24h }}%</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-black ">
                                        <div class="flex items-center space-x-2">
                                            <font-awesome-icon icon="pencil" class="text-white  cursor-pointer"
                                                @click="openEditModal(wallet)" />
                                            <font-awesome-icon icon="trash"
                                                class="text-red-500 hover:text-red-700 cursor-pointer"
                                                @click="openDeleteModal(wallet)" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <Transition>
            <add-edit-wallet-modal :show="showModal" :mode="modalMode" :user="props.user"
                :selectedWallet="selectedWallet" @close="closeModal" @save="saveCurrency"
                @deleteCurrency="deleteCurrency">
            </add-edit-wallet-modal>
        </Transition>
    </AuthenticatedLayout>
</template>
<style>
.v-enter-active,
.v-leave-active {
    transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}

body.modal-open {
    overflow: hidden;
}
</style>
