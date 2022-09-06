<template>
     <div class="hospital-list">
         <slot name="header"></slot>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-border table-striped custom-table datatable mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company Name</th>
                                <th>Address</th>
                                <th>Contact Person Name</th>
                                <th>Contact Person Mobile</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.company_name }} </td>
                                <td>{{ row.address }}</td>
                                <td>{{ row.contact_person_name }}</td>
                                <td>{{ row.contact_person_mobile }}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'ACTIVE' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                             <jet-responsive-nav-link :href="route('corporateClients.edit', [row.id])" :active="route().current('corporateClients.create')" class="dropdown-item">
                                                <i class="fa fa-pencil m-r-5"></i>Edit
                                            </jet-responsive-nav-link>
                                            <a class="dropdown-item" href="#" data-toggle="modal" @click="openDiscount(row)"  data-target="#clientDiscount"><i class="fa fa-diamond m-r-5"></i> View Discount Details</a>
                                            <a v-if="$page.isAdmin" class="dropdown-item" href="#" data-toggle="modal" @click="deleteRow(row)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

<!--         client discount modal section start-->

         <!-- Modal -->
         <div class="modal fade  bd-example-modal-lg" id="clientDiscount" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
             <div class="modal-dialog modal-lg modal-dialog" role="document">
                 <div class="modal-content">
                     <div class="modal-header">

                         <h5 class="modal-title pull-left" id="exampleModalLongTitle">Discounts of <b>{{client.company_name}}</b></h5>

                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
                     <div class="modal-body">
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="table-responsive" v-if="show_table">
                                     <div class="col-md-12">
                                         <h4 class="page-title pull-left">
                                             Discount List
                                         </h4>
                                         <button type="button" class="btn btn-primary btn-rounded float-right"  @click="newDiscount()">
                                             <i class="fa fa-plus new-btn" aria-hidden="true"></i>
                                             <span class="new-btn"> &nbsp; Create Discount </span>
                                         </button>
                                         <br/>
                                     </div>
                                     <table class="table table-border table-striped custom-table datatable mb-0">
                                         <thead>
                                         <tr>
                                             <th>#</th>
                                             <th>Hospital</th>
                                             <th>Service Name</th>
                                             <th>Package Name</th>
                                             <th>Discount (%)</th>
                                             <th>Discount Amount</th>
                                             <th>Status</th>
                                             <th class="text-right">Action</th>
                                         </tr>
                                         </thead>
                                         <tbody>
                                         <tr v-for="discount in discounts" :key="discount.id">
                                             <td>{{ discount.id }}</td>
                                             <td>{{ discount.hospital.name }}</td>
                                             <td>{{ discount.service ? discount.service.name : null }}</td>
                                             <td>{{ discount.service_package ? discount.service_package.name : null }}</td>
                                             <td>{{ discount.discount_percentage }}</td>
                                             <td>{{ discount.discount_amount }}</td>
                                             <td><span class="custom-badge" v-bind:class="discount.status == 'ACTIVE' ? 'status-green' : 'status-red'">{{ discount.status }}</span></td>

                                             <td class="text-right">
                                                 <div class="dropdown dropdown-action">
                                                     <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                     <div class="dropdown-menu dropdown-menu-right">
                                                         <a class="dropdown-item" href="#" data-toggle="modal" @click="editDiscount(discount)"><i class="fa fa-pencil m-r-5"></i>Edit</a>
                                                         <a class="dropdown-item" href="#" data-toggle="modal" @click="deleteDiscount(discount)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                     </div>
                                                 </div>
                                             </td>
                                         </tr>
                                         </tbody>
                                     </table>
                                 </div>

                                 <form v-if="show_form">
                                     <div class="row">
                                         <div class="col-md-12">
                                             <h4 class="page-title pull-left">
                                                 {{ editMode ? "Update" : "Add" }} Discount
                                             </h4>
                                             <button type="button" class="btn btn-primary btn-rounded float-right"  @click="backToDiscountList()">
                                                 <i class="fa fa-arrow-left new-btn" aria-hidden="true"></i>
                                                 <span class="new-btn"> &nbsp; Discount List </span>
                                             </button>
                                             <br/>
                                         </div>
                                         <div class="col-sm-12">
                                             <div class="form-group">
                                                 <label> Hospital</label>
                                                 <select class="form-control select"  v-model="form.hospital_id" >
                                                     <option value="" selected>Select Hospital</option>
                                                     <option  v-for="hospital in hospitals" :key="hospital.id" :value="hospital.id">{{ hospital.name }}</option>
                                                 </select>
                                             </div>
                                         </div>

                                         <div class="col-sm-12">
                                             <div class="form-group">
                                                 <label> Discount Category</label>
                                                 <select class="form-control select"  v-model="form.discount_category"  @change="getServicePackage()" >
                                                     <option value="" selected>Select Discount Category</option>
                                                     <option  v-for="type in discount_types" :key="type" :value="type">{{ type }}</option>
                                                 </select>
                                             </div>
                                         </div>


                                         <div class="col-sm-12">
                                             <div class="form-group">
                                                 <label> Service</label>
                                                 <select class="form-control select"  v-model="form.service_id" >
                                                     <option value="" selected>Select Service</option>
                                                     <option  v-for="data in services" :key="data.id" :value="data.id">{{ data.name }}</option>
                                                 </select>
                                             </div>
                                         </div>

                                         <div class="col-sm-12">
                                             <div class="form-group">
                                                 <label> Service Package</label>
                                                 <select class="form-control select"  v-model="form.service_package_id" >
                                                     <option value="" selected>Select Service Package</option>
                                                     <option  v-for="data in packages" :key="data.id" :value="data.id">{{ data.name }}</option>
                                                 </select>
                                             </div>
                                         </div>


                                         <div class="col-sm-12">
                                             <div class="form-group">
                                                 <label>Discount Percentage</label>
                                                 <input class="form-control" type="number" v-model="form.discount_percentage"/>
                                             </div>
                                         </div>

                                         <div class="col-sm-12">
                                             <div class="form-group">
                                                 <label>Discount Amount</label>
                                                 <input class="form-control" type="number" v-model="form.discount_amount"/>
                                             </div>
                                         </div>


                                         <div class="col-sm-12">
                                             <div class="form-group">
                                                 <label> Status</label><br/>
                                                 <div class="form-check form-check-inline">
                                                     <input
                                                         v-model="form.status"
                                                         class="form-check-input"
                                                         type="radio"
                                                         name="status"
                                                         value="ACTIVE"
                                                     />
                                                     <label
                                                         class="form-check-label"
                                                         for="product_active"
                                                     >
                                                         Active
                                                     </label>
                                                 </div>
                                                 <div class="form-check form-check-inline">
                                                     <input
                                                         v-model="form.status"
                                                         class="form-check-input"
                                                         type="radio"
                                                         name="status"
                                                         value="INACTIVE"
                                                     />
                                                     <label
                                                         class="form-check-label"
                                                         for="product_inactive"
                                                     >
                                                         Inactive
                                                     </label>
                                                 </div>
                                             </div>
                                         </div>

                                         <div class="col-sm-12">
                                             <div class="m-t-20 text-center">
                                                 <button
                                                     type="button"
                                                     class="btn btn-primary submit-btn"
                                                     wire:click.prevent="store()"
                                                     v-show="!editMode"
                                                     @click="saveDiscount(form)"
                                                 >
                                                     Save
                                                 </button>
                                                 <button
                                                     type="button"
                                                     class="btn btn-primary submit-btn"
                                                     wire:click.prevent="store()"
                                                     v-show="editMode"
                                                     @click="updateDiscount(form)"
                                                 >
                                                     Update
                                                 </button>
                                             </div>
                                         </div>

                                     </div>
                                 </form>
                             </div>

                         </div>
                     </div>
                 </div>
             </div>
         </div>

<!--         client discount modal section end-->

    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import {HTTP} from '@/utils/http-common'
import {success_message, error_message} from '@/utils/alert'

 export default {
     components: {
            JetResponsiveNavLink,
        },
        props: ['data', 'errors'],
        data() {
            return {
                form: {
                    hospital_id: '',
                    service_id: '',
                    service_package_id: '',
                    discount_category: '',
                    status: 'ACTIVE',
                },
                client: {},
                discounts: [],
                hospitals: [],
                services: [],
                packages: [],
                discount_types: ['OPDSERVICES', 'IPDSERVICES'],
                show_table: false,
                show_form: false,
                editMode: false
            }
        },
        methods: {
            openDiscount: function (data) {
                this.discounts = []
                this.client = data
                this.getDiscount();
                this.show_table = true;
                this.show_form = false;
                this.editMode = false;
            },
            getDiscount: function () {
                HTTP.get('/corporateClients/'+this.client.id+'/discounts')
                    .then((response)=>{
                        this.discounts = response.data.discounts;
                    })
            },
            deleteRow: function (data) {
                if (!confirm('Are you sure want to remove?')) return;
                data._method = 'DELETE';
                this.$inertia.post('/corporateClients/' + data.id, data)
            },
            deleteDiscount: function (data) {
                if (!confirm('Are you sure want to remove?')) return;
                data._method = 'DELETE';
                this.$inertia.post('/corporateClients/'+data.corporate_client_id+'/discounts/'+data.id, data)
                this.openDiscount(this.client)
            },
            newDiscount: function () {
                this.show_table = false;
                this.show_form = true;
                this.form = {
                    hospital_id: '',
                    service_id: '',
                    service_package_id: '',
                    discount_category: '',
                    status: 'ACTIVE',
                };
                this.getHospitalList();
            },
            getHospitalList: function () {
                HTTP.get('/hospitals/list')
                    .then((response)=>{
                        this.hospitals = response.data;
                    })
            },
            backToDiscountList: function () {
                this.show_table = true;
                this.show_form = false
            },
            editDiscount: function (data) {
                this.form = data
                this.getHospitalList();
                this.getServicePackage();
                this.show_table = false;
                this.show_form = true;
                this.editMode = true;
            },
            updateDiscount: function (data) {
                HTTP.put('/corporateClients/'+this.client.id+'/discounts/'+ data.id, data)
                    .then((response)=>{
                        success_message((response.data.message))
                        this.getDiscount();
                        this.show_table = true;
                        this.show_form = false;
                    }).catch(function (error) {
                    error_message(error.response.data.errors)
                })
            },
            saveDiscount: function (data) {
                HTTP.post('/corporateClients/'+this.client.id+'/discounts', data)
                    .then((response)=>{
                        success_message((response.data.message))
                        this.getDiscount();
                        this.show_table = true;
                        this.show_form = false;
                    }).catch(function (error) {
                    error_message(error.response.data.errors)
                })
            },
            getServicePackage: function () {
                    this.packages = []
                    this.services = []

                HTTP.get('/discounts/service_packages_list/'+this.form.discount_category)
                    .then((response)=>{
                        this.packages = response.data.packages;
                        this.services = response.data.services;
                    })
                }
        }
    }
</script>
