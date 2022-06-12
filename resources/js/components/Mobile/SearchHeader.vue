<template>
    <div class="searchbox">
        <div class="searchbox_main">
            <input type="text" name="search" placeholder="sony linkbuds" autocomplete="off" class="b-ellipsis">
        </div>
        <div class="searchbox__autocomplete">
        </div>
    </div>
</template>
<style>
.searchbox{
    position: relative;
    width: 100%;
}

.searchbox_main{
    background: #ffffff;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    width: 100%;
    padding: 4px;
    border-radius: 8px;
    -webkit-transition: width 2s ease-in-out;
    transition: width 2s ease-in-out;
}

.searchbox_main input {
    background: #ffffff;
    height: 30px;
    padding-left: 5px;
    border: none;
    font-size: 14px;
}

@media screen and (max-width: 768px){
    .searchbox__main{
        padding: 8px 4px 8px 8px;
        background: #f1f1f1;
    }

    .searchbox_main input{
        background: #f1f1f1;
    }
}

@media screen and (max-width: 1023px){
    .searchbox_main{
        height: 38px;
    }

    .searchbox_main input{
        width: 100%;
    }
}
</style>
<script>
import { Head } from '@inertiajs/inertia-vue'

export default {
  components: {
    Head,
  },
  props: {
    title: String,
  },
}
</script>