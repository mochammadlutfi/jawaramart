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
<style>
.block-slider{
    width: 100%;
    border-radius: 1rem;
}
.block-slider img{
    border-radius: 1rem;
}
.flicking-pagination {
  position: absolute;
  left: 0;
  bottom: 10px;
  width: 100%;
  text-align: center;
  z-index: 2;
}

.flicking-pagination-bullets,
.flicking-pagination-scroll {
  font-size: 0;
}

.flicking-pagination-scroll {
  left: 50%;
  transform: translate(-50%);
  white-space: nowrap;
  overflow: hidden;
}

.flicking-pagination-scroll .flicking-pagination-slider {
  transition: .2s transform;
}
.flicking-pagination-scroll.flicking-pagination-uninitialized .flicking-pagination-slider,
.flicking-pagination-scroll.flicking-pagination-uninitialized .flicking-pagination-bullet {
  transition: none;
}

.flicking-pagination-bullet {
  display: inline-block;
  width: 8px;
  height: 8px;
  margin: 0 4px;
  border-radius: 50%;
  background-color: rgb(10 10 10 / 10%);
  cursor: pointer;
  font-size: 1rem;
}

.flicking-pagination-scroll .flicking-pagination-bullet {
  vertical-align: middle;
  position: relative;
  transition: .2s transform;
}

.flicking-pagination-bullet-active {
  background-color: #f2a65e;
}

.flicking-pagination-scroll .flicking-pagination-bullet {
  vertical-align: middle;
  position: relative;
  transition: .2s transform,.2s left;
  transform: scale(0);
}

.flicking-pagination-scroll .flicking-pagination-bullet-active {
  transform: scale(1);
}

.flicking-pagination-scroll .flicking-pagination-bullet-prev,
.flicking-pagination-scroll .flicking-pagination-bullet-next {
  transform: scale(0.66);
}

.flicking-pagination-scroll .flicking-pagination-bullet-prev2,
.flicking-pagination-scroll .flicking-pagination-bullet-next2 {
  transform: scale(0.33);
}
</style>