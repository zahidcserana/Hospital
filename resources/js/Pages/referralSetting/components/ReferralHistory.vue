<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title pull-left" id="exampleModalLongTitle"><button type="button" @click="printElement()" class="btn btn-primary">Print</button></h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" v-if="data.person">
                <div class="wrapper " >
                    <section class="invoice"  id="printSection" style="width: 1000px">
                        <!-- title row -->
                        <div class="row border-dark border-bottom pb-2">
                            <div class="col-12">
                                <h3 class="page-header">
                                <span class="float-left">
<!--                                     <img src="assets/img/logo.jpg" style="height: 45px;">-->
                                    <img src="/assets/img/logo.jpg"   style="height: 45px;">
                                    <span style="vertical-align: -webkit-baseline-middle">AFC HEALTH LTD.</span>
                                </span>
                                    <!--                                    <small class="float-right">Print Time: {{ date }}</small>-->
                                </h3>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- info row -->
                        <div style="display: block!important;text-align: center;" class="row mt-1">
                            <p style="font-size: medium;">
                                <strong>AFC Health Fortis Heart Institute</strong>,
                                Cumilla
                            </p>
                            <p>
                                <h3>Referral History</h3>
                                <p>Person Name: <strong>{{data.person.name}}</strong></p>
                                <p>Person Email: <strong>{{data.person.email}}</strong></p>
                            </p>
                        </div>

                        <!-- Table row -->
                        <div class="row mt-4 border-dark border-bottom">
                            <div class="col-12 table-responsive">
                                <table id="product_table" class="table table-striped">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Sl.</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Bill No.</th>
                                        <th scope="col">Bill Type</th>
                                        <th scope="col">Total Bill Amount</th>
                                        <th scope="col">Referral Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(row, index) in data.histories">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ row.created_at | formatDateTime }}</td>
                                        <td>{{row.bill.bill_no}}</td>
                                        <td>{{row.bill_type}}</td>
                                        <td>{{row.bill.total_bill_amount}} tk</td>
                                        <td>{{row.referral_amount}} tk</td>
                                    </tr>
                                    <tr>
                                        <td  colspan="4"></td>
                                        <td>Total Referral Amount:</td>
                                        <td>{{ data.referral_amount }} tk</td>
                                    </tr>
                                    <tr>
                                        <td  colspan="4"></td>
                                        <td>Total Paid Amount:</td>
                                        <td>{{ data.payment_amount }} tk</td>
                                    </tr>
                                    <tr>
                                        <td  colspan="4"></td>
                                        <td>Total Due Amount:</td>
                                        <td>{{ data.referral_amount - data.payment_amount }} tk</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- /.row -->
                    </section>
                    <!-- /.content -->
                </div>
                <!-- ./wrapper -->
                <div id="printSections"></div>
            </div>
        </div>
    </div>

</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import moment from 'moment'

export default {
    components: {
        JetResponsiveNavLink,
    },
    props: ['data'],
    data() {
        return {

        }
    },
    methods: {
        printElement: function () {
            let elem = document.getElementById("printSection")
            var domClone = elem.cloneNode(true);
            //
            var $printSection = document.getElementById("printSections");
            //
            if (!$printSection) {
                var $printSection = document.createElement("div");
                $printSection.id = "printSection";
                document.body.appendChild($printSection);
            }

            $printSection.innerHTML = "";
            $printSection.appendChild(domClone);
            window.print();
        }
    }
}
</script>
<style>

.modal-dialog {
    max-width: 1050px;
    /*margin: 1.75rem auto;*/
}
@media print {
    body * {
        visibility:hidden;
    }
    #printSection, #printSection * {
        visibility:visible;
    }
    #printSection {
        position:absolute;
        left:0;
        top:0;
    }
}
</style>
