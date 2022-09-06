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
                <div class="table-responsive">
                    <p><b>Total Collection Amount:</b> {{data.total_payment}}</p>
                    <table class="table table-border table-striped custom-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient Name/Code </th>
                                <th>Doctor </th>
                                <th>Billing No.</th>
                                <th>Billing Type</th>
                                <th>Payment Amount</th>
                                <th>Payment Method</th>
                                <th>Payment Date</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in data.payments.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.patient.name }}/ {{row.bill.bill_no}}</td>
                                <td>{{ row.doctor.name }}</td>
                                <td>{{ row.bill.bill_no }}</td>
                                <td>{{ row.billing_type }}</td>
                                <td>{{ row.payment_amount }}</td>
                                <td>{{ row.payment_method }}</td>
                                <td>{{ row.payment_date }}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'FULL' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                             <jet-responsive-nav-link :href="route('payments.edit', [row.id])" :active="route().current('payments.create')" class="dropdown-item" v-if="$page.auth.user.role == 'BM' || $page.isAdmin">
                                                <i class="fa fa-pencil m-r-5"></i>Edit
                                            </jet-responsive-nav-link>

                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#receipt"   @click="invoice(row)"><i class="fa fa-print m-r-5"></i> Receipt</a>

                                            <a v-if="$page.isAdmin" class="dropdown-item" href="#" data-toggle="modal" @click="deleteRow(row)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br/>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"  v-for="row in data.payments.links" :key="row.label"  v-bind:class="row.active ? 'active' : ''"><a class="page-link" :href="row.url">{{row.label}}</a></li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!--        invoice modal section start-->

            <!-- Modal -->
            <div class="modal fade  bd-example-modal-lg" id="receipt" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                <invoice :invoice="invoice_data" />
            </div>

            <!--         invoice modal section end-->

        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Invoice from './Invoice'
import {HTTP} from "@/utils/http-common";
import { Datetime } from 'vue-datetime';
 export default {
     components: {
            JetResponsiveNavLink, Invoice, datetime: Datetime
        },
        props: ['data', 'errors'],
        data() {
            return {
                invoice_data: {},
                filter_types: [
                    {'key_value': '', 'key_name': 'All'},
                    {'key_value': 'bill_no', 'key_name': 'Bill No'},
                    {'key_value': 'bill_type', 'key_name': 'Bill Type'},
                    {'key_value': 'patient_name', 'key_name': 'Patient Name'},
                    {'key_value': 'patient_code', 'key_name': 'Patient Code'},
                    {'key_value': 'doctor_name', 'key_name': 'Doctor Name'},
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
                this.$inertia.get('/payments', this.query)
            },
            getData: function () {
                this.$inertia.get('/payments', this.query)
            },
            deleteRow: function (data) {
                if (!confirm('Are you sure want to remove?')) return;
                data._method = 'DELETE';
                this.$inertia.post('/payments/' + data.id, data)
            },
            invoice: function (row) {
                HTTP.get('/payments/'+row.id+'/receipt')
                    .then((response)=>{
                        this.invoice_data = response.data;
                    })
            }
        }
    }
</script>
