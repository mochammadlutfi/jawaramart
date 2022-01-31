<template>
    <BaseLayout title="Product Detail">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Detail Product
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-danger" >
                        <i class="si si-trash"></i>
                        Delete
                    </button>
                    <Link class="btn btn-sm btn-secondary" :href="route('admin.product.edit', {id : data.id})" type="button">
                        <i class="si si-plus"></i>
                        Edit
                    </Link>
                </div>
            </div>
            <div class="block block-rounded block-shadow-2 block-bordered mb-5">
                <div class="block-content block-content-full">
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
                            <div class="form-material">
                                <label for="field-name">Name</label>
                                <input type="text" class="form-control-lg form-control-plaintext" v-model="data.name" disabled>
                            </div>
                            <div class="form-material">
                                <label for="field-description">Description</label>
                                <textarea class="form-control form-control-plaintext" rows="8" id="field-description" v-model="data.description" disabled></textarea>
                            </div>
                            <div class="form-material">
                                <label for="field-name">Category</label>
                                <input type="text" class="form-control-lg form-control-plaintext" v-model="data.category.name" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>
<script>

import { Link } from '@inertiajs/inertia-vue';
import BaseLayout from '../Layouts/Authenticated.vue';
import { Flicking } from "@egjs/vue-flicking";
import { Sync } from "@egjs/flicking-plugins";
export default {
    components : {
        BaseLayout,
        Link,
        Flicking,
    },
    props : {
        errors : Object,
        data : Object,
    },
    data() {
        return {
            plugins: []
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
    }
}
</script>
<style>
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
