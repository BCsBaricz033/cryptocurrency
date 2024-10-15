<template>
    <div v-if="show" class="fixed inset-0 bg-grey-600 bg-opacity-50 flex items-center justify-center"
        @click.self="close">
        <div v-if="mode == 'add' || mode == 'edit'"
            class="bg-white p-6 rounded-lg shadow-lg w-96 h-screen overflow-auto">
            <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
            <span v-if="errorMessage" class="text-red-700">{{ errorMessage }}</span>
            <form @submit.prevent="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Name:</label>
                    <multiselect v-if="mode == 'add'" v-model="selectedcurrency" :options="cryptocurrencies"
                        :show-labels="false" :allow-empty="false" :searchable="true" track-by="name" label="name">
                    </multiselect>
                    <p v-else
                        class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        {{ selectedcurrency.name }}</p>
                </div>

                <span v-if="price_change_24h_in_currency < 0" class="text-red-700 pr-3">${{ price_change_24h_in_currency
                    }}</span>
                <span v-if="price_change_24h_in_currency > 0" class="text-green-700 pr-3">${{
                    price_change_24h_in_currency }}</span>
                <span v-if="price_change_percentage_24h_in_currency < 0" class="text-red-700">{{
                    price_change_percentage_24h_in_currency }}%</span>
                <span v-if="price_change_percentage_24h_in_currency > 0" class="text-green-700">{{
                    price_change_percentage_24h_in_currency }}%</span>


                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                    <input v-model="amount" type="number" min="0"  step="any" :max="user.balance"
                        class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <p
                        class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        {{ price }}</p>
                </div>
                <span v-if="balance < 0" class="text-red-700">${{ balance }}</span>
                <span v-if="balance > 0" class="text-green-700">${{ balance }}</span>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Total</label>
                    <p
                        class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        {{ total }}</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="mr-2 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Save</button>
                    <button @click="close" type="button"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
                </div>
            </form>
        </div>
        <div v-if="mode == 'delete'" class="bg-white p-6 rounded-lg shadow-lg w-96 overflow-auto">
            <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
            <div>
                <p>Are you sure you want to delete {{ selectedcurrency.name }}?</p>
                <div class="flex justify-end">
                    <button @click="deleteCurrency"
                        class="mr-2 bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Delete</button>
                    <button @click="close"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { watch } from 'vue';
import Multiselect from 'vue-multiselect'
export default {
    components: { Multiselect },
    props: ['show', 'mode', 'user', 'selectedWallet'],
    data() {
        return {
            value: '',
            cryptocurrencies: [],
            selectedcurrency: '',
            price: 0,
            amount: 1,
            errorMessage: '',
            price_change_24h_in_currency: '',
            price_change_percentage_24h_in_currency: '',

        };
    },
    watch: {
        selectedcurrency(value) {
            //console.log(value);
            if (value) {
                this.price = this.selectedcurrency.price;
                this.price_change_24h_in_currency = this.selectedcurrency.price_change_24h;
                this.price_change_percentage_24h_in_currency = this.selectedcurrency.price_change_percentage_24h;
                this.errorMessage = '';
            }



        },
        price(value) {
            this.errorMessage = '';
        },
        mode(value) {
            

        },
        show(value) {
            this.refresh();
            if (value && this.mode === 'edit' || this.mode == 'delete') {
                this.selectedcurrency = this.selectedWallet;
                this.amount = this.selectedWallet.amount;
                this.price = this.selectedWallet.price;
                this.price_change_24h_in_currency = this.selectedcurrency.price_change_24h;
                this.price_change_percentage_24h_in_currency = this.selectedcurrency.price_change_percentage_24h;
            }
            if (this.mode == 'add') {
                this.init();
                this.resetForm();
            }
        },
        amount(value) {
           
        }

    },
    methods: {
        close() {
            this.$emit('close');
            this.selectedcurrency = '';
            this.price = 0;
            this.amount = 1;
            this.price_change_24h_in_currency = '';
            this.price_change_percentage_24h_in_currency = '';
        },
        deleteCurrency() {
            this.$emit('deleteCurrency');
        },
        save() {
            this.errorMessage = '';
            if (!this.selectedcurrency) {
                this.errorMessage = 'Select a currency!';
            }
            else if (this.total > this.user.balance) {
                this.errorMessage = 'Your balance is not enought!';
            }
            else if (!this.price || this.price == 0) {
                this.errorMessage = 'The price is not valid!';
            }
            else {
                this.$emit('save', this.selectedcurrency, this.price, this.amount, this.total);
                this.resetForm();
            }
        },
        async init() {
            try {
                const response = await axios.get('/list');
                this.cryptocurrencies = response.data;
            } catch (error) {
                console.error(error);
                alert('Failed loading currency data, please try later!');
                this.close();
            }
        },
        async refresh() {
            try {
                const response = await axios.get('/refresh');
            } catch (error) {
                console.error(error);
            }
        },
        resetForm() {
            this.selectedcurrency = '';
            this.price = 0;
            this.amount = 1;
            this.price_change_24h_in_currency = '';
            this.price_change_percentage_24h_in_currency = '';
        }




    },
    mounted() {
        //this.init();


    },
    computed: {
        total() {
            return this.amount * this.price;
        },
        balance() {
            if(this.mode=='edit'){
                let balance=0;
                balance=this.user.balance + this.selectedWallet.total;
                balance= balance - this.total;
                return balance;
            }else{
                return this.user.balance - this.total;
            }
            
        },
        title() {
            if (this.mode == 'add') {
                return 'Add new cryptocurrency'
            }
            else if (this.mode == 'edit') {
                return 'Edit cryptocurrency'
            }
            else {
                return 'Delete cryptocurrency'
            }
        }

    }
};
</script>

<style scoped></style>