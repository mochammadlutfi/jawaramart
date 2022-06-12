<template>
    <div class="content category-slide">
        <div class="content-heading">
            <h3 class="title">Kategori</h3>
            <div class="slider-arrow slider-arrow-2 flex-right" :class="{'d-none' : $root.window.mobile}">
                <span class="slider-btn slider-prev is-outside">
                    <i class="fi-rs-arrow-small-left"></i>
                </span>
                <span class="slider-btn slider-next is-outside">
                    <i class="fi-rs-arrow-small-right"></i>
                </span>
            </div>
        </div>
        <div class="row mx-0">
            <Flicking :options="{ circular: true, align: 'prev' }" :plugins="plugins">
            <a :href="route('product.category', { category_slug : data.slug})" class="category-box" v-for="(data, index) in category" :key="index">
                <div class="category-wrapper">
                    <figure class="img-hover-scale overflow-hidden">
                        <img :src="data.thumbnail_url" class="img-fluid">
                    </figure>
                    <h6>{{ data.name }}</h6>
                </div>
            </a>
            </Flicking>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Flicking } from "@egjs/vue-flicking";
import { Arrow } from "@egjs/flicking-plugins";
import "@egjs/flicking-plugins/dist/arrow.css";
export default {
    name : 'CategorySlide',
    components: {
        Flicking,
    },
    data() {
        return {
            category : null,
            plugins: [new Arrow({ parentEl: document.body, prevElSelector : '.slider-prev', nextElSelector : '.slider-next' })]
        }
    },
    mounted(){
        this.getCategory();
    },
    methods : {
        async getCategory(){
            const self = this;
            // const params = {
            //     category_id: id,
            // };
            await axios.get(this.route("api.product.category"))
            .then(function (response) {
                var resp = response.data;
                self.category = resp.data;
            })
            .catch(function (error) {
                
            })
        },
    },
}

</script>
<style>
.category-slide .content-heading{
    padding-top: 0;
    margin-bottom: 3px;
    border-bottom: none;
    display: flex;
    justify-content: space-between;
}

.category-slide .content-heading .title {
    font-size: 1.857142857rem;
    font-weight: 700;
    line-height: 38px;
    margin-bottom: 0px;
}

@media screen and (max-width: 768px){
    .category-slide .content-heading .title{
        font-size: 1.2rem;
        line-height: 21px;
    }
}
.category-box {
    align-content: stretch !important;
    box-shadow: 0 5px 5px rgb(33 37 41 / 5%);
    border: 1px solid #e4e7ed;
    border-radius: 0.5rem !important;
    text-align: center;
    max-width: 135px;
    margin: 5px;
}

.category-box .category-wrapper {
    padding:10px;
}

.category-box .category-wrapper h6{
    font-weight: 500;
    font-size: 1rem;
    margin-bottom: 10px;
}

@media (max-width: 576px) {
    .category-box {
        max-width: 80px;
        margin: 3px;
        box-shadow: 0 3px 8px rgb(33 37 41 / 5%);
    }
    .category-box .category-wrapper {
        padding:5px;
    }
    .category-box .category-wrapper figure{
        margin-bottom: 0px;
    }
    .category-box .category-wrapper h6{
        font-size: 10px;
        margin-bottom: 5px;
    }
}

.category-box img {
    border-radius: 0.5rem;
}
</style>