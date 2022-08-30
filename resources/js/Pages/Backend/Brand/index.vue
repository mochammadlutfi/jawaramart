<template>
    <BaseLayout title="Product Brands">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Brands
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger">
                        <i class="si si-trash"></i>
                        Delete {{ selected.length }} Selected
                    </button>
                    <button class="btn btn-sm btn-secondary" type="button" @click="openModal">
                        <i class="si si-plus"></i>
                        Create
                    </button>
                </div>
            </div>
            <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                <div class="block-content p-2">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            <div class="has-search">
                                <i class="fa fa-search"></i>
                                <input type="search" class="form-control" id="search-data-list" v-model="search"
                                    @keyup="doSearch()" @change="doSearch()">
                            </div>
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                            <div class="d-flex float-right">
                                <div class="my-auto px-3">
                                    <span>{{ dataList.from }}-{{ dataList.to }}/{{ dataList.total }}</span>
                                </div>
                                <div class="pt-25 pl-0">
                                    <Link :href="dataList.prev_page_url ? dataList.prev_page_url : '#'" as="button"
                                        class="btn btn-alt-secondary mx-1" type="button"
                                        :disabled="dataList.prev_page_url ? false : true">
                                    <i class="fa fa-chevron-left fa-fw"></i>
                                    </Link>
                                    <Link :href="dataList.next_page_url ? dataList.next_page_url : '#'" as="button"
                                        class="btn btn-alt-secondary mx-1" type="button"
                                        :disabled="dataList.next_page_url ? false : true">
                                    <i class="fa fa-chevron-right fa-fw"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block block-rounded block-shadow-2 block-bordered mb-5">
                <div class="block-content px-0 py-0">
                    <table class="table table-striped table-vcenter table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="2%">
                                    <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll"></b-form-checkbox>
                                </th>
                                <th width="45px">Image</th>
                                <th>Name</th>
                                <th>State</th>
                                <th>Total Product</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody v-if="loading">
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-50">
                                        <div class="spinner-border text-primary wh-50" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <template v-if="Object.values(dataList.data).length">
                                <tr v-for="data in dataList.data" :key="data.id">
                                    <td>
                                        <b-form-checkbox
                                            :id="'data-'+data.id"
                                            v-model="selected"
                                            :name="'data-'+data.id"
                                            :value="data.id"
                                            >
                                        </b-form-checkbox>
                                    </td>
                                    <td> <img class="media-object" :src="data.image_url" style="max-width:45px"></td>
                                    <td>{{ data.name }}</td>
                                    <td>
                                        <span class="badge badge-primary" v-if="data.state == 'active'">Active</span>
                                        <span class="badge badge-danger" v-else>Inactive</span>
                                    </td>
                                    <td>{{ data.product_count }} Product</td>
                                    <th>
                                        <b-dropdown text="Action" class="m-md-2"  size="sm">
                                            <a href="javascript:void(0)" class="dropdown-item" @click.prevent="edit(data)">
                                                <i class="si si-note mr-3"></i>
                                                Edit
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item" @click.prevent="destroy(data)">
                                                <i class="si si-trash mr-3"></i>
                                                Delete
                                            </a>
                                        </b-dropdown>
                                    </th>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-if="!Object.values(dataList.data).length">
                                    <td colspan="6">
                                        Data Kosong
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        

        <b-modal v-model="modalShow" size="md" rounded body-class="p-0" centered hide-footer hide-header>
            <div class="block block-rounded  block-transparent mb-0">
                <form @submit.prevent="submit">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">{{ title }}</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" @click="reset">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <div class="form-group ">
                            <label for="field-name">Name</label>
                            <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name}"
                                id="field-name" v-model="form.name">
                            <div v-if="errors.name" class="invalid-feedback font-size-sm">{{ errors.name[0] }}</div>
                        </div>

                        <b-form-group label="State" v-slot="{ ariaDescribedby }">
                            <b-form-radio-group id="field-state" v-model="form.state" :aria-describedby="ariaDescribedby" name="state">
                                <b-form-radio value="active">Active</b-form-radio>
                                <b-form-radio value="inactive">Inactive</b-form-radio>
                            </b-form-radio-group>
                        </b-form-group>
                        <div class="form-group">
                            <label for="field-name">Image</label>
                            <div class="row">
                                <div class="col-4">
                                    <ImageUpload :image="(typeof form.image === 'string') ? asset(form.image) : null"
                                        :height="129.33" :ratio="1/1" @done="(image) => form.image = image"
                                        @removeImage="(image) => form.image = null" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full text-right border-top">
                        <b-button variant="alt-danger" class="mr-1" @click="reset">cancel</b-button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </b-modal>
    </BaseLayout>
</template>

<script>
    import {
        Link
    } from '@inertiajs/inertia-vue';
    import ImageUpload from '@/components/SingleImageUpload.vue';
    import moment from 'moment';
    import _ from 'lodash';

    import BaseLayout from '@/Layouts/Backend/Authenticated.vue';



    export default {
        components: {
            BaseLayout,
            Link,
            ImageUpload
        },
        data() {
            return {
                loading: false,
                selected: [],
                selectAll: false,
                search: this.route().params.search == '' ? '' : this.route().params.search,
                form: {
                    id: null,
                    name: null,
                    image: null,
                    state: 'active',
                },
                title: 'Add New Brand',
                modalShow: false,
                editMode: false,
            }
        },
        watch: {
            search: function () {
                this.doSearch()
            },
            selectAll: function(val){
                this.selected = [];
                if(val){
                    this.dataList.data.forEach((value, index) => {
                        this.selected.push(value.id)
                    });
                }
            }
        },
        props: {
            errors: Object,
            dataList: Object,
        },
        methods: {
            reset() {
                this.form = {
                    id: null,
                    name: null,
                    image: null,
                    state: 'active',
                };
                this.modalShow = false;
                this.editMode = true;
            },
            openModal() {
                this.editMode = false;
                this.modalShow = true;
            },
            submit: function () {
                let form = this.$inertia.form(this.form)
                let url = this.editMode ? this.route("admin.product.brand.update") : this.route(
                    "admin.product.brand.store");
                form.post(url, {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.$bvToast.toast(this.$page.props.flash.message, {
                            autoHideDelay: 5000,
                        })
                        this.reset();
                    },
                });
            },
            edit: function (data) {
                this.form.id = data.id;
                this.form.name = data.name;
                this.form.image = data.image;
                this.form.state = data.state;
                this.editMode = true;
                this.modalShow = true;
            },
            destroy: function (data) {
                this.$swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: `You won't be able to revert this!`,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete it',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.$inertia.delete(this.route('admin.product.brand.delete', data.id), {
                            preserveScroll: true
                        })
                        this.reset();
                    }
                })
            },
            format_date(value) {
                if (value) {
                    return moment(String(value)).format('DD MMM YYYY')
                }
            },
            doSearch: _.throttle(function () {
                this.$inertia.get(this.route('admin.product.brand.index', {
                    search: this.search
                }), {
                    preserveScroll: true,
                })
            }, 200),
            select() {
                this.selected = [];
                if (!this.selectAll) {
                    this.dataList.data.forEach((value, index) => {
                        this.selected.push(value.id)
                    });
                }
            }
        }
    }

</script>
