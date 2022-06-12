<template>
    <BaseLayout title="Product Brands">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Tax Rates
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
                                <th>Name</th>
                                <th>Tax Rate (%)</th>
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
                                    <td>{{ data.name }}</td>
                                    <td>{{ data.amount }} %</td>
                                    <th>
                                        <b-button variant="secondary" v-b-tooltip.hover.nofade.top="'Edit'"
                                            @click="edit(data)" size="sm">
                                            <i class="si si-note"></i>
                                        </b-button>
                                        <b-button variant="secondary" v-b-tooltip.hover.nofade.top="'Delete'"
                                            @click="destroy(data)" size="sm">
                                            <i class="si si-trash"></i>
                                        </b-button>
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

                        <div class="form-group ">
                            <label for="field-amount">Tax Rate %</label>
                            <input type="number" v-bind:class="{'form-control':true, 'is-invalid' : errors.amount}" id="field-amount" v-model="form.amount" max="100">
                            <div v-if="errors.amount" class="invalid-feedback font-size-sm">{{ errors.amount[0] }}</div>
                        </div>
                    </div>
                    <div class="block-content block-content-full text-right border-top">
                        <!-- <b-button variant="alt-danger" class="mr-1" @click="reset">cancel</b-button> -->
                        <button type="button" class="btn btn-danger btn-noborder" @click="reset">Cancel</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
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
                    amount: null,
                },
                title: 'Create New Tax Rate',
                modalShow: false,
                editMode: false,
            }
        },
        mounted() {
            this.$inertia.on('start', (event) => {
                console.log(event);
                // this.loading = true;
            })
            // this.$inertia.on('finish', (event) => {
            //     this.loading = false;
            // })
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
                    amount: null,
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
                let url = this.editMode ? this.route("admin.settings.tax.update") : this.route(
                    "admin.settings.tax.store");
                form.post(url, {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.$swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `Tax Rate Added Successfully!`,
                        });
                    },
                });
            },
            edit: function (data) {
                this.form.id = data.id;
                this.form.name = data.name;
                this.form.amount = data.amount;
                this.editMode = true;
                this.modalShow = true;
                this.title = 'Update Tax Rate';
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
                        this.$inertia.delete(this.route('admin.settings.tax.delete', data.id), {
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
                this.$inertia.get(this.route('admin.settings.tax.index', {
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
