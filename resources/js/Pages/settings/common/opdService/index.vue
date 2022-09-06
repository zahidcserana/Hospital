<template>
    <app-layout>
        <Models :data="data">
            <template #header>
                <Alert
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                />
            </template>
        </Models>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Models from './components/Models'
import Alert from '@/Pages/Component/Alert'

export default {
    components: {
        AppLayout,
        Models,
        Alert
    },
    props: ['data', 'errors', 'flash'],
    data () {
        return {
            title: 'Opd Service List',
            link: 'opdServices.create',
            label: 'Create Opd Service'
        }
    }
}
</script>
