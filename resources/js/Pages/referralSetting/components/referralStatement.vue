<template>
    <div class="patient-list">
        <slot name="header"></slot>

        <div class="card-box">
            <form id="filter-form">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label> Referred Person</label>
                            <Select2
                                v-model="query.person_id"
                                placeholder="Select Person"
                                :options="param.doctors"  />
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
                    <table class="table table-border table-striped custom-table datatable mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Person Name</th>
                            <th>Total Referral Amount</th>
                            <th>Total Payment Amount</th>
                            <th>Total Due Amount</th>
                            <th class="text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in param.data" :key="row.id">
                            <td>{{ index+1 }}</td>
                            <td>{{ row.person ? row.person.name : null }}</td>
                            <td>{{ row.total_referral_amount }}</td>
                            <td>{{ row.payment_amount }}</td>
                            <td>{{ row.total_referral_amount - row.payment_amount }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#makePayment"  @click="getSummary(row)"><i class="fa fa-money m-r-5"></i> Make Payment</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#paymentHistory"  @click="getPaymentHistory(row)"><i class="fa fa-print m-r-5"></i> Payment History</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#referralHistory"  @click="getReferralHistory(row)"><i class="fa fa-print m-r-5"></i> Referral History</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>



            <!-- Modal -->
            <div class="modal fade bd-example-modal" id="makePayment" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <MakePayment :data="summery" />
            </div>

            <!-- Modal -->
            <div class="modal fade  bd-example-modal-lg" id="paymentHistory" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <PaymentHistory :data="history" />
            </div>

            <!-- Modal -->
            <div class="modal fade  bd-example-modal-lg" id="referralHistory" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <ReferralHistory :data="referral_history" />
            </div>


        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import {HTTP} from "@/utils/http-common";
import Select2 from 'v-select2-component';
import { Datetime } from 'vue-datetime';
import MakePayment from './MakePayment';
import PaymentHistory from './PaymentHistory';
import ReferralHistory from './ReferralHistory';

export default {
    components: {
        JetResponsiveNavLink, Select2, datetime: Datetime,
        MakePayment, PaymentHistory, ReferralHistory
    },
    data() {
        return {
            summery: {},
            history: {},
            referral_history: {},
            query: {
                search_type: '',
                search_text: ''
            }
        }
    },
    props: ['param'],
    mounted() {
        if (!_.isEmpty(this.param.query)) {
            this.query = this.param.query
        }
    },
    methods: {
        resetQuery: function () {
            this.query = null
        },
        resetData: function () {
            this.resetQuery()
            this.$inertia.get('/referralStatement', this.query)
        },
        getData: function () {
            this.$inertia.get('/referralStatement', this.query)
        },
        getSummary: function (row) {
            HTTP.get('/referral/'+row.person_id+'/referralSummery')
                .then((response)=>{
                    this.summery = response.data;
                })
        },
        getPaymentHistory: function (row) {
            HTTP.get('/referral/'+row.person_id+'/paymentHistory')
                .then((response)=>{
                    this.history = response.data;
                })
        },
        getReferralHistory: function (row) {
            HTTP.get('/referral/'+row.person_id)
                .then((response)=>{
                    this.referral_history = response.data;
                })
        }
    }
}
</script>
