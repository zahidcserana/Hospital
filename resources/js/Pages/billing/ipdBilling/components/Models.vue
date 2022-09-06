<template>
     <div class="hospital-list">
         <slot name="header"></slot>

             <div class="card-box">
                 <form id="filter-form">

                     <div class="row">
                         <div class="col-md-2">
                             <div class="form-group">
                                 <label> Search By</label>
                                 <select class="form-control select" v-model="query.search_type" >
                                     <option  v-for="row in filter_types" :key="row.key_value" :value="row.key_value">{{ row.key_name }}</option>
                                 </select>
                             </div>
                         </div>

                         <div class="col-md-3">
                             <div class="form-group">
                                 <label> Search Text</label>
                                 <input type="text"   v-model="query.search_text"  class="form-control" placeholder="Type search text...">
                             </div>
                         </div>
                         <div class="col-md-2">
                             <div class="form-group">
                                 <label> Start Date</label>
                                 <datetime  v-model="query.from_date" type="date" input-class="form-control" format="yyyy-MM-dd"></datetime>
                             </div>
                         </div>

                         <div class="col-md-2">
                             <div class="form-group">
                                 <label> End Date</label>
                                 <datetime   v-model="query.to_date"  type="date" input-class="form-control" format="yyyy-MM-dd"></datetime>
                             </div>
                         </div>
                         <div class="col-sm-2 col-md-1">
                             <label for="">&nbsp;</label>
                             <button type="button" @click="getData()" class="btn btn-success btn-small"> Search </button>
                         </div>
                         <div class="col-sm-2 col-md-1">
                             <label for="">&nbsp;</label>
                             <button type="button" @click="resetData()" class="btn btn-warning btn-small"> Reset </button>
                         </div>
                     </div>
                 </form>
            </div>
        <div class="row">
            <div class="col-md-12">
                <p>
                    <b>Total Bill Amount: </b> {{ data.total_bill_amount }} &nbsp;&nbsp;&nbsp; <b>Total Paid Amount: </b> {{ data.total_paid_amount }} &nbsp;&nbsp;&nbsp; <b>Total Due Amount: </b> {{ data.total_due_amount }}
                </p>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-border table-striped custom-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient </th>
                                <th>Doctor </th>
                                <th>Patient Code</th>
                                <th>Bill No.</th>
                                <th>Bill Date </th>
                                <th>Total Bill Amount</th>
                                <th>Refund</th>
                                <th>Due Amount</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in data.data.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.patient_admission.patient.name }}</td>
                                <td>{{ row.patient_admission.doctor.name }}</td>
                                <td>{{ row.patient_admission.bill_no }}</td>
                                <td>{{ row.bill_no }}</td>
                                <td>{{ row.created_at | formatDate }}</td>
                                <td>{{ row.total_bill_amount }}</td>
                                <td>
                                    <span v-if="row.refund_amount>0"> {{ row.refund_amount }} |
                                    <i v-if="row.refund_status == 0" class="fa fa-clock-o"></i>
                                    <i v-if="row.refund_status == 1" class="fa fa-check"></i>
                                    <i v-if="row.refund_status == 2" class="fa fa-ban"></i>
                                </span></td>
                                <td>{{ row.total_due_amount }}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'COMPLETE' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">

                                             <jet-responsive-nav-link :href="route('ipdBillings.edit', [row.id])"  class="dropdown-item" v-if="row.status != 'COMPLETE' && ($page.auth.user.role == 'BM' || $page.isAdmin )">
                                                <i class="fa fa-pencil m-r-5"></i>Edit
                                            </jet-responsive-nav-link>

                                             <jet-responsive-nav-link :href="route('add_refund', [row.id])"  class="dropdown-item" v-if="row.status == 'COMPLETE' && row.refund_status == 0 && ($page.auth.user.role == 'BM' || $page.auth.user.role == 'BE' || $page.isAdmin )">
                                                <i class="fa fa-pencil m-r-5"></i>Refund
                                            </jet-responsive-nav-link>

                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#receipt"  @click="invoice(row)"><i class="fa fa-print m-r-5"></i> Invoice</a>

<!--                                            <a v-if="$page.isAdmin" class="dropdown-item" href="#" data-toggle="modal" @click="deleteRow(row)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>-->
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <br/>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"  v-for="row in data.data.links" :key="row.label"  v-bind:class="row.active ? 'active' : ''"><a class="page-link" :href="row.url">{{row.label}}</a></li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!--        invoice modal section start-->

            <!-- Modal -->
            <div class="modal fade  bd-example-modal-lg" id="receipt" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                            <invoice :bill="bill" />
            </div>

            <!--         invoice modal section end-->


        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Invoice from './Invoice'
import {HTTP} from '@/utils/http-common'
import { Datetime } from 'vue-datetime';

 export default {
     components: {
            JetResponsiveNavLink, Invoice,
         datetime: Datetime
        },
        props: ['data', 'errors'],
        data() {
            return {
                bill: {},
                filter_types: [
                    {'key_value': '', 'key_name': 'All'},
                    {'key_value': 'bill_no', 'key_name': 'Bill No'},
                    {'key_value': 'patient_name', 'key_name': 'Patient Name'},
                    {'key_value': 'patient_mobile', 'key_name': 'Patient Mobile'},
                    {'key_value': 'patient_code', 'key_name': 'Patient Code'},
                    {'key_value': 'patient_id', 'key_name': 'Patient ID'},
                    {'key_value': 'doctor_name', 'key_name': 'Doctor Name'},
                    {'key_value': 'doctor_id', 'key_name': 'Doctor ID'},
                ],
                query: {
                    search_type: '',
                    search_text: '',
                    from_date: undefined,
                    to_date: undefined
                }
            }
        },
     mounted() {
         if (!_.isEmpty(this.data.query)) {
             this.query = this.data.query
         }
     },
     methods: {
         resetQuery: function () {
             this.query = null
         },
         resetData: function () {
             this.resetQuery()
                 this.$inertia.get('/ipdBillings', this.query)
         },
         getData: function () {
                 this.$inertia.get('/ipdBillings', this.query)
         },
            deleteRow: function (data) {
                if (!confirm('Are you sure want to remove?')) return;
                data._method = 'DELETE';
                this.$inertia.post('/ipdBillings/' + data.id, data)
            },
            invoice: function (bill) {
                HTTP.get('/ipdBillings/'+bill.id+'/receipt')
                    .then((response)=>{
                        this.bill = response.data;
                    })
            }
        }
    }
</script>
