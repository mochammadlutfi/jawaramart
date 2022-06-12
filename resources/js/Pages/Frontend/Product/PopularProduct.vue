<template>
    <div class="content">
        <div class="content-heading pt-0 mb-3 border-0 d-flex justify-content-between">
            <h3 class="h3 mb-0">Popular Product</h3>
        </div>
        <div class="row mx-0">
            <div class="col-6 col-lg-2 col-md-2 mb-15 px-5" v-for="(data, index) in products" :key="index">
                <div class="product">
                    <div class="product-content">
                        <div class="product-img">
                            <a :href="route('product.show', { 'product' : data.slug })">
                                <img :src="data.main_image" class="img-fluid"/>
                            </a>
                        </div>
                        <div class="product-info">
                            <div class="product-title"><a :href="route('product.show', { 'product' : data.slug })">{{ data.name }}</a></div>
                            <div class="product-price">{{ currency(data.sell_price) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
export default {
    name : 'PopularProducts',
    components: {
    },
    data() {
        return {
            products : null,
        }
    },
    mounted(){
        this.data();
    },
    methods : {
        async data(){
            const self = this;
            // const params = {
            //     category_id: id,
            // };
            await axios.get(this.route("api.product.index"))
            .then(function (response) {
                var resp = response.data;
                self.products = resp.data;
            })
            .catch(function (error) {
                
            })
        },
    },
}
</script>