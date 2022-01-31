<template>
    <div class="input-group product-qty" v-bind:class="{'product-qty-sm' : size}">
        <span class="input-group-btn">
            <button class="btn btn-primary mr-1 btn-noborder" v-bind:class="{'btn-sm' : size}" type="button" @click="decrement">
                <i class="fa fa-minus"></i>
            </button>
        </span>
        <input type="text" v-model="qty" class="form-control input-number text-center" v-bind:class="{'form-control-sm' : size}">
        <span class="input-group-btn">
            <button class="btn btn-primary ml-1 btn-noborder" v-bind:class="{'btn-sm' : size}" type="button" @click="increment">
                <i class="fa fa-plus"></i>
            </button>
        </span>
    </div>
</template>
<script>
export default {
    name : 'product-qty',
    data(){
        return {
            qty : (this.value) ? this.value : 1,
            min : 1,
        }
    },
    props : ['max', 'size', 'value'],
    // created(){
    //     if(this.value){
    //         this.qty = this.value;
    //     }
    // },
    methods : {
        increment(){
            this.qty++;
        },
        decrement(){
            if(this.qty > this.min){
                this.qty--;
            }else{
                this.qty  = 1;
            }
        },
        returner(val){
            if(val < this.min){
                this.qty = this.min;
            }else if(val > this.max){
                this.qty = this.max;
            }else{
                this.qty = val;
            }
            this.$emit("change", this.qty)
        }
    },
    watch: {
        qty: function (val, oldVal) {
            this.returner(val);
        },
    }

}
</script>


<style>
.product-qty {
    width: 160px;
    border: 1px solid black;
    padding: 3px;
    border-radius: 4px;
}

.product-qty input{
    border: none;
}

.product-qty-sm {
    width: 100%;
}


.product-qty-sm input {
    padding-left: 0;
    padding-right: 0;
}
</style>