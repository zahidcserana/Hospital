<template>
    <div class="hospital-create">
        <slot name="header"></slot>

        <div class="row card-box">
            <div class="col-lg-12">
                <form>
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Product</label>
                                <select class="form-control select"  v-model="form.product_id">
                                    <option value="" selected>Select Product</option>
                                    <option  v-for="row in data.products" :key="row.id" :value="row.id">{{ row.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Product Quantity</label>
                                <input class="form-control" type="number" v-model="form.product_qty"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="display: block;"><span> Product For</span>
                                    <div class="float-right">
                                        <input @click="isNonPatient()" type="checkbox" class="form-checkbox" >
                                        <span class="ml-2 text-sm text-gray-600">Non Patient</span>
                                    </div>
                                </label>
                                <input v-if="nonPatient" class="form-control" type="text" v-model="form.product_for"/>

                                <Select2  v-if="!nonPatient"
                                        v-model="form.patient_id"
                                        placeholder="Select Patient"
                                        :options="data.patients"  />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Room</label>
                                <select class="form-control select"  v-model="form.consumption_room_id">
                                    <option value="" selected>Select Room</option>
                                    <option  v-for="row in data.rooms" :key="row.id" :value="row.id">{{ row.room_number }}</option>
                                </select>
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
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Alert from '@/Pages/Component/Alert'
import Select2 from 'v-select2-component';

export default {
    components: {
        Alert,
        Select2
    },
        props: ['data', 'errors', 'editMode'],
        data() {
            return {
                isOpen: false,
                nonPatient: false,
                form: {
                    id: undefined,
                    product_id: '',
                    patient_id: '',
                    consumption_room_id: ''
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
            isNonPatient: function () {
                this.nonPatient = !this.nonPatient
            },
            reset: function () {
                this.form = {}
            },
            save: function (data) {
                this.$inertia.post('/consumptions', data)
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },
            update: function (data) {
                data._method = 'PUT';
                this.$inertia.post('/consumptions/' + data.id, data)
            }
        }
    }
</script>

<style>
.select2.select2-container.select2-container--default {
    width: 100%!important;
}
</style>