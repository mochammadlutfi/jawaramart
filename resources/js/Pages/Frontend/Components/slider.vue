<template>
    <div class="content" id="home-slider">
        <template v-if="slider">
            <Flicking ref="home-slider" :options="{ circular: true, align : 'prev'}" :plugins="plugins">
                <div class="block-slider" v-for="(slide, index) in slider" :key="index">
                    <a href="">
                        <img :src="slide.image_url" class="img-fluid"/>
                    </a>
                </div>
                <div slot="viewport" class="flicking-pagination"></div>
                <span slot="viewport" class="flicking-arrow-prev slider-prev"></span>
                <span slot="viewport" class="flicking-arrow-next slider-next"></span>
            </Flicking>
        </template>
    </div>
</template>
<script>


import axios from 'axios'
import { Flicking } from "@egjs/vue-flicking";
import { Arrow, AutoPlay, Pagination } from "@egjs/flicking-plugins";
export default {
    name : 'HomeSlider',
    components: {
        Flicking,
    },
    data() {
        return {
            slider : null,
            plugins: [
                new Pagination({ parentEl : document.getElementById("home-slider") ,type: 'bullet' }),
                new Arrow(new Arrow({parentEl : document.getElementById("home-slider"), prevElSelector : '.slider-prev', nextElSelector : '.slider-next' }))
            ]
        }
    },
    mounted(){
        this.getSlider();
        // console.log(document.body);
    },
    methods : {
        async getSlider(){
            var self = this;
            await axios.get(this.route("api.slider"))
            .then(function (response) {
                var resp = response.data;
                self.slider = resp.data;
            })
            .catch(function (error) {
                
            })
        },
    },
}
</script>