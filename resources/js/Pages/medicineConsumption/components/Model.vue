<template>
    <div class="hospital-create">
        <slot name="header"></slot>

        <div class="row card-box">
            <div class="col-lg-12">
                <form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="typo__label" for="ajax">Medicine</label>
                                <multiselect v-model="form.medicine_id" id="ajax" label="name" track-by="name" placeholder="Type to search" open-direction="bottom" :options="products" :custom-label="productLabel" :searchable="true" :loading="isLoading" @search-change="searchMedicine"></multiselect>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Product Quantity</label>
                                <input class="form-control" type="number" v-model="form.quantity"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Patient</label>
                                <Select2
                                    v-model="form.patient_id"
                                    placeholder="Select Patient"
                                    :options="data.patients"
                                />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea
                                    v-model="form.remarks"
                                    cols="30"
                                    rows="4"
                                    class="form-control"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="m-t-20 text-center">
                        <button
                            type="button"
                            class="btn btn-primary submit-btn"
                            wire:click.prevent="store()"
                            v-show="!editMode"
                            @click="save(form)"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary submit-btn"
                            wire:click.prevent="store()"
                            v-show="editMode"
                            @click="update(form)"
                        >
                            Update
                        </button>
                        <button v-if="data.data.length" type="button" @click="printElement()" class="btn btn-warning submit-btn float-right">Print</button>
                    </div>
                </form>
            </div>
        </div>
         <div v-if="data.data.length" class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-border table-striped custom-table datatable mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Consumption Qty</th>
                                <th>Consumption For</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in data.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.medicine.name }}</td>
                                <td>{{ row.quantity }}</td>
                                <td>{{ row.product_for }}</td>
                                <td>{{ row.remarks }}</td>
                                <td>{{ row.date | myDateFormat }}</td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
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
        <div id="requisition-print">
            <InvoicePrint id="printThis" style="width: 250%" :item="data.data" />
        </div>
    </div>
</template>

<script>
import Alert from '@/Pages/Component/Alert'
import Select2 from 'v-select2-component'
import {HTTP} from '@/utils/http-common'
import InvoicePrint from './InvoicePrint'

export default {
    components: {
        Alert,
        Select2,
        InvoicePrint
    },
    props: ['data', 'errors', 'editMode'],
    data() {
        return {
            products: [],
            search: { term: ''},
            isLoading: false,
            isOpen: false,
            nonPatient: false,
            form: {
                id: undefined,
                medicine_id: '',
                patient_id: '',
            },
        }
    },
    created() {
        if (this.editMode) {
            this.form = this.data
            this.nonPatient = true
        }
    },
    methods: {
        printElement: function () {
            let elem = document.getElementById("printThis")
            var domClone = elem.cloneNode(true);

            var $printSection = document.getElementById("printSection");

            if (!$printSection) {
                var $printSection = document.createElement("div");
                $printSection.id = "printSection";
                document.body.appendChild($printSection);
            }

            $printSection.innerHTML = "";
            $printSection.appendChild(domClone);
            window.print();
        },
        productLabel ({ name, strength }) {
            return `${name} — (${strength})`
        },
        searchMedicine (query) {
            this.isLoading = true
            this.search.term = query
            HTTP.post('/searchMedicine', this.search).then(response => {
                this.products = response.data
                this.isLoading = false
            })
        },
        reset: function () {
            this.form = {}
        },
        save: function (data) {
            data.medicine_id = data.medicine_id.id
            this.$inertia.post('/medicine-consumption', data)
            this.reset()
        },
        edit: function (data) {
            this.form = Object.assign({}, data);
            this.editMode = true;
            this.openModal();
        },
        update: function (data) {
            data._method = 'PUT';
            this.$inertia.post('/medicine-consumption/' + data.id, data)
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/medicine-consumption/' + data.id, data)
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
#requisition-print {
    display: none;
}
.select2.select2-container.select2-container--default {
    width: 100%!important;
}
</style>
