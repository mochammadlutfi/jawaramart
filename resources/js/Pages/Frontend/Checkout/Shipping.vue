<template>
    <BaseLayout title="Pengiriman">
        <div class="content">
            <div class="row">
                <div class="col-lg-8">
                    <ShippingDestination :value="address" @done="(value) => destination = value"/>
                    <div class="content-heading pt-0 mb-0 border-0 font-size-md">
                        Pesanan Kamu
                    </div>
                    <div class="block block-bordered block-rounded block-shadow">
                        <div class="block-content product-cart block-content-full py-0">
                            <template v-for="(d, i) in cart">
                                <div class="cart-item" :key="d.variant_id" v-if="i == 0 || !showAll">
                                    <div class="row">
                                        <div class="col-3 col-lg-2 product_img">
                                            <img :src="d.product.main_image" class="img-fluid">
                                        </div>
                                        <div class="col-7 col-lg-7 product_info">
                                            <div class="product_name">{{ d.product.name }}</div>
                                            <div class="product_price">{{ currency(d.unit_price) }} x {{ d.qty }} barang</div>
                                            <div class="product_sub_total" v-if="$root.window.mobile">
                                                {{ currency(d.unit_price * d.qty) }}
                                            </div>
                                        </div>
                                        <div class="col-3 d-flex my-auto" v-if="!$root.window.mobile">
                                            <div class="product_sub_total">
                                                {{ currency(d.unit_price * d.qty) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="block-content py-10 text-center border-top border-bottom border-2x" @click="showAll = !showAll" v-if="cart.length > 1">
                            <template v-if="showAll">
                                <span class="font-size-md font-w700">Tampilkan Semua</span>
                                <i class="fi-rs-angle-small-down"/>
                            </template>
                            <template v-else>
                                <span class="font-size-md font-w700">Tampilkan Lebih Sedikit</span>
                                <i class="fi-rs-angle-small-up"/>
                            </template>
                        </div>
                        <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                            <div class="form-group">
                                <label for="field-name">Metode Pengiriman</label>
                                <v-select label="name"  :filterable="false"
                                :options="ShippingOption"
                                v-model="shippingSelected"
                                :class="{'is-invalid' : errors.area_id }">
                                    <template slot="option" slot-scope="option">
                                        <div class="d-center">
                                            {{ option.name}}
                                            </div>
                                        </template>
                                        <template slot="selected-option" slot-scope="option">
                                        <div class="selected d-center">
                                            {{ option.name}}
                                        </div>
                                    </template>
                                </v-select>
                                <div v-if="errors.area_id" class="text-danger font-size-sm">{{ errors.area_id[0] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="content-heading pt-0 mb-0 border-0 font-size-md">
                        Rincian Belanja
                    </div>
                    <div class="block block-bordered block-shadow block-rounded">
                        <div class="block-content block-content-full">
                            <div class="d-flex justify-content-between mb-5">
                                <div class="font-size-sm">Total Harga ({{ cart.length }} barang)</div>
                                <div class="font-size-sm  font-w600">{{ currency(totalHarga) }}</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="font-size-sm">Biaya Kirim</div>
                                <div class="font-size-sm  font-w600">{{ currency(shippingCost) }}</div>
                            </div>
                        </div>
                        <div class="block-content block-content-full border-top border-2x">
                            <button type="button" class="btn btn-lg btn-primary btn-noborder btn-block" @click="payment">
                                Lanjut Ke Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <b-modal id="paymentMethod" size="md" rounded body-class="p-0" centered hide-footer hide-header>
            <div class="block block-rounded  block-transparent mb-0">
                <div class="block-header border-bottom border-3x">
                    <h3 class="block-title">
                        Pembayaran
                    </h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" @click="$bvModal.hide('paymentMethod')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content px-0">
                    <ul class="payment-method-list">
                        <li class="list_item" v-for="index in 6" :key="index">
                            <label class="css-control css-control-primary css-radio payment-wrap">
                                <div class="payment__logo">
                                    <img src="https://ecs7.tokopedia.net/img/toppay/sprites/bca.png" class="img-fluid" alt="gateway">
                                </div>
                                <div class="payment__content">
                                    <div class="payment__text">
                                        <div class="mr-4 font-weight-bold">Transfer Bank BCA</div>
                                    </div>
                                </div>
                                <div class="payment__icon">
                                    <input type="radio" class="css-control-input" name="radio-group2">
                                    <span class="css-control-indicator"></span>
                                </div>
                            </label>
                        </li>
                    </ul>
                </div>
                <div class="block-content block-content-full text-right border-top p-10">
                    <button type="button" class="btn btn-danger" @click.prevent="$bvModal.hide('paymentMethod')">
                        <i class="si si-close mr-1"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="si si-check mr-1"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </b-modal>
    </BaseLayout>
</template>
<style>
.cart-item{
    padding : 10px 0;
    border-bottom: 2px solid #eaeaea !important;
    margin: 0;
}

.product-cart .cart-item:last-child {
    border-bottom: none !important;
}

.cart-item .product-img{
    max-width: 100px;
}
.cart-item img{
    width: 80px;
    height: 80px;
    border-radius: 1rem;
}

.cart-item .product_info .product_name{
    font-size: 1.15rem;
    font-weight: 500;
}

.cart-item .product_info .product_price{
    font-size: 1rem;
    font-weight: 700;
}

.cart-item .product_sub_total{
    font-size: 1.1rem;
    font-weight: 700;
}


.section-label {
    font-size: 18px;
    margin-bottom: 20px;
    line-height: 20px;
}

ul.payment-method-list {
    margin: 16px 0px;
    padding: 0px;
    list-style: none;
}

ul.payment-method-list li.list_item {
    display: flex;
    position: relative;
    padding: 16px 16px 16px 0px;
    margin-left: 16px;
    border-bottom: 0.5px solid rgb(224, 224, 224);
    -webkit-box-align: center;
    align-items: center;
}

ul.payment-method-list li.list_item .payment-wrap{
    display: flex;
    -webkit-box-pack: start;
    justify-content: flex-start;
    -webkit-box-align: center;
    align-items: center;
    flex: 1 1 0%;
    min-height: 38px;
    max-width: 100%;
    position: relative;
    cursor: pointer;
}

.payment-wrap .payment__logo {
    width: 52px;
    padding-right: 12px;
    text-align: center;
    line-height: 0;
}

.payment-wrap .payment__logo img {
    width: auto;
    height: auto;
    min-width: 32px;
    max-width: 100%;
}

.payment-wrap .payment__content {
    display: flex;
    flex: 1 1 0%;
    flex-direction: column;
    padding-right: 8px;
    text-overflow: ellipsis;
    overflow: hidden;
}

.payment-wrap .payment__content .payment__text{
    display: flex;
    -webkit-box-pack: start;
    justify-content: flex-start;
    -webkit-box-align: center;
    align-items: center;
}


.payment-wrap .payment__select {
    max-width: 50px;
    display: flex;
    -webkit-box-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    align-items: center;
}

.payment-wrap .payment__icon {
    padding: 0px;
    display: flex;
    -webkit-box-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    align-items: center;
    min-width: 8px;
    min-height: 13px;
    color: rgb(66, 181, 73);
    font-size: 12px;
    line-height: 16px;
    white-space: nowrap;
    text-overflow: ellipsis;
    max-width: 168px;
    opacity: 1;
}

</style>

<script>
import BaseLayout from "@/layouts/frontend/BaseLayout";
import ProductQty from "@/components/Product/ProductQty.vue";
import vSelect from 'vue-select';

import ShippingDestination from './ShippingDestination.vue';
export default {
    components: {
        BaseLayout,
        ProductQty,
        vSelect,
        ShippingDestination
    },
    props : {
        cart : Array,
        address : Object,
        errors : Object,
    },
    data() {
        return {
            product: [],
            var1: null,
            var2: null,
            selected : [],
            selectAll : false,
            destination : this.address,
            shippingSelected : {
                    name : 'Instan Rp 2000',
                    price : 2000
                },
            ShippingOption :[
                {
                    'name' : 'Instan Rp 2000',
                    'price' : 2000
                }
            ],
            showAll : true,
        }
    },
    computed :{
        totalProduct () {
            var qty = 0;
            this.selected.forEach((value, index) => {
                qty += value.qty;
            });
            return qty;
        },
        totalHarga () {
            var total = 0;
            this.cart.forEach((value, index) => {
                total += value.unit_price * value.qty;
            });
            return total;
        },
        shippingCost(){
            if(this.shippingSelected){
                return this.shippingSelected.price;
            }
            return 0;
        }
    },
    watch : {
        selectAll: function(val){
            this.selected = [];
            if(val){
                this.$page.props.cart.forEach((value, index) => {
                    this.selected.push(value)
                });
            }
        },
    },
    methods :{
        async updateCart(id, qty){
            let form = this.$inertia.form({
                id : id,
                qty : qty
            }); 
            form.post(this.route('cart.update'), {
                preserveScroll: true,
            });
        },
        payment(){
            const form = this.$inertia.form({
                products : this.cart,
                shipping : this.shippingSelected,
                delivery_id : this.destination.id,
                sub_total : this.totalHarga,
            });

            form.post(this.route('checkout.payment'), {
                preserveScroll: true,
                onSuccess: () => {
                },
            });
        }
        
    }

 }

</script>
