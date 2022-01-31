<template>
    <BaseLayout>
        <div class="content">
          <h2 class="content-heading">Keranjang</h2>
          <div class="row">
              <div class="col-lg-8">
                  <div class="border-bottom border-3x py-15">
                      <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll">Pilih Semua</b-form-checkbox>
                  </div>
                <div class="row cart-item" v-for="(value, index) in $page.props.cart" :key="index">
                    <div class="col d-flex px-0">
                        <div class="my-auto">
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" :id="value.id" type="checkbox" :value="value" v-model="selected">
                                <label class="custom-control-label" :for="value.id"></label>
                            </label>
                        </div>
                        <img :src="value.product.main_image" class="img-fluid">
                    </div>
                    <div class="col-lg-6 product_info">
                        <div class="product_name">
                            <a href="">
                                    {{ value.product.name }}
                            </a>
                        </div>
                        <div class="product_price">
                                {{ currency(value.unit_price) }}
                        </div>
                    </div>
                    <div class="col-2 d-flex my-auto px-2">
                        <product-qty @change="(qty) => updateCart(value.id, qty)" :value="value.qty" :size="'sm'" ></product-qty>
                    </div>
                    <div class="col d-flex my-auto px-0 col d-flex justify-content-center my-auto px-0">
                        <div class="product_sub_total">
                            {{ currency(value.unit_price * value.qty) }}
                        </div>
                    </div>
                </div>
              </div>
              <div class="col-lg-4">
                  <div class="block block-bordered block-shadow block-rounded">
                      <div class="block-content block-content-full">
                          <h6 class="font-size-h5">Ringkasan belanja</h6>
                          <div class="d-flex justify-content-between">
                              <div class="font-size-md font-w600">Total Belanja ({{ totalProduct }} barang)</div>
                              <div class="font-size-md">{{ currency(totalOrder) }}</div>
                          </div>
                          <hr/>
                          <button type="button" class="btn btn-lg btn-primary btn-noborder btn-block" :disabled="!selected.length">
                              Lanjut Ke Pembayaran
                          </button>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </BaseLayout>
</template>
<style>
.cart-item{
    padding : 10px 0;
    border-bottom: 2px solid #eaeaea;
    margin: 0;
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
        
    }

 }

</script>
