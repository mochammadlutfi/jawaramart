<template>
    <BaseLayout title="Purchase Detail">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Purchase Detail <small>{{ data.ref }}</small>
                <div class="float-right">
                    <a class="btn btn-secondary btn-sm" :href="route('admin.purchase.return.create', {id : data.id})" v-if="data.status == 'done'">
                        <i class="fas fa-undo mr-1"></i>
                        Create Purchase Return
                    </a>

                    <button type="button" class="btn btn-secondary btn-sm" @click.prevent="confirmOrder('ordered')" v-if="data.status == 'pending'">
                        <i class="si si-check mr-1"></i>
                        Confirm Order
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" @click.prevent="confirmOrder('done')" v-if="data.status == 'ordered'">
                        <i class="si si-check mr-1"></i>
                        Recieved
                    </button>
                    
                    <b-dropdown id="dropdown-1" text="Actions" size="sm" right >
                        <a class="dropdown-item" v-if="hasPermission('Purchase Order', 'delete') && data.status == 'draft'"
                         :href="route('admin.purchase.order.edit', { id : data.id})">
                            <i class="si si-note mr-3"></i>
                            <span class="font-w600">Edit</span>
                        </a>
                        <b-dropdown-item v-if="hasPermission('Purchase Order', 'delete') && data.status == 'draft'" @click="destroy(data.id)">
                            <i class="si si-trash mr-3"></i>
                            <span class="font-w600">Delete</span>
                        </b-dropdown-item>
                        <a class="dropdown-item" :href="route('admin.purchase.order.pdf', { id : data.id})" target="_blank">
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
                            <h5 class="font-weight-bold mb-3">Supplier Info</h5>
                            <div>{{ data.supplier.name }}</div>
                            <div>{{ data.supplier.email }}</div>
                            <div>{{ data.supplier.phone }}</div>
                        </div>
                        <div class="mb-4 col-sm-12 col-md-6 col-lg-6">
                            <h5 class="font-weight-bold">Bill Info</h5>
                            <div>Payment Status : 
                                <span class="badge badge-primary" v-if="data.payment_status == 'paid'">Paid</span>
                                <span class="badge badge-warning" v-else-if="data.payment_status == 'partial'">Partial</span>
                                <span class="badge badge-danger" v-else>Unpaid</span>
                            </div>
                            <div>Status : 
                                <span class="badge badge-secondary" v-if="data.status == 'draft'">Draft</span>
                                <span class="badge badge-warning" v-else-if="data.status == 'pending'">Pending</span>
                                <span class="badge badge-info" v-else-if="data.status == 'ordered'">Ordered</span>
                                <span class="badge badge-success" v-else-if="data.status == 'done'">Done</span>
                                <span class="badge badge-danger" v-else>Cancel</span>
                            </div>
                            <div>Staff : {{ data.staff.name }}</div>
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
                                    <td>{{ currency(d.unit_price - d.discount_amount + d.tax_amount) }}</td>
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
                                <!-- <tr>
                                    <th colspan="6" class="font-w600 text-right">Shipping: <b>(+)</b></th>
                                    <td class="text-right">{{ currency(data.shipping_amount) }}</td>
                                </tr> -->
                                <tr class="table-warning">
                                    <th colspan="6" class="font-w600 text-right">Grand Total: </th>
                                    <td class="text-right">{{ currency(data.grand_total) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="6" class="font-w600 text-right">Total Paid:</th>
                                    <td class="text-right">{{ currency(data.total_paid) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="font-w600 text-right">Total Due:</th>
                                    <td class="text-right">{{ currency(data.to_pay) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sale Payment -->
            <payment-table type="App\Models\Purchase" :id="data.id" :amount_due="data.to_pay" :url="this.route('admin.purchase.order.payment')"/>
        </div>

    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import PaymentTable from '../Payment/PaymentTable.vue';
export default {
    components: {
        BaseLayout,
        PaymentTable
    },
    props: {
        data : Object,
        errors : Object,
    },
    data(){
        return {
            duePayment : 0,
            FormPayment: {
                purchase_id : this.data.id,
                amount : null,
                amount_received : 0,
                payment_method : 'cash',
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
            editPayment : false,
        }
    },
    watch:{
        FormPayment: {
            deep: true,
            handler(val){
                val.change = val.amount_received - this.duePayment;
            }
        }
    },
    mounted(){
        this.duePayment = this.data.grand_total - this.data.total_paid;
        this.FormPayment.amount_received = this.duePayment;
        this.FormPayment.amount = this.duePayment;
    },
    methods :{
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MM YYYY')
            }
        },
        submitPayment(){
            let form = this.$inertia.form(this.FormPayment)
            let url = this.editPayment ? this.route("admin.payment.update") : this.route("admin.purchase.order.payment");
            form.post(url, {
                preserveScroll: true,
                resetOnSuccess: false,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Payment Added Successfully!`,
                        showConfirmButton : false,
                        showCancelButton: false,
                        timer : 1500,
                    });
                },
            });
        },
        paymentReset(){
            this.FormPayment.amount = 0;
            this.FormPayment.amount = 0;
            this.FormPayment.amount = 0;
            this.FormPayment.amount = 0;
            this.FormPayment.amount = 0;
        },
        confirmOrder(status){
            this.$swal.fire({
                title: 'Please wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form({
                id : this.data.id,
                status : status
            })
            let url = this.route("admin.purchase.order.update_status");
            form.post(url, {
                preserveScroll: true,
                resetOnSuccess: false,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Purchase Status Updated Successfully!`,
                        showConfirmButton : false,
                        showCancelButton: false,
                        timer : 1500,
                    });
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        },
        destroy: function (id) {
            this.$swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('admin.purchase.order.delete', id), {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.reset();
                            return this.$swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: `Purchase order deleted!`,
                                showConfirmButton : false,
                                showCancelButton: false,
                                timer : 1500,
                            });
                        },
                    })
                }
            })
        },
    }
}
</script>
