<template>
    <BaseLayout :title="(editMode) ? 'Edit Product' : 'Add New Product'">
        <div class="content">
            <form @submit.prevent="submit">
                <div class="content-heading pt-0 mb-3">
                    Add New Product
                    <button type="submit" class="btn btn-primary float-right mr-5 btn-sm">
                        <i class="si si-paper-plane mr-1"></i>
                        Save
                    </button>
                </div>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <div class="block-title">
                            <strong>Product Information</strong>
                        </div>
                    </div>
                    <div class="block-content pb-15">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="field-name">Name</label>
                            <div class="col-lg-9">
                                <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name}" id="field-name" v-model="form.name" >
                                <div v-if="errors.name" class="invalid-feedback font-size-sm">{{ errors.name[0] }}</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="field-description">Description</label>
                            <div class="col-lg-9">
                                <textarea v-bind:class="{'form-control':true, 'is-invalid' : errors.description}" id="field-description" v-model="form.description" cols="30" rows="10"></textarea>
                                <div v-if="errors.description" class="invalid-feedback font-size-sm">{{ errors.description[0] }}</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="field-kategori">Category</label>
                            <div class="col-lg-9">
                                <CategorySelector @done="(category) => category_selected = category" :error="errors.category_id" :category_id="form.category_id"/>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="field-show_on_web">Show On Web?</label>
                            <div class="col-lg-9">
                                <b-form-radio-group id="field-show_on_web" v-model="form.show_on_web" name="show_on_web">
                                    <b-form-radio value="1">Yes</b-form-radio>
                                    <b-form-radio value="0">No</b-form-radio>
                                </b-form-radio-group>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <div class="block-title">
                            <strong>Product Prices</strong>
                        </div>
                    </div>
                    <div class="block-content pb-15">
                        <div class="form-group row" v-if="!form.has_variant">
                            <label class="col-lg-3 col-form-label" for="field-sku">SKU</label>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" id="field-sku" v-model="variant[0].sku">
                            </div>
                        </div>
                        <div class="form-group row" v-if="!form.has_variant">
                            <label class="col-lg-3 col-form-label" for="field-sell_price">Sell Price</label>
                            <div class="col-lg-7">
                                <CurrencyInput v-model="variant[0].sell_price" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group row" v-if="!form.has_variant">
                            <label class="col-lg-3 col-form-label" for="field-purchase_price">Purchase Price</label>
                            <div class="col-lg-7">
                                <CurrencyInput v-model="variant[0].purchase_price" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group row" v-if="!form.has_variant">
                            <label class="col-lg-3 col-form-label" for="btn-add-variasi">Variant 1</label>
                            <div class="col-lg-7">
                                <button type="button" class="btn btn-outline-primary" @click="addVariant">
                                    <span>
                                        <i class="si si-plus mr-1"></i> Add Variant
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="form-group" v-if="form.has_variant">
                            <label for="field-var1">Varian 1</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="text" class="form-control" id="field-var1_nama" v-model="form.var1_name" placeholder="Masukkan Nama Varian, contoh: Warna, dll.">
                                    <div class="text-danger font-size-sm" id="error-var1_nama"></div>
                                </div>
                                <div class="col-7">
                                    <b-form-tags input-id="tags-basic"
                                    v-model="var1_value"
                                    separator=","
                                    placeholder="Masukan Nilai Varian, contoh : Merah, Hijau"
                                    tag-variant="primary"
                                    :remove-on-delete="true"
                                    >
                                    <!-- <template v-slot="{ tags,removeTag }">
                                        <ul class="b-form-tags-list list-unstyled mb-0 d-flex flex-wrap align-items-center">
                                            <li v-for="tag in tags" :key="tag" tag="li" class="badge b-form-tag d-inline-flex align-items-baseline mw-100 badge-primary mr-2">
                                                <span>{{ tag }}</span>
                                                <b-button @click="removeTag(tag)" class="close b-form-tag-remove" size="sm">x</b-button>
                                            </li>
                                        </ul>
                                        <b-input-group class="mb-2">
                                    </template> -->
                                    </b-form-tags>
                                    <div class="text-danger font-size-sm" id="error-var1_value"></div>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-danger" v-if="form.has_variant" @click="removeVariant">
                                        <i class="si si-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" v-if="has_variant2 == false && form.has_variant">
                            <label class="col-lg-3 col-form-label" for="btn-add-variasi">Variant 2</label>
                            <div class="col-lg-7">
                                <button type="button" class="btn btn-outline-primary" v-if="!has_variant2" @click="has_variant2 = !has_variant2">
                                    <span>
                                        <i class="si si-plus mr-1"></i> Add Variant
                                    </span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group" v-if="has_variant2">
                            <label for="field-var1">Varian 2</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="text" class="form-control" id="field-var2_name" v-model="form.var2_name" placeholder="Masukkan Nama Varian, contoh: Warna, dll.">
                                    <div class="text-danger font-size-sm" id="error-var2_name"></div>
                                </div>
                                <div class="col-7">
                                    <b-form-tags input-id="tags-basic"
                                    v-model="var2_value"
                                    separator=","
                                    placeholder="Masukan Nilai Varian, contoh : Merah, Hijau"
                                    tag-variant="primary"
                                    :remove-on-delete="true"
                                    >
                                    </b-form-tags>
                                    <div class="text-danger font-size-sm" id="error-var1_value"></div>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-danger" @click="removeVariant2">
                                        <i class="si si-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-vcenter text-center" v-if="form.has_variant">
                            <thead>
                                <tr>
                                    <th width="18%" v-if="form.var1_name">{{ form.var1_name }}</th>
                                    <th width="18%" v-if="form.var2_name && var2_value.length > 0">{{ form.var2_name }}</th>
                                    <th width="20%">Sell Price</th>
                                    <th width="20%">Purchase Price</th>
                                    <th width="20%">SKU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(value, index) in variant" :key="index">
                                    <td 
                                        :colspan="rowSpan"
                                        v-if="!index ? true : variant[index-1].var1 == variant[index].var1 ? '' : true"
                                        class="text-center"
                                        v-text="value.var1"
                                    ></td>
                                    <td v-if="form.var2_name && var2_value.length > 0">{{ value.var2 }}</td>
                                    <td>
                                        <CurrencyInput :value="variant[index].sell_price" @change="variant[index].sell_price = $event" class="form-control"/>
                                    </td>
                                    <td>
                                        <CurrencyInput :value="variant[index].purchase_price"  @change="variant[index].purchase_price = $event" class="form-control"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" :value="variant[index].sku"  @change="event => variant[index].sku = event.target.value"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Product Images -->
                
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <div class="block-title">
                            <strong>Product Images</strong>
                        </div>
                    </div>
                    <div class="block-content pb-15">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col" v-for="(n, i) in 5" :key="i">
                                        <ImageUpload 
                                            :image="form.images[i]" 
                                            :ratio="1/1"
                                            :height="200"
                                            @done="(image) => form.images[i] = image"
                                            @removeImage="(image) => form.images[i] = null" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import CategorySelector from '../Category/CategoryModal.vue';
import CurrencyInput  from '@/components/Form/CurrencyInput.vue';
import ImageUpload from '@/components/SingleImageUpload.vue';

export default {
    components: {
        BaseLayout,
        Link,
        CategorySelector,
        CurrencyInput,
        ImageUpload
    },
    data(){
        return {
            form: {
                id : null,
                name: null,
                description : null,
                category_id : null,
                has_variant : false,
                show_on_web : true,
                var1_name : null,
                var1_value : null,
                var2_name : null,
                var2_value : null,
                images : [],
            },
            variant : [{
                id : null,
                var1 : '',
                var2 : '',
                sell_price : 0,
                purchase_price : 0,
                sku : '',
            }],
            has_variant2 : false,
            var1_value : [],
            var2_value : [],
            variantDeleted : [],
        }
    },
    props: {
        errors : Object,
        data: Object,
        editMode: Boolean,
    },
    computed: {
        variantRowSpan: function(){
            if(this.var2_value.length > 0){
                return this.var2_value.length;
            }
            return false;
        }
    },
    watch: {
        var1_value: function (val, oldVal) {
            this.var1_value = val;
            this.form.var1_value = val;
            if(val.length > 0){
                this.updateVariantRow();
            }
            const removed = oldVal.filter(v => !val.includes(v));
            if(removed && removed.length > 0) {
                // alert(`${removed} was removed from the list`);
                this.removeVariantRow(1, removed);
            }
        },
        var2_value: function (val, oldVal) {
            this.var2_value = val;
            this.form.var2_value = val;
            if(val.length > 0){
                this.updateVariantRow();
            }
        },
        images: function (val, oldVal) {
            this.form.images = val;
            console.log(this.form.images);
        },
    },
    methods :{
        submit: function(){
            let data = Object.assign(this.form, {variant : this.variant, variant_deleted : this.variantDeleted});
            this.$swal.fire({
                title: 'Tunggu Sebentar...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form(data)
            let url = this.editMode ? this.route("admin.product.update") : this.route("admin.product.store");
            form.post(url, {
                preserveScroll: true,
                resetOnSuccess: true,
                onSuccess: () => {

                    this.editModeActive();

                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Product Saved Successfully!`,
                        showCancelButton: true,
                        confirmButtonText: 'Add Another',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$inertia.visit(this.route("admin.product.create"));
                        }
                    });
                },
                onerror: () => {
                    return this.$swal.close();
                }
            });
        },

        addVariant(){
            this.form.has_variant = true;
            this.variant = [];
        },

        updateVariantRow(){
            const newVariant = [];
            this.var1_value.forEach((var1, i1) => {
                if(this.var2_value.length == 0){
                    var foundIndex = this.variant.findIndex(x => x.var1 == var1 && x.id != null);
                    if(foundIndex != -1){
                        this.variant[foundIndex].var1 = var1;
                    }else{
                        newVariant.push({
                            'id' : null,
                            'var1' : var1,
                            'var2' : null,
                            'sell_price' : null,
                            'purchase_price' : null,
                            'sku' : null,
                        });
                    }
                }else{
                    this.var2_value.forEach((var2, i2) => {
                    var foundIndex = this.variant.findIndex(x => x.var1 == var1 && x.id != null);
                        if(foundIndex != -1){
                            this.variant[foundIndex].var1 = var1;
                            this.variant[foundIndex].var2 = var2;
                        }else{
                            newVariant.push({
                                'id' : null,
                                'var1' : var1,
                                'var2' : var2,
                                'sell_price' : null,
                                'purchase_price' : null,
                                'sku' : null,
                            });
                        }
                    });
                }
            });
            this.variant.push(...newVariant);
        },

        removeVariantRow(type, variant){
            if(type == 1){
                console.log('variant 1');
                console.log(variant);
                var foundIndex = this.variant.findIndex(x => x.var1 == variant);
                if(this.variant[foundIndex].id){
                    this.variantDeleted.push(this.variant[foundIndex]);
                }
                this.variant.splice(foundIndex, 1);
            }else{
                var foundIndex = this.variant.findIndex(x => x.var2 == variant);
                var foundIndex = this.variant.findIndex(x => x.var1 == variant);
                if(this.variant[foundIndex].id){
                    this.variantDeleted.push(this.variant[foundIndex]);
                }
                this.variant.splice(foundIndex, 1);
            }
        },

        removeVariant: function(){
            this.form.has_variant = false;
            this.form.var1_name = null;
            this.form.var1_value = null;
            this.form.var2_name = null;
            this.form.var2_value = null;
            this.var1_value = [];
            this.var2_value = [];
            this.variant = [];
            this.variant.push({
                'id' : null,
                'var1' : '',
                'var2' : '',
                'sell_price' : 0,
                'purchase_price' : 0,
                'sku' : '',
            });
        },

        removeVariant2 : function(){
            this.form.var2_name = null;
            this.form.var2_value = null;
            this.has_variant2 = false;
            this.var2_value = [];
        },
        coba(e){
            console.log(e);
            if(e.length > 0){
                this.updateVariantRow();
            }
        },

        editModeActive(){
            if(this.editMode){
                this.form.id = this.data.id;
                this.form.name = this.data.name;
                this.form.description = this.data.description;
                this.form.category_id = this.data.category_id;
                this.form.has_variant = this.data.has_variant == 1 ? true : false;
                this.form.var1_name = this.data.var1_name;
                this.form.show_on_web = this.data.show_web;
                this.form.var2_name = this.data.var2_name ? this.data.var2_name : null;
                this.var1_value = this.data.var1_value ? JSON.parse(this.data.var1_value) : [];
                this.var2_value = this.data.var2_value ? JSON.parse(this.data.var2_value) : [];
                // if(this.data.variant.length > 1){
                    this.variant = [];
                    this.data.variant.forEach((e, i) => {
                        this.variant.push({
                            'id' : e.id,
                            'var1' : e.variant,
                            'var2' : e.variant2,
                            'sell_price' : e.sell_price,
                            'purchase_price' : e.purchase_price,
                            'sku' : e.sku,
                        });
                    });
                // }else{
                //     this.form.sell_price = this.data.variant[0].sell_price;
                //     this.form.purchase_price = this.data.variant[0].purchase_price;
                //     this.form.sku = this.data.variant[0].sku;
                // }
                if(this.data.images.length > 0){
                    this.form.images = [];
                    this.data.images.forEach((value, index) => {
                        // this.form.images.push({
                        //     'id' : id,
                        //     'image' : value.path,
                        // });
                        this.form.images.push(value.image_url);
                    });
                }
            }
        },
        removeTag(tag){

        }
    },
    mounted() {
        if(this.editMode){
            this.editModeActive();
        }
    },
}
</script>
