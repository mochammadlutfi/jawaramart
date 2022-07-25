<template>
    <BaseLayout title="Keranjang Belanja">
    
        <div class="checkbox-row" v-if="$root.window.mobile">
            <div class="cart-checkbox-head">
                <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll">Pilih Semua</b-form-checkbox>
            </div>
        </div>

        <div class="content cart-content">
            <h2 class="content-heading" v-if="!$root.window.mobile">
                Keranjang
            </h2>
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-header" v-if="!$root.window.mobile">
                        <div class="wrap-left">
                            <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll">Pilih Semua</b-form-checkbox>
                        </div>
                        <div class="wrap-right">
                            <button class="btn btn-sm btn-danger" type="button" @click="removeSelected" v-if="selected.length">
                                <i class="si si-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                    
                    <div class="cart-item" v-for="(value, index) in $page.props.cart" :key="index">
                        <div class="cart-item-check">
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" :id="value.id" type="checkbox" :value="value" v-model="selected">
                                <label class="custom-control-label" :for="value.id"></label>
                            </label>
                        </div>
                        <div class="cart-item-product">
                            <div class="cart-product_img">
                                <img :src="value.product.main_image" class="img-fluid">
                            </div>
                            <div class="cart-product_info">
                                <div class="product_name">
                                    <a :href="route('product.show', { product: value.product.slug })">
                                            {{ value.product.name }}
                                    </a>
                                </div>
                                <div class="product_price">
                                        {{ currency(value.unit_price) }}
                                </div>
                            </div>
                        </div>
                        <template v-if="!$root.window.mobile">
                        <div class="cart-item-qty" >
                            <product-qty @change="(qty) => updateCart(value.id, qty)" :value="value.qty" :size="'sm'" ></product-qty>
                        </div>
                        <div class="cart-item-subtotal">
                            <div class="product_sub_total">
                                {{ currency(value.unit_price * value.qty) }}
                            </div>
                        </div>
                        <div class="cart-item-remove">
                            <button  class="btn btn-danger btn-sm" @click="remove(value.id)">
                                <i class="si si-trash"></i>
                            </button>
                        </div>
                        </template>
                    </div>
                </div>
                <div class="col-lg-4" v-if="!$root.window.mobile">
                    <div class="block block-bordered block-shadow block-rounded">
                        <div class="block-content block-content-full">
                            <h6 class="font-size-h5">Ringkasan belanja</h6>
                            <div class="d-flex justify-content-between">
                                <div class="font-size-md font-w600">Total Belanja ({{ totalProduct }} barang)</div>
                                <div class="font-size-md">{{ currency(totalOrder) }}</div>
                            </div>
                            <hr/>
                            <button type="button" class="btn btn-lg btn-primary btn-noborder btn-block" :disabled="!selected.length" @click="checkout">
                                Lanjut Ke Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template v-if="$root.window.mobile">
            <div class="cart-floating" data-unify="Floating">
                <div class="wrapper">
                    <div class="action-wrapper">
                        <div class="total">
                            <p color="rgba(49, 53, 59, 0.68)" class="css-s8aquh-unf-heading e12ykf338">Total Harga</p>
                            <p color="rgba(49, 53, 59, 0.96)" class="text__price css-1qging0-unf-heading e12ykf338">{{ currency(totalOrder) }}</p>
                        </div>
                        <div role="button" tabindex="0" data-testid="cartCheckoutBTNWrapper">
                            <button type="button" class="btn btn-lg btn-primary btn-noborder btn-block" :disabled="!selected.length" @click="checkout">
                                Beli ({{totalProduct}})
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </BaseLayout>
</template>
<style>
.cart-header {
    border-bottom : 3px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    padding-bottom: 10px;
}
@media only screen and (max-width: 768px) {
    .content.cart-content {
        padding: 52px 12px 12px 12px !important;
    }

    .cart-item .product_info {
        padding: 20px;
    }

    .cart-floating {
        position: fixed;
        width: 100%;
        z-index: 10;
        box-shadow: none;
        max-width: 500px;
        left: initial;
        bottom: 0px;
        background: var(--N0,#FFFFFF);
        padding: 12px 16px;
        box-shadow: 0 1px 4px 0 #364d68bd;
    }

    .cart-floating .wrapper {
        display: block;
        width: 100%;
        bottom: 0px;
        background: var(--N0,#FFFFFF);
        color: rgba(0, 0, 0, 0.38);
    }

    .cart-floating .wrapper .action-wrapper {
        display: flex;
        width: 100%;
        -webkit-box-pack: justify;
        justify-content: space-between;
    }
    
    .cart-floating .total p{
        display: block;
        position: relative;
        font-weight: 400;
        font-family: "Open Sauce One", -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: rgba(49, 53, 59, 0.68);
        letter-spacing: 0.1px;
        text-decoration: initial;
        margin: 0px;
    }
}


.cart-content {
    padding : 0 !important;
}
.checkbox-row {
    width: 100%;
    box-shadow: rgb(0 0 0 / 5%) 0px 5px 1px -3px;
    background-color: white;
    max-width: 500px;
    z-index: 39;
    top: 51px;
    position: fixed;
    margin: 0px auto;
    transition: all 600ms cubic-bezier(0.63, 0.01, 0.29, 1) 0s;
    transform: translateY(0px);
}
.checkbox-row .cart-checkbox-head {
    padding-top: 15px;
    padding-bottom: 15px;
}
.checkbox-row .cart-checkbox-head {
    display: flex;
    flex-wrap: wrap;
    margin-left: -4px;
    margin-right: -4px;
    box-sizing: border-box;
    -webkit-box-align: center;
    align-items: center;
    padding-left: 16px;
    padding-right: 16px;
}


.cart-item{
    padding : 10px 0;
    border-bottom: 2px solid #eaeaea !important;
    margin: 0;
}
.cart-item:last-child {
    border-bottom: none !important;
}

.cart-item .product_sub_total{
    font-size: 1.1rem;
    font-weight: 700;
}
</style>

<style lang="scss">
.cart-item {
    display : flex;
    
    .cart-item-check {
        margin-top: auto;
        margin-bottom: auto;
    }

    .cart-item-product  {
        display: flex;
        width: 60%;

        .cart-product_img img{
            width: 80px;
            height: 80px;
            border-radius: 1rem;
        }

        .cart-product_info {
            margin-top: auto;
            margin-bottom: auto;

            .cart-product_title {
                font-size: 1.15rem;
                font-weight: 500;
                a {
                    font-size: 14px;
                    color: rgba(49,53,59,0.96);
                    width: calc(100% - 8px);
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
            }
            .cart-product_price {
                font-size: 1rem;
                font-weight: 700;
            }
        }
    }
    .cart-item-qty {
        width : 115px;
        margin-top: auto;
        margin-bottom: auto;
        
    }
    .cart-item-subtotal {
        margin-top: auto;
        margin-bottom: auto;
        
    }
    .cart-item-remove {
        margin-top: auto;
        margin-bottom: auto;
        
    }
}
</style>
<script>
import BaseLayout from "@/layouts/frontend/BaseLayout";
import ProductQty from "@/components/Product/ProductQty.vue";
export default {
    components: {
        BaseLayout,
        ProductQty
    },
    data() {
        return {
            product: [],
            var1: null,
            var2: null,
            selected : [],
            selectAll : false,
            voucher : [],
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
        totalOrder () {
            var order = 0;
            this.selected.forEach((value, index) => {
                order += value.unit_price * value.qty;
            });
            return order;
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
        checkout(){
            const form = this.$inertia.form({
                cart : this.selected,
            });

            form.post(this.route('checkout.shipping'), {
                preserveScroll: true,
            });
        },
        removeSelected(){
            this.$swal.fire({
                icon: 'warning',
                title: 'Kamu Yakin?',
                text: `${ this.selected.length } produk akan dihapus!`,
                showCancelButton: true,
                cancelButtonText : 'Tidak, Batal',
                confirmButtonText: "Ya, Hapus",
                confirmButtonColor: '#3f9ce8',
                cancelButtonColor: '#af1310',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.post(this.route('cart.removeSelected'), {
                            ids : this.selected,
                        }, {
                        preserveScroll: true,
                        resetOnSuccess: false,
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
                            this.$swal.Close();
                            return this.$swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: "Produk berhasil dihapus dari keranjang",
                                showCancelButton: false,
                                showConfirmButton: false,
                            });
                        },
                        onError:() => {
                            this.$swal.Close();
                        }
                    })
                }
            })
        },
        remove(id){
            this.$swal.fire({
                icon: 'warning',
                title: 'Kamu Yakin?',
                text: `Semua produk yang dipilih akan dihapus!`,
                showCancelButton: true,
                cancelButtonText: 'Tidak, Batal!',
                confirmButtonText: 'Ya, Hapus!',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('cart.remove', id), {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.$swal.Close();
                            return this.$swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: "Produk berhasil dihapus dari keranjang",
                                showCancelButton: false,
                                showConfirmButton: false,
                            });
                        },
                    })
                }
            })
        }
    }

 }

</script>
