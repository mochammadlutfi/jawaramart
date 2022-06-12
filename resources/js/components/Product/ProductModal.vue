<template>
    <b-modal v-model="open" size="lg" content-class="rounded" no-close-on-backdrop body-class="p-0" hide-footer hide-header>
        <div class="block block-rounded block-transparent mb-0">
            <div class="block-content">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="product-img-detail">
                            <div class="product-img-nav">
                                <flicking ref="flicking1" :options="{ bound: true, bounce: 30, moveType: 'freeScroll', align : 'prev' }" :class="{ 'd-none' : $root.window.mobile }">
                                    <div class="flicking-panel thumb has-background-primary" v-for="(thumb, iThumb) in data.images" :key="iThumb">
                                        <img class="thumb-image" :src="asset(thumb.path)" />
                                    </div>
                                </flicking>
                            </div>
                            <flicking ref="flicking0" :options="{ bounce: 30, disableOnInit: true}" :plugins="plugins">
                                <template v-if="data.images.length">
                                    <div class="flicking-panel has-background-primary" v-for="(image, index) in data.images" :key="index">
                                        <img class="panel-image img-fluid" :src="asset(image.path)" />
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="flicking-panel has-background-primary">
                                        <img class="panel-image img-fluid" :src="asset('media/placeholder/product.png')" />
                                    </div>
                                </template>
                            </flicking>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="product-info">
                            <h2 class="product-title">
                                {{ data.name }}
                            </h2>
                            <div class="product-meta">
                                <div class="">
                                    Terjual <span>{{ data.sale_count }}</span>
                                </div>
                                <div class="">
                                    <!-- <template v-if="data.reviews.length > 0">
                                        {{ data.reviews.length }} Ulasan
                                    </template> -->
                                    <!-- <template v-else> -->
                                        Belum Ada Ulasan
                                    <!-- </template> -->
                                </div>
                            </div>
                            <div class="product-price">
                                <div class="product-price_final">
                                    {{ currency(data.sell_price) }}
                                </div>
                            </div>
                        </div>
                        <div class="product-variant" v-if="data.var1_value">
                            <div class="product-variant_selected">
                                <span class="label">{{ data.var1_name }} : </span>
                                <span>{{ var1 }}</span>
                            </div>
                            <div class="product-variant_list">
                                <label :for="'var-'+index" class="radio-chip b-outline b-secondary" v-for="(value, index) in JSON.parse(data.var1_value)" :key="index">
                                    <input type="radio" :id="'var-'+index" name="var1"  v-model="var1" :value="value" :checked="{ 'checked': index === 0 }">
                                    <span>{{ value }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="product-variant" v-if="data.var2_value">
                            <div class="product-variant_selected">
                                <span class="label">{{ data.var2_name }} : </span>
                                <span>{{ var2 }}</span>
                            </div>
                            <div class="product-variant_list">
                                <label :for="'var2-'+index" class="radio-chip b-outline b-secondary" v-for="(value, index) in JSON.parse(data.var2_value)" :key="index">
                                    <input type="radio" :id="'var2-'+index" name="var2"  v-model="var2" :value="value" :checked="{ 'checked': index === 0 }">
                                    <span>{{ value }}</span>
                                </label>
                            </div>
                        </div>
                        <template v-if="!$root.window.mobile">
                            <div class="py-20">
                                <product-qty @change="(qty) => product.qty = qty"></product-qty>
                            </div>

                            <button @click="addCart" type="button" class="btn btn-primary btn-lg btn-noborder btn-block">
                                <i class="fi-rs-shopping-bag-add"></i>
                                Masukan Keranjang
                            </button>

                            <div class="row mt-3">
                                <div class="col-lg-6">
                                    <button type="button" class="btn btn-wishlist btn-lg btn-block" :class="[{'active' : data.is_wishlist }]" @click="addWishlist">
                                        <svg :fill="data.is_wishlist == 1 ? 'currentColor' : 'none'"  viewBox="0 0 28 28" class="text-2xl" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="1"
                                                d="M11.995 7.23319C10.5455 5.60999 8.12832 5.17335 6.31215 6.65972C4.49599 8.14609 4.2403 10.6312 5.66654 12.3892L11.995 18.25L18.3235 12.3892C19.7498 10.6312 19.5253 8.13046 17.6779 6.65972C15.8305 5.18899 13.4446 5.60999 11.995 7.23319Z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Whishlist
                                    </button>
                                </div>
                                <div class="col-lg-6">
                                    <button type="button" class="btn btn-share btn-lg btn-block">
                                        <i class="fi fi-rs-share"></i>
                                        Share
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
import { Flicking } from "@egjs/vue-flicking";
import { Sync } from "@egjs/flicking-plugins";
import ProductQty from "@/components/Product/ProductQty.vue";
export default {
    components: {
        Flicking,
        ProductQty,
    },
    props : {
        data : Object,
    },
    data() {
        return {
            plugins: [],
            product: {
                product_id : null,
                variant_id : null,
                price : null,
                qty : 1,
            },
            var1: null,
            var2: null,
            open : false,
        }
    },
    created () {
        this.product.product_id = this.data.id;
        if(this.data.var1_value){
            this.var1 = JSON.parse(this.data.var1_value)[0];
        }

        if(this.data.var2_value){
            this.var2 = JSON.parse(this.data.var2_value)[0];
        }

        if(this.data.variant.length == 1){
            this.product.variant_id = this.data.variant[0].id;
            this.product.price = this.data.variant[0].sell_price;
        }
    },
    mounted() {
        this.plugins = [new Sync({
        type: "index",
        synchronizedFlickingOptions: [
            {
                flicking: this.$refs.flicking0,
                isSlidable: false
            },
            {
                flicking: this.$refs.flicking1,
                isClickable: true,
                activeClass: "active"
            }
        ]
        })];
    },
    methods:{
        openModal(e){
            // alert('asdsa');
            this.open = true;
            // this.$refs.productModal.show();
        },
        addWishlist(){
            if(this.$page.props.auth.user){
                let form = this.$inertia.form({
                    'product_id' : this.data.id,
                    'ref' : this.route().current()
                });
                form.post(this.route('user.wishlist.store'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        return this.$swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text : (this.data.is_wishlist) ? 'Produk berhasil disimpan di wishlist' : 'Produk berhasil dihapus dari wishlist',
                            showCancelButton: false,
                            showConfirmButton: false,
                            timer: 3000,
                        });
                    },
                });
            }else{
                window.location = this.route('login')
            }
        },

        addCart(){
            if(this.$page.props.auth.user){
                let form = this.$inertia.form(this.product);
                form.post(this.route('cart.store'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        return this.$swal.fire({
                            icon: 'success',
                            title: 'Berhasil ditambahkan!',
                            showCancelButton: false,
                            showConfirmButton: false,
                            timer: 3000,
                        });
                    },
                });
            }else{
                // alert('as');
                window.location = this.route('login')
            }
        }
    }
}

</script>

<style>