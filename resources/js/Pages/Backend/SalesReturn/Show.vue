<template>
    <BaseLayout title="Detail Sale">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Sale Return Detail <small>{{ data.ref }}</small>
                <div class="float-right">
                    <button class="btn btn-secondary btn-sm" type="button" @click="updateStatus('confirmed')" v-if="data.status == 'pending'">
                        <i class="si si-check mr-1"></i>
                        Confirm Return
                    </button>
                </div>
            </div>
            <div class="block block-rounded block-shadow mb-10">
                <div class="block-content">
                    <div class="row">
                        <div class="mb-4 col-sm-12 col-md-6 col-lg-6">
                            <h5 class="font-weight-bold">Return Info</h5>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Return No</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ data.ref }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Return Date</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ format_date(data.date) }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Payment Status</div>
                                <div class="col-lg-9 value">
                                    <span class="badge badge-primary" v-if="data.payment_status == 'paid'">Paid</span>
                                    <span class="badge badge-info" v-else-if="data.payment_status == 'partial'">Partial</span>
                                    <span class="badge badge-warning" v-else-if="data.payment_status == 'pending'">Pending</span>
                                    <span class="badge badge-danger" v-else>Unpaid</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 col-sm-12 col-md-6 col-lg-6">
                            <h5 class="font-weight-bold mb-3">Invoice Info</h5>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Invoice No</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ data.sale.ref }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Return Date</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ format_date(data.sale.date) }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Customer</div>
                                <div class="col-lg-9 value">
                                    <a href="#">{{ data.sale.customer.name }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sale Line -->
            <h2 class="content-heading pt-10">Products ({{ data.line.length }})</h2>
            <div class="block block-rounded block-shadow">
                <div class="block-content p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="8%">Quantity</th>
                                    <th width="14%">Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(d, i) in data.line" :key="i">
                                    <td><a class="font-w600" href="#">{{ d.product.name }}</a></td>
                                    <td class="text-center">{{ d.qty }}</td>
                                    <td>{{ currency(d.unit_price) }}</td>
                                    <td>{{ currency(d.sub_total) }}</td>
                                </tr>
                            <!-- </tbody> -->
                            <!-- <tbody> -->
                                <tr>
                                    <th colspan="3" class="font-w600 text-right">Total: </th>
                                    <td class="text-right">{{ currency(data.total) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="font-w600 text-right">Discount: <b>(-)</b></th>
                                    <td class="text-right">{{ currency(data.discount_amount) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="font-w600 text-right">Payment Fee: <b>(+)</b></th>
                                    <td class="text-right">{{ currency(data.payment_fee) }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <th colspan="3" class="font-w600 text-right">Grand Total: </th>
                                    <td class="text-right">{{ currency(data.grand_total) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="3" class="font-w600 text-right">Total Paid:</th>
                                    <td class="text-right">{{ currency(data.total_paid) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="font-w600 text-right">Total Remaining:</th>
                                    <td class="text-right">{{ currency(duePayment) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <payment-table type="App\Models\SaleReturn" :id="data.id" :amount_due="data.grand_total - data.total_paid" :url="this.route('admin.sale.return.payment')"/>
        </div>

    </BaseLayout>
</template>
<style>
.value::before{
    content: ': ';
    font-weight: normal;
}
</style>
<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import flatPickr from 'vue-flatpickr-component';
import hasPermission from '@/Utils/permission';
import PaymentTable from '../Payment/PaymentTable.vue';
export default {
    components: {
        BaseLayout,
        flatPickr,
        PaymentTable
    },
    props: {
        data : Object,
        payment : Array,
        errors : Object,
    },
    data(){
        return {
            duePayment : 0,
            FormPayment: {
                purchase_id : null,
                amount : null,
                amount_received : null,
                change : null,
                ref : null,
                date : new Date(),
                payment_method_id : null,
            },
            config: {
                altFormat: 'j F Y',
                altInput: true,
                dateFormat: 'Y-m-d',
                showMonths: 1,
            },
        }
    },
    mounted(){
        this.duePayment = this.data.grand_total - this.data.total_paid;
    },
    methods :{
        hasPermission,
        format_date(value){
            if (value) {

                return moment(String(value)).format('DD MMMM YYYY hh:mm')
            }
        },
        updateStatus(value){
            let form = this.$inertia.form({
                id : this.data.id,
                status : value,
            });
            let url = this.route("admin.sale.order.update_status");
            form.post(url, {
                preserveScroll: true,
                onProgress: ()=> {
                    this.$swal.fire({
                        title: 'Tunggu Sebentar...',
                        text: '',
                        imageUrl: window._asset + 'media/loading.gif',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                },
                onSuccess: () => {
                    this.$swal.close();
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Sale Return Updated Successfully!`,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        timer : 1500,
                    });
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        },
    }
}
</script>
