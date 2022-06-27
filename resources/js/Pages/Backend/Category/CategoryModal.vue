<template>
    <div>
        <div class="c-pointer" @click="modalShow = !modalShow" v-bind:class="{'form-control':true, 'is-invalid' : error}">
            <div v-if="parentSelected">
                <span v-if="parentSelected">{{ parentSelected.name }}</span>
                <span v-if="childSelected"> <i class="si si-arrow-right"></i> {{ childSelected.name }}</span>
                <span v-if="grandChildSelected"> <i class="si si-arrow-right"></i> {{ grandChildSelected.name }}</span>
            </div>
            <div v-else>
                Pilih Kategori
            </div>
        </div>
        <div v-if="error" class="invalid-feedback font-size-sm">{{ error[0] }}</div>

        <b-modal v-model="modalShow" :size="modalSize" rounded body-class="p-0" centered hide-footer hide-header>
            <div class="block block-rounded  block-transparent mb-0">
              <div class="block-header block-header-default">
                <h3 class="block-title">Pilih Kategori</h3>
                <div class="block-options">
                  <button type="button" class="btn-block-option" @click="close">
                    <i class="fa fa-fw fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="block-content font-size-sm">
                <div class="row">
                    <div class="col">
                        <div class="modal-category-box c-scrollbar">
                            <div class="modal-category-list">
                                <ul id="categories">
                                    <slot>
                                    <li v-for="d in dataList" :key="d.id" @click="select(d)" :class="{ 'selected' : parentSelected &&  parentSelected.id == d.id}">
                                        <span>{{  d.name }} <i v-if="d.child_count > 0" class="si si-arrow-right float-right"></i></span>
                                    </li>
                                    </slot>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col" v-if="child">
                        <div class="modal-category-box c-scrollbar">
                            <div class="modal-category-list">
                                <ul id="categories">
                                    <slot>
                                    <li v-for="c in child" :key="c.id" @click="select(c)" :class="{ 'selected' : childSelected &&  childSelected.id == c.id}">
                                        <span>{{  c.name }} <i v-if="c.child_count > 0" class="si si-arrow-right float-right"></i></span>
                                    </li>
                                    </slot>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col" v-if="grandchild">
                        <div class="modal-category-box c-scrollbar">
                            <div class="modal-category-list">
                                <ul id="categories">
                                    <slot>
                                    <li v-for="gc in grandchild" :key="gc.id" @click="select(gc)" :class="{ 'selected' : grandChildSelected &&  grandChildSelected.id == gc.id}">
                                        <span>{{  gc.name }} <i v-if="gc.child_count > 0" class="si si-arrow-right float-right"></i></span>
                                    </li>
                                    </slot>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="text-bold">
                        Category Selected : 
                    </div>
                    <div class="mx-10">
                        <span v-if="parentSelected">{{ parentSelected.name }}</span>
                        <span v-if="childSelected"> <i class="si si-arrow-right"></i> {{ childSelected.name }}</span>
                        <span v-if="grandChildSelected"> <i class="si si-arrow-right"></i> {{ grandChildSelected.name }}</span>
                    </div>
                </div>
              </div>
              <div class="block-content block-content-full text-right border-top">
                <b-button variant="alt-primary" class="mr-1" @click="close">Close</b-button>
                <b-button variant="primary" @click="select">Ok</b-button>
              </div>
            </div>
        </b-modal>
    </div>
</template>
<script>
import axios from 'axios';
export default {
    name: 'CategoryModal',
    data() {
        return {
            modalShow: false,
            dataList: null,
            child : null,
            grandchild : null,
            modalSize: 'md',
            parentSelected : null,
            childSelected : null,
            grandChildSelected : null,
            category_id : this.data,
        }
    },
    props : {
        error : Array,
        data : Number
    },
    mounted() {
        // alert(this.category_id);
        // if(this.category_id){
        //     this.findCategoryTree();
        // };
        this.getCategory();
    },
    methods: {
        async getCategory(id = null, level = null){
            const self = this;
            const params = {
                category_id: id,
            };
            await axios.get(this.route("admin.product.category.data"), { params })
            .then(function (response) {
                if(level == 1 && id){
                    self.grandchild = response.data;
                    self.modalSize = 'lg';
                }else if(level == 0 && id){
                    self.child = response.data;
                    self.grandchild = null;
                    self.modalSize = 'md';
                }else{
                    self.dataList = response.data;
                    self.child = null;
                    self.grandchild = null;
                    self.modalSize = 'md';
                }
            })
            .catch(function (error) {
                
            })
        },
        close(){
            this.modalShow = false;
        },
        select(d){
            if(d.level == 2){
                this.grandChildSelected = d;
            }else if(d.level == 1){
                this.grandChildSelected = null;
                this.childSelected = d;
            }else{
                this.grandChildSelected = null;
                this.childSelected = null;
                this.parentSelected = d;
            }

            if(d.child_count != 0){
                this.getCategory(d.id, d.level)
            }else{
                if(d.level == 0){
                    this.child = null;
                    this.grandchild = null;
                }else if(d.level == 1){
                    this.modalSize = 'md';
                    this.grandchild = null;
                }
                this.category_id = d.id;
                this.$emit('done', this.category_id);
                this.modalShow = false;
            }

        },

        async findCategoryTree(){
            const self = this;
            const params = {
                category_id: self.category_id,
            };
            await axios.get(this.route("admin.product.category.extractTree"), { params })
            .then(function (response) {
                self.grandChildSelected = response.data.grandchild;
                self.childSelected = response.data.child;
                self.parentSelected = response.data.parent;
                self.category_id = self.category_id;
              
            })
            .catch(function (error) {
                
            })
        }
        
    },
}
</script>
<style>
.c-pointer {
    cursor: pointer;
}

.modal-category-list ul {
    list-style: none;
    padding: 0;
    padding-top: 15px;
    margin: 0;
}

.modal-category-list ul li {
    font-size: 13px;
    padding: 6px 16px;
    cursor: pointer;
    position: relative;
    z-index: 1;
}
.modal-category-list ul li.selected:before,
.modal-category-list ul li:hover:before {
    content: "";
    position: absolute;
    top: 0;
    right: 0px;
    width: 100%;
    background: #eaeaea;
    height: 100%;
    z-index: -1;
}

.modal-category-list.has-right-arrow ul li:after {
    content: "\f105";
    font-family: "fontawesome";
    position: absolute;
    right: 0;
    margin-right:15px;
    top: calc(50% - 10px);
    font-size: 14px;
    opacity: 0.8;
}


.has-search i {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
}
.has-search .form-control {
    padding-left: 2.375rem;
}


.spacer {
    height: 5px;
    background: 0 0;
}
</style>