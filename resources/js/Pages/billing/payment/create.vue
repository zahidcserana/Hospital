<template>
    <app-layout>
        <Model :data="data">
            <template #header>
                <Alert
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                />
            </template>
        </Model>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Model from './components/Model'
import Alert from '@/Pages/Component/Alert'
import Select2 from 'v-select2-component';

export default {
    components: {
        AppLayout,
        Model,
        Alert,
        Select2
    },
    props: ['data', 'errors', 'flash'],
    data () {
        return {
            title: 'New Payment',
            link: 'payments.index',
            label: 'Payment List'
        }
    }
}
</script>
