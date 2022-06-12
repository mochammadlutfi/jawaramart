<template>
    <div class="block-content px-0">
        <h2 class="font-size-h5">Ulasan</h2>
        <div class="row">
            <div class="col-lg-6" v-if="data">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <button class="rating_circle">4.1</button>
                        <b-form-rating
                            id="rating-readonly"
                            value="3.6536"
                            readonly
                            variant="warning"
                            precision="3"
                            ></b-form-rating>
                    </div>
                    <div class="col-md-8">
                        <div class="rating-detail">
                            <div class="rating-review">
                                <i class="fas fa-star"></i>
                                <span class="font-w700">5</span>
                            </div>
                            <b-progress :value="60" :max="total" variant="warning"></b-progress>
                        </div>
                        <div class="rating-detail">
                            <div class="rating-review">
                                <i class="fas fa-star"></i>
                                <span class="font-w700">4</span>
                            </div>
                            <b-progress :value="60" :max="total" variant="warning"></b-progress>
                        </div>
                        <div class="rating-detail">
                            <div class="rating-review">
                                <i class="fas fa-star"></i>
                                <span class="font-w700">3</span>
                            </div>
                            <b-progress :value="60" :max="total" variant="warning"></b-progress>
                        </div>
                        <div class="rating-detail">
                            <div class="rating-review">
                                <i class="fas fa-star"></i>
                                <span class="font-w700">2</span>
                            </div>
                            <b-progress :value="60" :max="total" variant="warning"></b-progress>
                        </div>
                        <div class="rating-detail">
                            <div class="rating-review">
                                <i class="fas fa-star"></i>
                                <span class="font-w700">1</span>
                            </div>
                            <b-progress :value="10" :max="total" variant="warning"></b-progress>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12" v-else>
                <div class="block block-bordered block-rounded">
                    <div class="block-content block-content-full text-center">
                        <div class="font-size-h4 font-w700">
                            Belum ada ulasan untuk produk ini
                        </div>
                        <div class="nice-copy">
                            Jadilah yang pertama membeli produk ini dan memberikan ulasan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name : 'product-review',
    data(){
        return {
            rating : 4.1,
            total : 100,
        }
    },
    props:{
        data : Object,
    }
}
</script>
<style lang="scss">
.rating-detail {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;

    .rating-review {
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-flex: 1;
        -ms-flex: 1 0 0px;
        flex: 1 0 0;
        -webkit-box-ordinal-group: 2;
        -ms-flex-order: 1;
        order: 1;

        i {
            color: #fdb813;
        }
    }

    .progress {
        -webkit-box-ordinal-group: 3;
        -ms-flex-order: 2;
        order: 2;
        background-color: #f1f1f1;
        border-radius: 4px;
        overflow: hidden;
        height: 10px;
        margin: 0 8px;
        -webkit-box-flex: 8;
        -ms-flex: 8 0 0px;
        flex: 8 0 0;
    }

    .rating-detail_bar {
        background-color: #fdb813;
        border-radius: 4px;
        display: block;
        height: 6px;
        margin-bottom:5px;
    }
}
.rating_circle{
    width: 60px;
    height: 60px;
    border-radius: 70px;
    border: 1px none;
    font-size: 35px;
  }

  .rating_text{
      margin-top: 38px;
  }
  .stars-outer {
  display: inline-block;
  position: relative;
    font-family: "Font Awesome 5 Free";
  font-size: 20px;
  letter-spacing: 5px;
}

.stars-outer::before {
    content: "\f005 \f005 \f005 \f005 \f005";
}

.stars-inner {
  position: absolute;
  top: 0;
  left: 0;
  white-space: nowrap;
  overflow: hidden;
  width: 0;
}

.stars-inner::before {
content: "\f005 \f005 \f005 \f005 \f005";
color: #000000;
}
.dark {
    background-color: #000000;
}
</style>