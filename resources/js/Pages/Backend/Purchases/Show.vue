<template>
    <BaseLayout title="Purchase Detail">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Purchase Detail <small>{{ data.ref }}</small>
                <div class="float-right">
                    <button type="button" class="btn btn-secondary btn-sm" @click.prevent="confirmOrder('pending')" v-if="data.status == 'draft'">
                        <i class="si si-check mr-1"></i>
                        Confirm
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" @click.prevent="confirmOrder('ordered')" v-if="data.status == 'pending'">
                        <i class="fa fa-truck mr-1"></i>
                        Ordered
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" @click.prevent="confirmOrder('done')" v-if="data.status == 'ordered'">
                        <i class="si si-check mr-1"></i>
                        Recieved
                    </button>
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
                            <h5 class="font-weight-bold">Invoice Info</h5>
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
                                    <th colspan="6" class="font-w600 text-right">Total Due:</th>
                                    <td class="text-right">{{ currency(data.total_paid) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="font-w600 text-right">Total Remaining:</th>
                                    <td class="text-right">{{ currency(data.grand_total - data.total_paid) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sale Payment -->
            <div class="content-heading pt-10">
                Payment ({{ data.payment.length }})
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-secondary" @click.prevent="$bvModal.show('payment')">
                        <i class="si si-plus"></i>
                        Create
                    </button>
                </div>
            </div>
            <div class="block block-rounded block-shadow">
                <div class="block-content p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Reference No</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Payment Note</th>
                                </tr>
                            </thead>
                            <tbody v-if="data.payment.length">
                                <tr v-for="(p, i) in data.payment" :key="i">
                                    <td>{{ i+1 }}</td>
                                    <td>{{ format_date(p.created_at) }}</td>
                                    <td>{{ p.ref }}</td>
                                    <td>{{ currency(p.amount) }}</td>
                                    <td>
                                        {{ p.payment_method.name }}
                                    </td>
                                    <td>{{ p.note }}</td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="7">Payment Not Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <b-modal id="payment" ref="payment" size="lg" content-class="rounded" body-class="p-0" centered no-close-on-backdrop hide-footer hide-header>
            <form @submit.prevent="submitPayment">
                <div class="block block-rounded block-transparent mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Create Payment</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" @click="$bvModal.hide('payment')">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="payment-recieved">Date</label>
                                    <flat-pickr class="bg-white" :config="config" v-bind:class="{'form-control':true , 'is-invalid' : errors.date} " v-model="FormPayment.date"></flat-pickr>
                                </div>
                                <div class="form-group">
                                    <label for="payment-recieved">Payment Method</label>
                                    <v-select
                                        v-model="FormPayment.payment_method"
                                        :reduce="label => label.value"
                                        placeholder="Choose"
                                        :options="
                                            [
                                                {label: 'Cash', value: 'cash'},
                                                {label: 'Bank Transfer', value: 'transfer'},
                                                {label: 'Other', value: 'other'},
                                            ]"
                                    ></v-select>
                                </div>
                                <div class="form-group">
                                    <label for="amount-recieved">Amount Received</label>
                                    <CurrencyInput v-model="FormPayment.amount_received" id="amount-recieved" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="payment-ref">Reference No</label>
                                    <input type="text" class="form-control" id="payment-ref" v-model="FormPayment.ref">
                                </div>
                                <div class="form-group">
                                    <label for="amount-due">Amount Due</label>
                                    <div class="font-w600">{{ currency(duePayment) }}</div>
                                </div>
                                <div class="form-group" v-if="FormPayment.payment_method == 'transfer'">
                                    <label for="payment-change">Bank</label>
                                    <div>{{ currency(FormPayment.change) }}</div>
                                </div>
                                <div class="form-group" v-else-if="FormPayment.payment_method == 'cash'">
                                    <label for="payment-change">Change</label>
                                    <div>{{ currency(FormPayment.change) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-noborder" @click.prevent="$bvModal.hide('payment')">
                        <i class="fa fa-close"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-noborder">
                        <i class="fa fa-check"></i> Save
                    </button>
                </div>
            </form>
        </b-modal>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import flatPickr from 'vue-flatpickr-component';
import CurrencyInput  from '@/components/Form/CurrencyInput.vue';
import vSelect from 'vue-select';
export default {
    components: {
        BaseLayout,
        CurrencyInput,
        flatPickr,
        vSelect,
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
        }
    }
}
</script>
