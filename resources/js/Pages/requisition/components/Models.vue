<template>
     <div class="hospital-list">
        <slot name="header"></slot>

        <div v-if="param.hospitals" class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <form id="filter-form" ref="filter-form">
                        <input type="hidden" name="type" :value="param.type">
                        <div class="row filter-row">
                            <div class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <select class="form-control select" v-model="query.hospital_id" @change="param.type==2?getDepartment(): false">
                                        <option value="" selected>-- Hospital --</option>
                                        <option  v-for="(row,index) in param.hospitals" :key="index" :value="row.id">{{ row.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div v-if="departments" class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <select class="form-control select"  v-model="query.department_id">
                                        <option value="" selected>-- Department --</option>
                                        <option  v-for="(row,index) in departments" :key="index" :value="row.id">{{ row.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <input class="form-control" v-model="query.requisition_no"   :placeholder="[[ requisition_placeholder ]]">
                                </div>
                            </div>
                            <div v-if="param.status" class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <select class="form-control select mb-2 mr-sm-2"  v-model="query.status">
                                        <option value="" selected>--- Status ---</option>
                                        <option  v-for="(value, key, index) in param.status" :key="index" :value="key">{{value }}</option>
                                    </select>
                                </div>
                            </div>
                            <div v-if="param.categories" class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <select class="form-control select mb-2 mr-sm-2"  v-model="query.requisition_category_id">
                                        <option value="" selected>--- Requisition Category ---</option>
                                        <option  v-for="(value, key, index) in param.categories" :key="index" :value="key">{{value }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <date-picker v-model="query.date_range" valueType="format" range></date-picker>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1">
                                <button type="button" @click="getData()" class="btn btn-success btn-block"> Search </button>
                            </div>
                            <div class="col-sm-1 col-md-1">
                                <button type="button" @click="resetData()" class="btn btn-warning btn-block"> Reset </button>
                            </div>
                            <div v-if="param.catProducts.length !== 0" class="col-sm-1 col-md-1">
                                <button type="button" @click="CatProduct()" class="btn btn-sm btn-secondary"> Summary </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-border table-striped custom-table datatable mb-0">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Requisition No</th>
                                <th>Hospital</th>
                                <th v-if="param.type==2">Department</th>
                                <th>Category</th>
                                <th>Expected Date</th>
                                <th>Receive Date</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Attachment</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row,index) in param.data" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    <jet-responsive-nav-link v-if="row.editAccess" :href="route('requisitions.edit', [row.id])" :active="route().current('requisitions.create')">
                                        {{ row.requisition_no }}
                                    </jet-responsive-nav-link>
                                    <span class="edit-link" v-else>{{ row.requisition_no }}</span>
                                </td>
                                <td>{{ row.hospital.name }}</td>
                                <td v-if="param.type==2">{{ row.department.name }}</td>
                                <td>{{ row.category.name }}</td>
                                <td>{{ row.expected_date | myDateFormat }}</td>
                                <td>{{ row.receive_date | myDateFormat }}</td>
                                <td>
                                    <a class="dropdown-item" href="#" data-toggle="modal" @click="show(row)">
                                        <span class="custom-badge" v-bind:class="row.status | statusFilter">{{ param.status[row.status] }}</span>
                                    </a>
                                </td>
                                <td>{{ row.created_at | formatDateTime }}</td>
                                <td>
                                    <a v-if="row.image" :href="row.image.source" target="_blank">View</a>
                                </td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                             <jet-responsive-nav-link v-if="row.editAccess" :href="route('requisitions.edit', [row.id])" :active="route().current('requisitions.create')" class="dropdown-item">
                                                <i class="fa fa-pencil m-r-5"></i>Edit
                                            </jet-responsive-nav-link>
                                            <a v-if="$page.isAdmin" class="dropdown-item" href="#" data-toggle="modal" @click="deleteRow(row)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                            <a class="dropdown-item" href="#" data-toggle="modal" @click="show(row)"><i class="fa fa-eye m-r-5"></i> View</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <modal width="70%" height="100%" style="top: 8%;" :adaptive="true" name="invoice-modal">
            <invoice :item="item" />
        </modal>
        <modal width="70%" height="100%" style="top: 8%;" :adaptive="true" name="cat-product-modal">
            <CatProduct :item="param.catProducts" />
        </modal>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Invoice from './Invoice'
import CatProduct from './CatProduct'
import ServiceModel from './ServiceModel'

 export default {
    components: {
        JetResponsiveNavLink,
        Invoice,
        CatProduct
    },
    props: ['param'],
    filters: {
        statusFilter(status) {
            const statusMap = ServiceModel.statusMap
            return statusMap[status]
        }
    },
    data() {
        return {
            departments: null,
            requisition_placeholder: 'Number',
            item: null,
            query: ServiceModel.search
        }
    },
    mounted () {
        if (!_.isEmpty(this.param.query)) {
            this.query = this.param.query

            if (this.param.type == 2) {
                let stop = false
                this.param.hospitals.forEach(row => {
                    if(stop){ return; }

                    if (row.id == this.query.hospital_id) {
                        this.departments = _.isEmpty(row.departments) ? null : row.departments
                        stop = true;
                    }
                })
            }
        }

        if (this.param.query.length === 0) {
            this.resetQuery()
        }
    },
    methods: {
        resetQuery: function () {
            this.query = {
                hospital_id: '',
                department_id: '',
                requisition_category_id: '',
                status: '',
                date_range: undefined,
                requisition_no: '',
            }
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/requisitions/' + data.id, data)
        },
        CatProduct () {
            this.$modal.show('cat-product-modal')
        },
        show (item) {
            this.item = item
            this.$modal.show('invoice-modal')
        },
        hide () {
            this.$modal.hide('invoice-modal')
        },
        resetData: function () {
            this.resetQuery()
            if (this.param.type == 2) {
                this.$inertia.get('/dept-requisitions')
            } else {
                this.$inertia.get('/requisitions')
            }
        },
        getData: function () {
            if (this.param.type == 2) {
                this.$inertia.get('/dept-requisitions', this.query)
            } else {
                this.$inertia.get('/requisitions', this.query)
            }
        },
        getDepartment: function () {
            this.query.department_id = undefined
            let stop = false
            this.param.hospitals.forEach(row => {
                if(stop){ return; }

                if (row.id == this.query.hospital_id) {
                    this.departments = _.isEmpty(row.departments) ? null : row.departments
                     stop = true;
                }
            })
        }
    }
}
</script>

<style>
.edit-link {
    padding-left: 1rem!important;
}
</style>