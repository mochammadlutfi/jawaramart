<template>
    <observer @on-change="onChange" class="product">
        <div class="product-content">
            <div class="product-img">
                <template v-if="loading">
                    <vue-skeleton-loader
                    type="rect"
                    :width="'100%'"
                    :height="'210px'"
                    animation="fade"
                    />
                </template>
                <template v-else>
                    <a :href="route('product.show', { 'product' : product.slug })">
                        <img :src="product.main_image" class="img-fluid"/>
                    </a>
                </template>
            </div>
            <div class="product-info">
                <div class="product-title"><a :href="route('product.show', { 'product' : product.slug })">{{ product.name }}</a></div>
                <div class="product-price">{{ currency(product.sell_price) }}</div>
                <div class="gutters-tiny row">
                    <div class="col-4">
                        <button class="btn btn-secondary  btn-sm btn-block" @click="destroy(product.id)">
                            <i class="si si-trash"></i>
                        </button>
                    </div>
                    <div class="col-8">
                        <button class="btn btn-secondary btn-sm btn-block" @click="moveCart">
                            + Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ProductModal :data="product" ref="productModal" />
    </observer>
</template>

<script>

import VueSkeletonLoader from 'skeleton-loader-vue';
import Observer from 'vue-intersection-observer';
import ProductModal from '@/components/Product/ProductModal.vue';
export default {
    name : 'ProductItem',
    props : ['product', 'id'],
    components: {
        Observer,
        VueSkeletonLoader,
        ProductModal
    },
    data() {
        return{
            loading: false,
            openModal : false,
        }
    },
    methods: {
        onChange(entry, unobserve) {
            if (entry.isIntersecting) {
                unobserve();
            }

            this.loading = entry.isIntersecting ? false : true;
        },
        moveCart(){
            // alert('asas');
            // this.openModal = true;
            this.$refs.productModal.openModal();
        },
        destroy(value){
            this.$inertia.delete(this.route('user.wishlist.delete', {id : this.id}),{
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
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: "Barang telah dihapus dari Wishlist.",
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer : 3000,
                    });
                },
                onError:() => {
                    this.$swal.close();
                }
            })
        }
    },
}
</script>
