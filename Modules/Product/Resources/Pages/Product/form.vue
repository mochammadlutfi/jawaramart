<template>
    <BaseLayout>
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
                                <CategorySelector @done="(category) => form.category_id = category" :error="errors.category_id" :category_id="form.category_id"/>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <div class="block-title">
                            <strong>Harga</strong>
                        </div>
                    </div>
                    <div class="block-content pb-15">
                        <div class="form-group row" v-if="!form.has_variant">
                            <label class="col-lg-3 col-form-label" for="field-sell_price">Sell Price</label>
                            <div class="col-lg-7">
                                <CurrencyInput v-model="form.sell_price" class="form-control"/>
                                <div class="text-danger font-size-sm" id="error-sell_price"></div>
                            </div>
                        </div>
                        <div class="form-group row" v-if="!form.has_variant">
                            <label class="col-lg-3 col-form-label" for="field-purchase_price">Purchase Price</label>
                            <div class="col-lg-7">
                                <CurrencyInput v-model="form.purchase_price" class="form-control"/>
                                <div class="text-danger font-size-sm" id="error-purchase_price"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="btn-add-variasi">Variant</label>
                            <div class="col-lg-7">
                                <button type="button" class="btn btn-outline-primary" v-if="!form.has_variant" @click="form.has_variant = !form.has_variant">
                                    <span>
                                        <i class="si si-plus mr-1"></i> Add Variant
                                    </span>
                                </button>
                                <button type="button" class="btn btn-outline-danger" v-if="form.has_variant" @click="removeVariant">
                                    <span>
                                        <i class="si si-trash mr-1"></i> Remove Variant
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
                                <div class="col-8">
                                    <b-form-tags input-id="tags-basic"
                                    v-model="var1_value"
                                    separator=","
                                    placeholder="Masukan Nilai Varian, contoh : Merah, Hijau"
                                    tag-variant="primary"
                                    :remove-on-delete="true"
                                    >
                                    </b-form-tags>
                                    <div class="text-danger font-size-sm" id="error-var1_value"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" v-if="form.has_variant && var1_value.length > 0">
                            <label for="field-var1">Varian 2</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="text" class="form-control" id="field-var1_nama" v-model="form.var2_name" placeholder="Masukkan Nama Varian, contoh: Warna, dll.">
                                    <div class="text-danger font-size-sm" id="error-var1_nama"></div>
                                </div>
                                <div class="col-8">
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
                            </div>
                        </div>

                        <table class="table table-bordered table-vcenter text-center" v-if="form.has_variant && form.var1_name">
                            <thead>
                                <tr>
                                    <th width="18%">{{ form.var1_name }}</th>
                                    <th width="18%" v-if="form.var2_name">{{ form.var2_name }}</th>
                                    <th width="20%">Sell Price</th>
                                    <th width="20%">Purchase Price</th>
                                    <th width="20%">SKU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(value, index) in variant_list" :key="index">
                                    <td 
                                        :rowspan="variantRowSpan"
                                        v-if="!index ? true : variant_list[index-1].var1 == variant_list[index].var1 ? '' : true"
                                        class="text-center"
                                        v-text="value.var1"
                                    ></td>
                                    <td v-if="form.var2_name && var2_value.length > 0">{{ value.var2 }}</td>
                                    <td>
                                        <CurrencyInput v-model="form.variant[index].sell_price" class="form-control"/>
                                    </td>
                                    <td>
                                        <CurrencyInput v-model="form.variant[index].purchase_price" class="form-control"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="form.variant[index].sku"/>
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
                            <strong>3. Foto Produk</strong>
                        </div>
                    </div>
                    <div class="block-content pb-15">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col" v-for="(n, i) in 5" :key="i">
                                        <ImageUpload 
                                            :image="(form.images.length > 0 && typeof form.images[i]!= 'undefined') ? asset(form.images[i]) : null" 
                                            :ratio="1/1"
                                            @done="(image) => form.images[i] = image"
                                            @removeImage="(image) => form.images.splice(i, 1)" />
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
import BaseLayout from '@/Layouts/Authenticated.vue';
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
                category_id : null,
                has_variant : true,
                var1_name : null,
                var1_value : null,
                var2_name : null,
                var2_value : null,
                variant : null,
                images : [],
            },
            var1_value : [],
            var2_value : [],
        }
    },
    props: {
        errors : Object,
        data: Object,
        editMode: Boolean,
    },
    computed: {
        variant_list: function () {
            let list = [];
            if(this.editMode){
                this.data.variant.forEach((value, index) => {
                    if(this.data.var2_value){
                        list.push({
                            'var1' : value.variant,
                            'var2' : value.variant2,
                            'sell_price' : value.sell_price,
                            'purchase_price' : value.purchase_price,
                            'sku' : value.sku,
                        });
                    }else{
                        list.push({
                            'var1' : value.variant,
                            'sell_price' : value.sell_price,
                            'purchase_price' : value.purchase_price,
                            'sku' : value.sku,
                        });
                    }
                });
            }else{
                this.var1_value.forEach((var1, index) => {
                    if(this.var2_value.length > 0){
                        this.var2_value.forEach((var2, index) => {
                            list.push({
                                'var1' : var1,
                                'var2' : var2,
                                'sell_price' : null,
                                'purchase_price' : null,
                                'sku' : null,
                            });
                        });
                    }else{
                        list.push({
                            'var1' : var1,
                            'sell_price' : null,
                            'purchase_price' : null,
                            'sku' : null,
                        });
                    }
                });
            }

            this.form.variant = list;
            return list;
        },
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
        },
        var2_value: function (val, oldVal) {
            this.var2_value = val;
            this.form.var2_value = val;
        },
        images: function (val, oldVal) {
            this.form.images = val;
            console.log(this.form.images);
        },
    },
    methods :{
        submit: function(){
            let form = this.$inertia.form(this.form)
            let url = this.editMode ? this.route("admin.product.update") : this.route("admin.product.store");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
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
            });
        },
        removeVariant: function(){
            this.form.has_variant = false;
            this.form.var1_name = null;
            this.form.var1_value = null;
            this.form.var2_name = null;
            this.form.var2_value = null;
            this.var1_value = [];
            this.var2_value = [];
        },
        editModeActive(){
            if(this.editMode){
                this.form.id = this.data.id;
                this.form.name = this.data.name;
                this.form.description = this.data.description;
                this.form.category_id = this.data.category_id;
                this.form.has_variant = this.data.has_variant == 1 ? true : false;
                this.form.var1_name = this.data.var1_name;
                this.form.var2_name = this.data.var2_name ? this.data.var2_name : null;
                this.var1_value = this.data.var1_value ? JSON.parse(this.data.var1_value) : [];
                this.var2_value = this.data.var2_value ? JSON.parse(this.data.var2_value) : [];
                if(this.data.has_variant == 0){
                    this.form.sell_price = this.data.variant[0].sell_price;
                    this.form.purchase_price = this.data.variant[0].purchase_price;
                    this.form.sku = this.data.variant[0].sku;
                }
                if(this.data.images.length > 0){
                    this.data.images.forEach((value, index) => {
                        // this.form.images.push({
                        //     'id' : id,
                        //     'image' : value.path,
                        // });
                        this.form.images.push(value.path);
                    });
                }
            }
        }
    },
    mounted() {
        if(this.editMode){
            this.editModeActive();
        }
    },
}
</script>
