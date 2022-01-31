<template>
    <BaseLayout>
    
        <Head>
            <title>Jual {{ data.name }}</title>
            <meta name="description" content="Your page description">
        </Head>

        <div class="content content-full">
            <div class="row">
                <div class="col-4">
                    <flicking ref="flicking0" :options="{ bounce: 30, disableOnInit: true}" :plugins="plugins">
                        <div class="flicking-panel has-background-primary" v-for="(image, index) in data.images" :key="index">
                            <img class="panel-image img-fluid" :src="asset(image.path)" />
                        </div>
                    </flicking>
                    <flicking ref="flicking1" :options="{ bound: true, bounce: 30, moveType: 'freeScroll', align : 'prev' }">
                        <div class="flicking-panel thumb has-background-primary" v-for="(thumb, iThumb) in data.images" :key="iThumb">
                            <img class="thumb-image" :src="asset(thumb.path)" />
                        </div>
                    </flicking>
                </div>
                <div class="col-8">
                    <div class="product-info">
                        <h1 class="product-title">
                            {{ data.name }}
                        </h1>
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
                    <div class="py-20">
                        <product-qty @change="(qty) => product.qty = qty"></product-qty>
                    </div>
                    <button @click="addCart" type="button" class="btn btn-primary btn-lg btn-noborder">
                        <i class="fi-rs-shopping-cart"></i>
                        Masukan Keranjang
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-body">
            <div class="content content-full">
                <div class="block">
                    <ul class="nav nav-tabs nav-tabs-alt">
                        <li class="nav-item">
                            <a class="nav-link active" href="#product-description">Description
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#product-reviews">Review
                            </a>
                        </li>
                    </ul>
                    <div class="block-content block-content-full" id="product-description">
                        {{ data.description }}
                    </div>
                    <div class="block-content block-content-full" id="product-reviews">
                        <div class="py-2">
                            <h4 class="h4 mb0">Reviews</h4>
                        </div>
                        <template v-if="data.reviews.length > 0">
                        </template>
                        <template v-else>
                            <div class="border">
                                Belum ada ulasan.
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from "@/layouts/frontend/BaseLayout";
import { Flicking } from "@egjs/vue-flicking";
import { Sync } from "@egjs/flicking-plugins";
import ProductQty from "@/components/Product/ProductQty.vue";
import { Head } from '@inertiajs/inertia-vue';
import axios from 'axios';
export default {
    components: {
        BaseLayout,
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
        // async addCart(){
        //     await axios.post(this.route('cart.store'), this.product)
        //     .then(function (response) {
        //         var resp = response.data;
        //         // this.$page.props.cart
        //     })
        //     .catch(function (error) {
                
        //     })
        //     if(this.$page.props.auth.user){
        //         // console.log('coba');
        //         // console.log();
        //         // alert('Tambah Cart');
        //     }else{
        //         // alert('as');
        //         window.location = this.route('login')
        //     }
        // },

        addCart(){
            if(this.$page.props.auth.user){
                let form = this.$inertia.form(this.product);
                form.post(this.route('cart.store'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        return this.$swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `Product Saved Successfully!`,
                            showCancelButton: false,
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

.product-variant{
    padding: 24px 0;
}

.flicking-panel {
    width: 100%;
    height: 100%;
    display: flex;
    border-radius: 5px;
    margin-right: 10px;
    margin-bottom: 10px;
    align-items: flex-end;
    padding: 30px 20px;
    box-sizing: border-box;
    position: relative;
}
.flicking-viewport {
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    position: relative;
    overflow: hidden;
}
.flicking-camera {
    width: 100%;
    height: 100%;
    display: flex;
    position: relative;
    flex-direction: row;
    z-index: 1;
}

.panel-image {
    top: -100%;
    bottom: -100%;
    width: 100%;
    margin: auto;
}

.flicking-panel.thumb.active {
    border: 2px solid #48c78e;
}

.flicking-panel.thumb {
    padding: 0;
    width: 20%;
    height: 20%;
    margin-bottom: 0;
    cursor: pointer;
}
.flicking-panel .thumb-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>