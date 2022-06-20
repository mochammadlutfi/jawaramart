<template>
    <BaseLayout :title="title">
        <div class="content">
            <form @submit.prevent="submit">
                <div class="content-heading pt-0 mb-3">
                    {{ title }}
                    <div class="float-right">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="si si-paper-plane mr-1"></i>
                            Save
                        </button>
                    </div>
                </div>
                <div class="block block-bordered block-shadow block-rounded">
                    <div class="block-content pb-15">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-ref">Source Document</label>
                                    <div class="form-control-plaintext font-w700">{{ data.ref }}</div>
                                </div>
                                <div class="form-group">
                                    <label for="field-ref">Document Date</label>
                                    <div class="form-control-plaintext font-w700">{{ data.date }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-supplier">Supplier</label>
                                    <div class="form-control-plaintext font-w700">{{ data.supplier.name }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-date">Return Date</label>
                                    <div class="has-search">
                                        <i class="si si-calendar"></i>
                                        <flat-pickr class="bg-white" :config="config" v-bind:class="{'form-control':true , 'is-invalid' : errors.date}" v-model="form.date"></flat-pickr>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.date">{{ errors.date[0] }}</div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-vcenter" id="pembelian_table">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="36%">Product</th>
                                            <th width="14%">Sell Quantity</th>
                                            <th>Net Price</th>
                                            <th width="14%">Return Qty</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="lines.length">
                                            <tr v-for="(line, index) in lines" :key="index">
                                                <td class="px-0">
                                                    {{ line.name }}
                                                </td>
                                                <td>
                                                    {{ line.qty }}
                                                </td>
                                                <td>
                                                    {{ currency(line.price) }}
                                                </td>
                                                <td>
                                                    <div class="input-group product-qty product-qty-sm">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-noborder btn-primary btn-sm m-0" type="button" @click="decrement(line.variant_id)">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </span>
                                                        <input type="text" v-model="line.qty_return" class="form-control input-number text-center form-control-sm">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-noborder btn-primary btn-sm m-0" type="button" @click="increment(line.variant_id)">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ currency(line.subtotal) }}
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="font-w700 text-uppercase text-right">Total</td>
                                            <td colspan="2" class="font-w700 text-right">{{ currency(form.total) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="field-discount_type">Discount Type</label>
                                    <b-form-select v-model="form.discount_type" id="field-discount_type" name="discount_type">
                                        <b-form-select-option value="fixed">Fixed</b-form-select-option>
                                        <b-form-select-option value="percentage">Percentage</b-form-select-option>
                                    </b-form-select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="field-discount_amount">Discount Amount</label>
                                    <input type="number" min="0" max="100" class="form-control" v-model.number="form.discount_value" id="field-discount_amount" v-if="form.discount_type == 'percentage'">
                                    <CurrencyInput v-model="form.discount_amount" id="field-discount_amount" class="form-control" v-else/>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="mr-5">Discount</label>:
                                   <div class="font-w700"> (-) {{ currency(form.discount_amount) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="field-note">Additional Notes</label>
                                    <textarea class="form-control" id="field-note" v-model="form.note"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-4 ml-auto">
                                <div class="form-group">
                                    <label class="mr-5">Grand Total</label>:
                                    <div class="font-w700">{{ currency(form.grand_total) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import CustomerSelect  from '@/components/Form/CustomerSelect.vue';
import TaxRateSelect from '@/Components/Form/TaxRateSelect.vue';
import flatPickr from 'vue-flatpickr-component';
import ProductQty from "@/components/Product/ProductQty.vue";
import CurrencyInput  from '@/components/Form/CurrencyInput.vue';
import BrowseProduct from '@/components/Product/ProductBrowseModal.vue';
import _ from 'lodash';

export default {
    components: {
        BaseLayout,
        CustomerSelect,
        flatPickr,
        CurrencyInput,
        ProductQty,
        BrowseProduct,
        TaxRateSelect,
    },
    data(){
        return {
            form : {
                id: null,
                ref : null,
                purchase_id : null,
                date : new Date(),
                discount_type : 'fixed',
                discount_amount : 0,
                discount_value : 0,
                status : 'draft',
                total : 0,
                note : null,
                grand_total : 0,
            },
            config: {
                altFormat: 'j F Y',
                altInput: true,
                dateFormat: 'Y-m-d',
                showMonths: 1,
            },
            lines : [],
            title : 'Create Purchase Return',
            
        }
    },
    props : {
        data : Object,
        errors : Object,
        editMode: Boolean,
    },
    validations () {
        return {
            detail : {
                price : {
                    required,
                },
            },
        }
    },
    mounted() {
        this.fillData();
    },
    watch: {
        form: {
            deep: true,
            handler(val){
                if(val.discount_type == 'percentage' && val.discount_value){
                    if(val.discount_value > 100){
                        val.discount_value = 100;
                    }
                    val.discount_amount  =  val.total *  val.discount_value / 100;
                }
                if(val.discount_type == 'fixed'){
                    if(val.discount_amount > val.total){
                        val.discount_amount = val.total;
                    }
                    val.discount_value = 0;
                }
                if(val.tax){
                    val.tax_id = val.tax.id;
                    val.tax_amount = (val.total - val.discount_amount) * val.tax.amount /100;
                }else{
                    val.tax_amount = 0;
                }
                // if(val.payment_received){
                // }


                val.grand_total = (val.total - val.discount_amount) + val.tax_amount;

                val.payment_change = val.payment_received - val.grand_total;
            }
        }
    },
    methods: {
        //----------- Calcul Total
        CaclulTotal() {
            this.form.total = 0;
            for (var i = 0; i < this.lines.length; i++) {
                if(this.lines[i].discount_amount){
                    this.lines[i].subtotal = this.lines[i].qty_return * (this.lines[i].price - this.lines[i].discount_amount);

                }else{
                    this.lines[i].subtotal = this.lines[i].qty_return * this.lines[i].price;
                }

                this.form.total = this.form.total + this.lines[i].subtotal;
            }

            this.form.grand_total = this.form.total - this.form.discount_amount;
        },
 
        //----------------------------------- Increment QTY ------------------------------\\
        increment(variant_id) {
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id == variant_id) {
                    if (this.lines[i].qty_return + 1 > this.lines[i].qty) {
                        this.$swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: `Only ${ this.lines[i].qty } available`,
                            showConfirmButton : false,
                            showCancelButton: false,
                            timer : 1000,
                        });
                    } else {
                        this.lines[i].qty_return++;
                    }
                }
            }
            this.CaclulTotal();
            this.$forceUpdate();
        },

        //----------------------------------- decrement QTY ------------------------------\\
        decrement(variant_id) {
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id == variant_id) {
                    if (this.lines[i].qty_return - 1 < 1) {
                        this.deleteCart(variant_id);
                    } else {
                        this.lines[i].qty_return--;
                    }
                }
            }
            this.CaclulTotal();
            this.$forceUpdate();
        },

        submit: function(){
            let data = {};
            data = Object.assign(data, this.form)
            let lines = {
                lines : this.lines
            }
            data = Object.assign(data, lines)

            let form = this.$inertia.form(data)
            let url = this.editMode ? this.route("admin.purchase.return.update") : this.route("admin.purchase.return.store");
            this.$swal.fire({
                title: 'Please Wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            form.post(url, {
                preserveScroll: true,
                resetOnSuccess: false,
                onSuccess: () => {
                    this.$swal.close();
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Purchase Return Added Successfully!`,
                        showCancelButton: false,
                        showConfirmButton: false,
                    });
                },
                onError :() => {
                    this.$swal.close();
                }
            });
        },

        fillData(){
            this.title = 'Create Purchase Return';
            this.form.purchase_id = this.data.id;
            this.form.discount_amount = this.data.discount_amount;
            this.form.discount_value = this.data.discount_value;
            this.form.total = this.data.total;
            this.form.grand_total = this.data.grand_total;

            if(this.data.line.length > 0){
                this.data.line.forEach((value, index) => {
                    let line = {
                        id : value.id,
                        product_id : value.product_id,
                        name : value.product.name,
                        variant_id : value.variant_id,
                        variant_name : '',
                        price : value.net_price,
                        qty : value.qty,
                        qty_return : 0,
                        subtotal : 0,
                    }
                    this.lines.push(line);
                });
            }
        },
    }
}
</script>
