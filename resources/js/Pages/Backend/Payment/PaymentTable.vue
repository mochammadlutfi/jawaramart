<template>
    <div>
        <div class="content-heading pt-10">
            Payment ({{ data.length }})
            <div class="float-right">
                <button type="button" class="btn btn-sm btn-secondary" @click.prevent="openModal" v-if="amount_due">
                    <i class="si si-plus"></i>
                    Add Payment
                </button>
            </div>
        </div>
        <div class="block block-rounded block-shadow">
            <div class="block-content p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped table-vcenter table-sm mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th>Amount</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="data.length">
                            <tr v-for="(p, i) in data" :key="i">
                                <td>{{ i+1 }}</td>
                                <td>{{ format_date(p.created_at) }}</td>
                                <td>
                                    {{ p.payment_method.name }}
                                </td>
                                <td>{{ currency(p.amount) }}</td>
                                <td>
                                    <button type="button" @click="destroy(p.id)" class="btn btn-sm btn-danger">
                                        <i class="si si-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center">
                                            <img class="img-fluid" :src="asset('images/not_found.png')">
                                            <h3 class="font-size-24 font-w600 mt-3">Data Tidak Ditemukan</h3>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <b-modal id="form-payment" ref="form-payment" size="lg" content-class="rounded" body-class="p-0" centered no-close-on-backdrop hide-footer hide-header>
            <form @submit.prevent="submit">
                <div class="block block-rounded block-transparent mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">{{ title}}</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" @click="$bvModal.hide('form-payment')">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="payment-recieved">Date</label>
                                    <flat-pickr class="bg-white" :config="config" v-bind:class="{'form-control':true} " v-model="form.date"></flat-pickr>
                                </div>
                                <div class="form-group">
                                    <label for="payment-recieved">Payment Method</label>
                                    <v-select
                                        v-model="form.payment_method_id"
                                        label="name"
                                        :reduce="data => data.id"
                                        placeholder="Choose"
                                        :options="payment_method"
                                    ></v-select>
                                </div>
                                <div class="form-group">
                                    <label for="amount-due">Amount Due</label>
                                    <div class="font-w600 form-control-plaintext">{{ currency(amount_due) }}</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="amount-recieved">Amount Received</label>
                                    <CurrencyInput v-model="form.amount_received" id="amount-recieved" class="form-control"/>
                                </div>
                                <div class="form-group" v-if="form.payment_method_id == 1">
                                    <label for="payment-change">Change</label>
                                    <div>{{ currency(form.change) }}</div>
                                </div>
                                <div class="form-group" v-else>
                                    <label for="payment-ref">Reference No</label>
                                    <input type="text" class="form-control" id="payment-ref" v-model="form.ref">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-noborder" @click.prevent="$bvModal.hide('form-payment')" :disabled="loading">
                        <i class="si si-close"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-noborder" :disabled="loading">
                        <i class="si si-check"></i> Save
                    </button>
                </div>
            </form>
        </b-modal>
    </div>
</template>
<script>
import CurrencyInput  from '@/components/Form/CurrencyInput.vue';
import flatPickr from 'vue-flatpickr-component';
import axios from 'axios';
import moment from 'moment';
import vSelect from 'vue-select';
export default {
    name : 'payment-table',
    components :{
        CurrencyInput,
        flatPickr,
        vSelect
    },
    props: {
        type : String,
        id : Number,
        amount_due : Number,
        url : String,
        errors : Object,
    },
    data(){
        return {
            data : [],
            title : 'Add New Payment',
            form : {
                id : null,
                paymenttable_type : this.type,
                paymenttable_id : this.id,
                amount : 0,
                amount_received : 0,
                change : 0,
                ref : null,
                date : new Date(),
                payment_method_id : 1,
            },
            payment_method : [],
            config: {
                altFormat: 'j F Y',
                altInput: true,
                dateFormat: 'Y-m-d',
                showMonths: 1,
            },
            editMode : false,
            loading : false,
        }
    },
    watch: {
        form: {
            deep: true,
            handler(val){
                val.change = val.amount_received - this.amount_due;

                if(val.amount_received >= this.amount_due){
                    val.amount = this.amount_due;
                }else{
                    val.amount = val.amount_received;
                }
            }
        }
    },
    mounted(){
        this.getData();
    },
    methods : {
        openModal(){
            this.form.amount_received = this.amount_due;
            this.$bvModal.show('form-payment');
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMMM YYYY hh:mm')
            }
        },
        async getData(){
            const self = this;
            await axios.get(self.route("admin.accounting.payment.data"),{
                params: {
                    id: self.id,
                    type : self.type
                }
            })
            .then(function (response) {
                const resp = response.data;
                self.data = resp.data;
                self.payment_method = resp.payment_methods;
                
            })
            .catch(function (error) {
                
            })
        },
        destroy: function (data) {
            this.$swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('admin.accounting.payment.delete', data), {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.getData(3);
                            return this.$swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: `Payment Deleted Successfully!`,
                                timer : 1500,
                                showConfirmButton : false,
                            });
                        },
                    })
                }
            })
        },
        submit: function () {
            let form = this.$inertia.form(this.form)
            let url = this.editMode ? this.route("admin.accounting.payment.update") : this.url;
            this.loading = true;
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.loading = false;
                    this.getData();
                    this.reset();
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Payment Saved Successfully!`,
                        timer : 1500,
                        showConfirmButton : false,
                    });
                },
            });
        },
        reset(){
            this.form.id = null;
            this.form.change = null;
            this.$bvModal.hide('form-payment');
        }
    }
}
</script>