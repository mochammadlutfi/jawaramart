<template>
    <div class="input-group">
        <input :type="feildType" v-model="inputVal" v-bind:class="{'form-control':true, 'is-invalid' : error}" :id="id" autocomplete="on">
        <div class="input-group-append">
            <span class="input-group-text">
                <a href="javaScript:void(0);" @click="switchPassword">
                    <i :class="iconType" aria-hidden="true"></i>
                </a>
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: 'password-input',
        props: {
            error : Array,
            value : String,
            id : String,
        },
        data() {
            return {
                feildType: "password",
                iconType : "fa fa-eye-slash",
            }
        },
        methods: {
            switchPassword() {
                this.feildType = this.feildType === 'password' ? 'text' : 'password'
                this.iconType = this.iconType === 'fa fa-eye-slash' ? 'fa fa-eye' : 'fa fa-eye-slash'
            }
        },
        
        computed: {
            inputVal: {
                get() {
                    return this.value;
                },
                set(val) {
                    this.$emit('input', val);
                }
            },
        }   
    }
</script>