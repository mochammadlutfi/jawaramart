<template>
    <BaseLayout title="Detail Sale">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Sale Detail <small>{{ data.ref }}</small>
                    <span class="badge badge-secondary" v-if="data.status == 'draft'">Draft</span>
                    <span class="badge badge-warning" v-else-if="data.status == 'pending'">Pending</span>
                    <span class="badge badge-info" v-else-if="data.status == 'confirmed'">Siap Dikirim</span>
                    <span class="badge badge-info" v-else-if="data.status == 'delivery'">Dalam Pengiriman</span>
                    <span class="badge badge-success" v-else-if="data.status == 'done'">Done</span>
                    <span class="badge badge-danger" v-else>Cancel</span>
                <div class="float-right">
                    <a class="btn btn-secondary btn-sm" :href="route('admin.sale.return.create', {id : data.id})">
                        <i class="si si-note mr-1"></i>
                        Create Sale Return
                    </a>
                    <button class="btn btn-secondary btn-sm" type="button" @click="updateStatus('confirmed')" v-if="data.status == 'pending'">
                        <i class="si si-check mr-1"></i>
                        Confirm Order
                    </button>
                    <button class="btn btn-secondary btn-sm" type="button" @click="updateStatus('delivery')" v-if="data.status == 'confirmed'">
                        <i class="si si-check mr-1"></i>
                        Order Sent
                    </button>
                    <button class="btn btn-secondary btn-sm" type="button" @click="updateStatus('cancel')" v-if="data.status == 'pending'">
                        <i class="si si-close mr-1"></i>
                        Cancel Order
                    </button>
                    <b-dropdown id="dropdown-1" text="Actions" size="sm" right >
                        <a class="dropdown-item" v-if="hasPermission('Sales Order', 'delete') && data.status == 'draft'"
                         :href="route('admin.purchase.order.edit', { id : data.id})">
                            <i class="si si-note mr-3"></i>
                            <span class="font-w600">Edit</span>
                        </a>
                        <b-dropdown-item v-if="hasPermission('Sales Order', 'delete') && data.status == 'cancel'" @click="destroy(data.id)">
                            <i class="si si-trash mr-3"></i>
                            <span class="font-w600">Delete</span>
                        </b-dropdown-item>
                        <a class="dropdown-item" :href="route('admin.sale.order.print', { id : data.id})">
                            <i class="si si-printer mr-3"></i>
                            <span class="font-w600">Print PDF</span>
                        </a>
                    </b-dropdown>
                </div>
            </div>
            <div class="block block-rounded block-shadow mb-10">
                <div class="block-content">
                    <div class="row">
                        <div class="mb-4 col-sm-12 col-md-6 col-lg-6">
                            <h5 class="font-weight-bold">Invoice Info</h5>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Invoice No</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ data.ref }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Order Date</div>
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
                            <h5 class="font-weight-bold mb-3">Customer Info</h5>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Customer</div>
                                <div class="col-lg-9 value">
                                    <a href="#">{{ data.customer.name }}</a>
                                </div>
                            </div>
                            <div class="gutters-tiny row" v-if="data.delivery">
                                <div class="col-lg-3">Delivery Address</div>
                                <div class="col-lg-9 value">
                                    <b>{{ data.delivery.reciever }}</b><br>
                                    {{ data.delivery.phone }}<br>
                                    {{ data.delivery.address }}<br>
                                    {{ data.delivery.area_text }}, {{ data.delivery.postal_code }}
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
                                    <th width="34%">Product</th>
                                    <th width="8%">Quantity</th>
                                    <th width="14%">Unit Price</th>
                                    <th width="14%">Discount</th>
                                    <th width="14%">Tax</th>
                                    <th width="14%">Net Price</th>
                                    <th width="14%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(d, i) in data.line" :key="i">
                                    <td><a class="font-w600" href="#">{{ d.product.name }}</a></td>
                                    <td class="text-center">{{ d.qty }}</td>
                                    <td>{{ currency(d.product.sell_price) }}</td>
                                    <td>{{ currency(d.discount_amount) }}</td>
                                    <td>{{ currency(d.tax_amount) }}</td>
                                    <td>{{ currency(d.net_price) }}</td>
                                    <td>{{ currency(d.sub_total) }}</td>
                                </tr>
                            <!-- </tbody> -->
                            <!-- <tbody> -->
                                <tr>
                                    <th colspan="6" class="font-w600 text-right">Total: </th>
                                    <td class="text-right">{{ currency(data.total) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="font-w600 text-right">Discount: <b>(-)</b></th>
                                    <td class="text-right">{{ currency(data.discount_amount) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="font-w600 text-right">Order Tax: <b>(+)</b></th>
                                    <td class="text-right">{{ currency(data.tax_amount) }}</td>
                                </tr>
                                <tr v-if="!data.is_pos">
                                    <th colspan="6" class="font-w600 text-right">Shipping: <b>(+)</b></th>
                                    <td class="text-right">{{ currency(data.shipping_cost) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="font-w600 text-right">Payment Fee: <b>(+)</b></th>
                                    <td class="text-right">{{ currency(data.payment_fee) }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <th colspan="6" class="font-w600 text-right">Grand Total: </th>
                                    <td class="text-right">{{ currency(data.grand_total) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="6" class="font-w600 text-right">Total Paid:</th>
                                    <td class="text-right">{{ currency(data.total_paid) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="font-w600 text-right">Total Remaining:</th>
                                    <td class="text-right">{{ currency(duePayment) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <div class="content-heading pt-10">
                Payment ({{ data.payment.length }})
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-secondary" @click.prevent="$bvModal.show('payment')">
                        <i class="si si-trash"></i>
                        Create
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
                                    <th>Payment Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(p, i) in data.payment" :key="i">
                                    <td>{{ i+1 }}</td>
                                    <td>{{ format_date(p.created_at) }}</td>
                                    <td>
                                        {{ p.payment_method.name }}
                                    </td>
                                    <td>{{ currency(p.amount) }}</td>
                                    <td>{{ p.note }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
import CurrencyInput  from '@/components/Form/CurrencyInput.vue';
export default {
    components: {
        BaseLayout,
        CurrencyInput,
        flatPickr,

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
                        title: 'Berhasil',
                        text: `Status Sale Order Berhasil Di Perbaharui!`,
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
        submitPayment(){

        },
    }
}
</script>
